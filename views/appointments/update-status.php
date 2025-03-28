<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';
require_once '../../includes/notification_helper.php';

// Redirect if not a donor
if (!$isDonor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id']) && isset($_POST['status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['status'];

    try {
        $conn->beginTransaction();

        // First, verify this appointment belongs to the current donor
        $stmt = $conn->prepare("
            SELECT da.*, dr.request_id, dr.requester_id, d.donor_id 
            FROM DonationAppointment da
            JOIN DonationRequest dr ON da.request_id = dr.request_id
            JOIN Donor d ON da.donor_id = d.donor_id
            WHERE da.appointment_id = ? AND d.user_id = ?
        ");
        $stmt->execute([$appointment_id, $user['user_id']]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            throw new Exception("Appointment not found or you don't have permission to update it");
        }

        // Update the appointment status
        $stmt = $conn->prepare("
            UPDATE DonationAppointment 
            SET status = ?, updated_at = NOW()
            WHERE appointment_id = ?
        ");
        $stmt->execute([$new_status, $appointment_id]);

        // Notify about the status change for this appointment
        if ($new_status !== 'completed') notifyAppointmentStatusChange($conn, $appointment_id, $new_status);

        // If the new status is "completed", fulfill the donation request
        if ($new_status === 'completed') {
            // Get request details for history
            $stmt = $conn->prepare("
                SELECT * FROM DonationRequest WHERE request_id = ?
            ");
            $stmt->execute([$appointment['request_id']]);
            $request = $stmt->fetch(PDO::FETCH_ASSOC);

            // Move the request to history
            $stmt = $conn->prepare("
                INSERT INTO DonationRequestHistory
                (request_id, hospital_id, requester_id, blood_type, quantity, urgency, 
                 contact_person, contact_phone, created_at, fulfilled_at, fulfilled_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'fulfilled')
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
                $request['created_at'],
                $appointment['donor_id']
            ]);

            // Cancel all other pending appointments for this request
            $stmt = $conn->prepare("
                SELECT da.appointment_id
                FROM DonationAppointment da
                WHERE da.request_id = ? AND da.appointment_id != ? AND da.status IN ('pending', 'confirmed')
            ");
            $stmt->execute([$appointment['request_id'], $appointment_id]);
            $otherAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Update status and notify for each cancelled appointment
            foreach ($otherAppointments as $otherAppointment) {
                $stmt = $conn->prepare("
                    UPDATE DonationAppointment
                    SET status = 'cancelled', updated_at = NOW()
                    WHERE appointment_id = ?
                ");
                $stmt->execute([$otherAppointment['appointment_id']]);

                // Use the notification function for each cancelled appointment
                notifyAppointmentStatusChange($conn, $otherAppointment['appointment_id'], 'cancelled');
            }

            // Send additional notification to requester about request fulfillment
            $stmt = $conn->prepare("
                SELECT u.email, u.name
                FROM Users u
                WHERE u.user_id = ?
            ");
            $stmt->execute([$request['requester_id']]);
            $requester = $stmt->fetch(PDO::FETCH_ASSOC);

            $fulfillmentMessage = "Good news! Your blood donation request has been completely fulfilled. Thank you for using our platform.";
            sendInAppNotification($conn, $request['requester_id'], $fulfillmentMessage);
            sendEmailNotification($conn, $request['requester_id'], $fulfillmentMessage);

            // Delete the original request
            $stmt = $conn->prepare("DELETE FROM DonationRequest WHERE request_id = ?");
            $stmt->execute([$appointment['request_id']]);

            $_SESSION['success_message'] = 'Donation completed and request fulfilled successfully!';
        } else {
            $_SESSION['success_message'] = 'Appointment status updated successfully!';
        }

        $conn->commit();
        header('Location: ' . BASE_URL . '/views/appointments/index.php');
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = 'Failed to update appointment status: ' . $e->getMessage();
        header('Location: ' . BASE_URL . '/views/appointments/index.php');
        exit();
    }
} else {
    header('Location: ' . BASE_URL . '/views/appointments/index.php');
    exit();
}
