<?php
class Location {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
    }
    
    /**
     * Save a user's location
     * 
     * @param int $user_id User ID
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param string $address Full address
     * @param string $location_name Optional name for this location
     * @return bool Success status
     */
    public function saveUserLocation($user_id, $latitude, $longitude, $address, $location_name = 'Home') {
        $stmt = $this->conn->prepare("
            INSERT INTO Location (
                user_id,
                latitude,
                longitude,
                address,
                location_name
            ) VALUES (?, ?, ?, ?, ?)
        ");
        
        return $stmt->execute([
            $user_id,
            $latitude,
            $longitude,
            $address,
            $location_name
        ]);
    }
    
    /**
     * Get a user's most recent location (if available)
     * 
     * @param int $user_id User ID
     * @return array|null Location data or null if not found
     */
    public function getUserLocation($user_id) {
        $stmt = $this->conn->prepare("
            SELECT * FROM Location
            WHERE user_id = ?
            ORDER BY location_id DESC
            LIMIT 1
        ");
        
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find nearby users based on coordinates and distance
     * 
     * @param float $latitude Center latitude
     * @param float $longitude Center longitude
     * @param float $radius_km Radius in kilometers
     * @return array Array of nearby users with their locations
     */
    public function findNearbyUsers($latitude, $longitude, $radius_km = 10) {
        // Haversine formula to calculate distances
        $stmt = $this->conn->prepare("
            SELECT 
                l.*,
                u.name,
                u.email,
                (
                    6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(l.latitude)) * 
                        cos(radians(l.longitude) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(l.latitude))
                    )
                ) AS distance
            FROM Location l
            JOIN Users u ON l.user_id = u.user_id
            HAVING distance < ?
            ORDER BY distance
        ");
        
        $stmt->execute([$latitude, $longitude, $latitude, $radius_km]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Find nearby hospitals based on coordinates and distance
     * 
     * @param float $latitude Center latitude
     * @param float $longitude Center longitude
     * @param float $radius_km Radius in kilometers
     * @return array Array of nearby hospitals with their locations
     */
    public function findNearbyHospitals($latitude, $longitude, $radius_km = 10) {
        $stmt = $this->conn->prepare("
            SELECT 
                l.*,
                h.name as hospital_name,
                h.address as hospital_address,
                (
                    6371 * acos(
                        cos(radians(?)) * 
                        cos(radians(l.latitude)) * 
                        cos(radians(l.longitude) - radians(?)) + 
                        sin(radians(?)) * 
                        sin(radians(l.latitude))
                    )
                ) AS distance
            FROM Location l
            JOIN Hospital h ON l.hospital_id = h.hospital_id
            HAVING distance < ?
            ORDER BY distance
        ");
        
        $stmt->execute([$latitude, $longitude, $latitude, $radius_km]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Update a user's location
     * 
     * @param int $user_id User ID
     * @param float $latitude Latitude
     * @param float $longitude Longitude
     * @param string $address Full address
     * @return bool Success status
     */
    public function updateUserLocation($user_id, $latitude, $longitude, $address) {
        // Insert a new record instead of updating to keep location history
        return $this->saveUserLocation($user_id, $latitude, $longitude, $address);
    }
    
    /**
     * Calculate distance between two points using Haversine formula
     * 
     * @param float $lat1 First point latitude
     * @param float $lon1 First point longitude
     * @param float $lat2 Second point latitude
     * @param float $lon2 Second point longitude
     * @return float Distance in kilometers
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        // Haversine formula to calculate distance between two points
        $earthRadius = 6371; // in kilometers
        
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        
        $a = sin($dLat/2) * sin($dLat/2) + 
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * 
             sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        
        return $earthRadius * $c;
    }
}