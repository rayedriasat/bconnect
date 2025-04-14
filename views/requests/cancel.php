<?php
require_once '../../includes/auth_middleware.php';
require_once '../../includes/notification_helper.php';
require_once '../../Core/functs.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_id'])) {
    $request_id = $_POST['request_id'];

    try {
        // First verify that this request belongs to the current user
        $stmt = $conn->prepare("
            SELECT * FROM DonationRequest 
            WHERE request_id = ? AND requester_id = ?
        ");
        $stmt->execute([$request_id, $user['user_id']]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            $_SESSION['error_message'] = 'Request not found or access denied';
            header('Location: ' . BASE_URL . '/views/requests/index.php');
            exit();
        }

        // Get hospital name for notification
        $stmt = $conn->prepare("
            SELECT h.name FROM Hospital h
            JOIN DonationRequest dr ON h.hospital_id = dr.hospital_id
            WHERE dr.request_id = ?
        ");
        $stmt->execute([$request_id]);
        $hospital = $stmt->fetch(PDO::FETCH_ASSOC);

        // Get all donors who have appointments for this request
        $stmt = $conn->prepare("
            SELECT d.user_id 
            FROM DonationAppointment da
            JOIN Donor d ON da.donor_id = d.donor_id
            WHERE da.request_id = ?
        ");
        $stmt->execute([$request_id]);
        $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Move the request to history
        $stmt = $conn->prepare("
            INSERT INTO DonationRequestHistory
            (request_id, hospital_id, requester_id, blood_type, quantity, urgency, 
             contact_person, contact_phone, created_at, fulfilled_at, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 'cancelled')
        ");
        $stmt->execute([
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

        // Delete the request
        $stmt = $conn->prepare("DELETE FROM DonationRequest WHERE request_id = ?");
        $stmt->execute([$request_id]);

        // Notify all donors who had appointments
        foreach ($donors as $donor) {
            $message = "A donation request for {$request['blood_type']} blood at {$hospital['name']} has been cancelled(Other donor fullfilled it). Any scheduled appointments for this request are no longer needed.";
            sendInAppNotification($conn, $donor['user_id'], $message);
            sendEmailNotification($conn, $donor['user_id'], $message);
        }

        $_SESSION['success_message'] = 'Donation request cancelled successfully';
        header('Location: ' . BASE_URL . '/views/requests/index.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Failed to cancel request: ' . $e->getMessage();
        header('Location: ' . BASE_URL . '/views/requests/index.php');
        exit();
    }
} else {
    header('Location: ' . BASE_URL . '/views/requests/index.php');
    exit();
}
