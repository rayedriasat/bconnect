<?php
require_once '../../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    try {
        $conn->beginTransaction();

        // Get request details and verify ownership
        $stmt = $conn->prepare("
            SELECT * FROM DonationRequest 
            WHERE request_id = ? AND requester_id = ?
        ");
        $stmt->execute([$_POST['request_id'], $user['user_id']]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            throw new Exception("Request not found or unauthorized");
        }

        // Insert into history
        $stmt = $conn->prepare("
            INSERT INTO DonationRequestHistory (
                request_id, hospital_id, requester_id, blood_type, 
                quantity, urgency, contact_person, contact_phone,
                created_at, fulfilled_at, fulfilled_by, status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NULL, 'cancelled'
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
            $request['created_at']
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

        $conn->commit();

        // Redirect back with success message
        header('Location: ' . BASE_URL . '/views/requests/index.php?success=1&message=cancelled');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        error_log("Error in cancel-request.php: " . $e->getMessage());
        header('Location: ' . BASE_URL . '/views/requests/index.php?error=1&message=cancel_failed');
        exit();
    }
}

// If we get here, something went wrong
header('Location: ' . BASE_URL . '/views/requests/index.php');
exit();
