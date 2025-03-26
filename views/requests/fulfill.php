<?php
require_once '../../includes/auth_middleware.php';

// Redirect if not a donor
if (!$isDonor) {
    header('Location: ' . BASE_URL . '/views/dashboard/index.php');
    exit();
}

// Get donor details first
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donor) {
    header('Location: ' . BASE_URL . '/views/requests/index.php?error=1');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    try {
        $conn->beginTransaction();

        // Get request details
        $stmt = $conn->prepare("
            SELECT dr.*, u.user_id as requester_user_id 
            FROM DonationRequest dr
            JOIN Users u ON dr.requester_id = u.user_id 
            WHERE dr.request_id = ?
        ");
        $stmt->execute([$_POST['request_id']]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            // Insert into history
            $stmt = $conn->prepare("
                INSERT INTO DonationRequestHistory (
                    request_id, hospital_id, requester_id, blood_type, 
                    quantity, urgency, contact_person, contact_phone,
                    created_at, fulfilled_at, fulfilled_by, status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'fulfilled'
                )
            ");

            $result = $stmt->execute([
                $request['request_id'],
                $request['hospital_id'],
                $request['requester_id'],
                $request['blood_type'],
                $request['quantity'],
                $request['urgency'],
                $request['contact_person'],
                $request['contact_phone'],
                $request['created_at'],
                $donor['donor_id']
            ]);

            if (!$result) {
                throw new Exception("Failed to insert into history");
            }

            // Delete from active requests
            $stmt = $conn->prepare("DELETE FROM DonationRequest WHERE request_id = ?");
            $deleteResult = $stmt->execute([$_POST['request_id']]);

            if (!$deleteResult) {
                throw new Exception("Failed to delete request");
            }

            // Send confirmation message
            $stmt = $conn->prepare("INSERT INTO Message (sender_id, receiver_id, content) VALUES (?, ?, ?)");
            $messageResult = $stmt->execute([
                $user['user_id'],
                $request['requester_user_id'],
                "I have fulfilled this blood donation request."
            ]);

            if (!$messageResult) {
                throw new Exception("Failed to send message");
            }

            $conn->commit();

            // Redirect back with success message
            header('Location: ' . BASE_URL . '/views/requests/index.php?success=1');
            exit();
        } else {
            throw new Exception("Request not found");
        }
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error in fulfill-request.php: " . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/requests/index.php?error=1');
        exit();
    }
}

// If we get here, something went wrong
header('Location: ' . BASE_URL . '/views/requests/index.php');
exit();
