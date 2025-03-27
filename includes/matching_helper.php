<?php

/**
 * Helper functions for donor-request matching
 */

/**
 * Calculate match score between a donor and a request
 * 
 * @param PDO $conn Database connection
 * @param int $donor_id Donor ID
 * @param int $request_id Request ID
 * @return float Match score
 */
function calculateMatchScore($conn, $donor_id, $request_id) {
    // Get donor details
    $stmt = $conn->prepare("
        SELECT d.*, u.user_id, l.latitude as donor_lat, l.longitude as donor_long
        FROM Donor d
        JOIN Users u ON d.user_id = u.user_id
        LEFT JOIN Location l ON u.user_id = l.user_id
        WHERE d.donor_id = ?
    ");
    $stmt->execute([$donor_id]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Get request details
    $stmt = $conn->prepare("
        SELECT dr.*, h.hospital_id, l.latitude as hospital_lat, l.longitude as hospital_long
        FROM DonationRequest dr
        JOIN Hospital h ON dr.hospital_id = h.hospital_id
        LEFT JOIN Location l ON h.hospital_id = l.hospital_id
        WHERE dr.request_id = ?
    ");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$donor || !$request) {
        return 0;
    }
    
    // Base score - blood type match is essential
    if ($donor['blood_type'] != $request['blood_type']) {
        return 0;
    }
    
    // Start with a base score of 50
    $score = 50;
    
    // Add points for availability
    if ($donor['is_available']) {
        $score += 20;
    }
    
    // Add points for urgency match
    if ($request['urgency'] == 'high') {
        $score += 15;
    }
    
    // Add points for location proximity if available
    if (isset($donor['donor_lat']) && isset($donor['donor_long']) && 
        isset($request['hospital_lat']) && isset($request['hospital_long'])) {
        
        // Calculate distance (simplified)
        $distance = sqrt(
            pow($donor['donor_lat'] - $request['hospital_lat'], 2) + 
            pow($donor['donor_long'] - $request['hospital_long'], 2)
        );
        
        // Closer donors get higher scores (max 15 points)
        $proximity_score = max(0, 15 - ($distance * 10));
        $score += $proximity_score;
    }
    
    return min(100, $score); // Cap at 100
}

/**
 * Generate matches for a donation request
 * 
 * @param PDO $conn Database connection
 * @param int $request_id Request ID
 * @return int Number of matches generated
 */
function generateMatches($conn, $request_id) {
    try {
        // Get request details
        $stmt = $conn->prepare("SELECT blood_type FROM DonationRequest WHERE request_id = ?");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$request) {
            return 0;
        }
        
        // Find potential donors with matching blood type
        $stmt = $conn->prepare("
            SELECT donor_id 
            FROM Donor 
            WHERE blood_type = ? AND is_available = 1
        ");
        $stmt->execute([$request['blood_type']]);
        $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($donors)) {
            return 0;
        }
        
        // Clear existing matches for this request
        $stmt = $conn->prepare("DELETE FROM Matches WHERE request_id = ?");
        $stmt->execute([$request_id]);
        
        // Calculate and store match scores
        $match_count = 0;
        foreach ($donors as $donor) {
            $score = calculateMatchScore($conn, $donor['donor_id'], $request_id);
            
            // Only store significant matches (score > 50)
            if ($score > 50) {
                $stmt = $conn->prepare("
                    INSERT INTO Matches (request_id, donor_id, score)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$request_id, $donor['donor_id'], $score]);
                $match_count++;
            }
        }
        
        return $match_count;
    } catch (Exception $e) {
        error_log("Error generating matches: " . $e->getMessage());
        return 0;
    }
}