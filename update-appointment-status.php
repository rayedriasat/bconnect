<?php
require_once 'includes/auth_middleware.php';

// Add debugging
error_log("POST data received: " . print_r($_POST, true));

// Redirect if not a donor
if (!$isDonor) {
    error_log("Not a donor - redirecting");
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

// Get donor details first
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donor) {
    error_log("Donor not found");
    header('Location: ' . BASE_URL . '/appointments.php?error=1&message=invalid_donor');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['appointment_id']) || !isset($_POST['status']) || empty($_POST['status'])) {
    error_log("Invalid request - Missing required fields");
    error_log("Method: " . $_SERVER['REQUEST_METHOD']);
    error_log("Appointment ID: " . ($_POST['appointment_id'] ?? 'not set'));
    error_log("Status: " . ($_POST['status'] ?? 'not set'));
    header('Location: ' . BASE_URL . '/appointments.php?error=1&message=invalid_request');
    exit();
}

$allowed_statuses = ['pending', 'confirmed', 'completed', 'cancelled'];
if (!in_array($_POST['status'], $allowed_statuses)) {
    error_log("Invalid status: " . $_POST['status']);
    header('Location: ' . BASE_URL . '/appointments.php?error=1&message=invalid_status');
    exit();
}

try {
    $conn->beginTransaction();
    error_log("Starting transaction");

    // First verify the appointment exists and belongs to this donor
    $stmt = $conn->prepare("
        SELECT da.*, dr.*, dr.requester_id 
        FROM DonationAppointment da
        JOIN DonationRequest dr ON da.request_id = dr.request_id
        WHERE da.appointment_id = ? AND da.donor_id = ?
    ");
    $stmt->execute([$_POST['appointment_id'], $donor['donor_id']]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        error_log("Appointment not found or doesn't belong to donor");
        throw new Exception("Invalid appointment");
    }

    error_log("Current appointment status: " . $appointment['status']);
    error_log("Requested status change to: " . $_POST['status']);

    // Check if appointment can be updated
    if (in_array($appointment['status'], ['cancelled', 'completed'])) {
        error_log("Cannot update appointment - current status: " . $appointment['status']);
        throw new Exception("Cannot update appointment in " . $appointment['status'] . " status");
    }

    // If status is being set to completed
    if ($_POST['status'] === 'completed') {
        // First, update this appointment to completed
        $stmt = $conn->prepare("
            UPDATE DonationAppointment 
            SET status = 'completed', 
                updated_at = CURRENT_TIMESTAMP
            WHERE appointment_id = ? AND donor_id = ?
        ");
        $stmt->execute([$_POST['appointment_id'], $donor['donor_id']]);

        // Second, delete other appointments for this request
        $stmt = $conn->prepare("
            DELETE FROM DonationAppointment 
            WHERE request_id = ? AND appointment_id != ?
        ");
        $stmt->execute([$appointment['request_id'], $_POST['appointment_id']]);

        // Third, move to donation history
        $stmt = $conn->prepare("
            INSERT INTO DonationRequestHistory (
                request_id, 
                hospital_id, 
                requester_id, 
                blood_type, 
                quantity, 
                urgency, 
                contact_person, 
                contact_phone,
                created_at, 
                fulfilled_at, 
                fulfilled_by, 
                status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'fulfilled'
            )
        ");

        $stmt->execute([
            $appointment['request_id'],
            $appointment['hospital_id'],
            $appointment['requester_id'],
            $appointment['blood_type'],
            $appointment['quantity'],
            $appointment['urgency'],
            $appointment['contact_person'],
            $appointment['contact_phone'],
            $appointment['created_at'],
            $donor['donor_id']
        ]);

        // Finally, delete the donation request
        $stmt = $conn->prepare("DELETE FROM DonationRequest WHERE request_id = ?");
        $stmt->execute([$appointment['request_id']]);

        $message = "The donor has completed the blood donation. The donation request has been fulfilled.";
    } else {
        // Update appointment status
        $stmt = $conn->prepare("
            UPDATE DonationAppointment 
            SET status = ?, 
                updated_at = CURRENT_TIMESTAMP
            WHERE appointment_id = ? AND donor_id = ?
        ");

        $updateResult = $stmt->execute([
            $_POST['status'],
            $_POST['appointment_id'],
            $donor['donor_id']
        ]);

        if (!$updateResult) {
            throw new Exception("Failed to update appointment status");
        }

        $message = match ($_POST['status']) {
            'confirmed' => "The donor has confirmed the appointment.",
            'cancelled' => "The donor has cancelled the appointment.",
            'pending' => "The appointment status has been set to pending.",
            default => "The appointment status has been updated."
        };
    }

    // Send notification
    $stmt = $conn->prepare("
        INSERT INTO Message (sender_id, receiver_id, content) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $user['user_id'],
        $appointment['requester_id'],
        $message
    ]);

    $conn->commit();
    header('Location: ' . BASE_URL . '/appointments.php?success=1&message=status_updated');
    exit();
} catch (Exception $e) {
    $conn->rollBack();
    error_log("Error in update-appointment-status.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    header('Location: ' . BASE_URL . '/appointments.php?error=1&message=' . urlencode($e->getMessage()));
    exit();
}
