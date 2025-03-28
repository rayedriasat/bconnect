<?php
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

echo "<h1>BloodConnect Data Fix Script</h1>";
echo "<p>Converting confirmed appointments to completed and adding donation history...</p>";

try {
    // Create database connection
    $db = new Database();
    $conn = $db->connect();
    $conn->beginTransaction();

    // 1. Convert confirmed appointments to completed
    echo "<h3>Converting confirmed appointments to completed...</h3>";

    $stmt = $conn->prepare("
        UPDATE DonationAppointment 
        SET status = 'completed', 
            scheduled_time = DATE_SUB(NOW(), INTERVAL RAND()*10 DAY) 
        WHERE status = 'confirmed'
    ");
    $stmt->execute();
    $convertedCount = $stmt->rowCount();

    echo "Converted $convertedCount confirmed appointments to completed<br>";

    // 2. Move completed appointments to donation history
    echo "<h3>Moving completed appointments to donation history...</h3>";

    // Select all completed appointments
    $stmt = $conn->prepare("
        SELECT da.*, dr.* 
        FROM DonationAppointment da
        JOIN DonationRequest dr ON da.request_id = dr.request_id
        WHERE da.status = 'completed'
    ");
    $stmt->execute();
    $completedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($completedAppointments as $appointment) {
        // Check if this request is already in history
        $stmt = $conn->prepare("
            SELECT COUNT(*) FROM DonationRequestHistory 
            WHERE request_id = ?
        ");
        $stmt->execute([$appointment['request_id']]);
        $exists = $stmt->fetchColumn();

        if (!$exists) {
            // Move to history
            $stmt = $conn->prepare("
                INSERT INTO DonationRequestHistory
                (request_id, hospital_id, requester_id, blood_type, quantity, urgency, 
                 contact_person, contact_phone, created_at, fulfilled_at, fulfilled_by, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'fulfilled')
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
                $appointment['donor_id']
            ]);

            echo "Moved request #{$appointment['request_id']} to donation history<br>";

            // Create fulfillment notification
            $stmt = $conn->prepare("
                SELECT u.user_id FROM Users u
                JOIN Donor d ON u.user_id = d.user_id
                WHERE d.donor_id = ?
            ");
            $stmt->execute([$appointment['donor_id']]);
            $donorUserId = $stmt->fetch(PDO::FETCH_COLUMN);

            if ($donorUserId) {
                $fulfillmentMessage = "Thank you for your blood donation! Your contribution has helped save lives.";
                sendInAppNotification($conn, $donorUserId, $fulfillmentMessage);
                echo "Created fulfillment notification for donor user #$donorUserId<br>";
            }
        } else {
            echo "Request #{$appointment['request_id']} already in history, skipping<br>";
        }
    }

    // 3. Create reminders for upcoming appointments
    echo "<h3>Creating reminders for appointments...</h3>";

    // Get all pending and confirmed appointments
    $stmt = $conn->prepare("
        SELECT appointment_id, scheduled_time 
        FROM DonationAppointment 
        WHERE status IN ('pending', 'confirmed')
    ");
    $stmt->execute();
    $upcomingAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $methods = ['sms', 'email'];
    $reminderCount = 0;

    foreach ($upcomingAppointments as $appointment) {
        // Create 1-3 reminders per appointment
        $numReminders = rand(1, 3);

        for ($i = 0; $i < $numReminders; $i++) {
            $method = $methods[array_rand($methods)];

            // Set sent_at to a random time before the appointment
            $appointmentTime = strtotime($appointment['scheduled_time']);
            $reminderTime = date('Y-m-d H:i:s', $appointmentTime - rand(3600, 86400 * 7)); // Between 1 hour and 7 days before

            $stmt = $conn->prepare("
                INSERT INTO Reminder (appointment_id, method, sent_at)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$appointment['appointment_id'], $method, $reminderTime]);
            $reminderCount++;
        }
    }

    echo "Created $reminderCount reminders for upcoming appointments<br>";

    // Commit all changes
    $conn->commit();

    echo "<h2>Data fix complete!</h2>";
    echo "<p>Successfully converted appointments and created donation history records.</p>";
} catch (Exception $e) {
    // Roll back transaction on error
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
