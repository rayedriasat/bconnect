<?php

/**
 * Helper functions for sending notifications
 */

/**
 * Send an in-app notification to a user
 * 
 * @param PDO $conn Database connection
 * @param int $user_id User ID to send notification to
 * @param string $message Notification message
 * @return bool Success status
 */
function sendInAppNotification($conn, $user_id, $message)
{
    try {
        $stmt = $conn->prepare("
            INSERT INTO Notification (user_id, message, type)
            VALUES (?, ?, 'in-app')
        ");
        return $stmt->execute([$user_id, $message]);
    } catch (Exception $e) {
        error_log('Error sending notification: ' . $e->getMessage());
        return false;
    }
}

/**
 * Send an email notification to a user
 * 
 * @param PDO $conn Database connection
 * @param int $user_id User ID to send notification to
 * @param string $message Notification message
 * @return bool Success status
 */
function sendEmailNotification($conn, $user_id, $message)
{
    try {
        // First, record the notification in the database
        $stmt = $conn->prepare("
            INSERT INTO Notification (user_id, message, type)
            VALUES (?, ?, 'email')
        ");
        $result = $stmt->execute([$user_id, $message]);

        // Get user email and name
        $stmt = $conn->prepare("SELECT email, name FROM Users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Send actual email using PHPMailer
            require_once __DIR__ . '/../phpmailer_src/PHPMailer.php';
            require_once __DIR__ . '/../phpmailer_src/SMTP.php';
            require_once __DIR__ . '/../phpmailer_src/Exception.php';
            require_once __DIR__ . '/../config/mail_config.php';

            $mail = new PHPMailer\PHPMailer\PHPMailer(true);

            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = MAIL_HOST;
                $mail->SMTPAuth = true;
                $mail->Username = MAIL_USERNAME;
                $mail->Password = MAIL_PASSWORD;
                $mail->SMTPSecure = MAIL_ENCRYPTION;
                $mail->Port = MAIL_PORT;

                // Recipients
                $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
                $mail->addAddress($user['email'], $user['name']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'BloodConnect Notification';
                $mail->Body = '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">' .
                    '<h2 style="color: #e53e3e;">BloodConnect</h2>' .
                    '<p>' . htmlspecialchars($message) . '</p>' .
                    '<p style="margin-top: 20px; font-size: 12px; color: #666;">This is an automated message from BloodConnect. Please do not reply to this email.</p>' .
                    '</div>';
                $mail->AltBody = strip_tags($message);

                $mail->send();
                error_log('Email sent to ' . $user['email']);
            } catch (Exception $e) {
                error_log('Email could not be sent. Mailer Error: ' . $mail->ErrorInfo);
            }
        }

        return $result;
    } catch (Exception $e) {
        error_log('Error sending email notification: ' . $e->getMessage());
        return false;
    }
}

/**
 * Send an SMS notification to a user
 * 
 * @param PDO $conn Database connection
 * @param int $user_id User ID to send notification to
 * @param string $message Notification message
 * @return bool Success status
 */
function sendSmsNotification($conn, $user_id, $message)
{
    try {
        // First, record the notification in the database
        $stmt = $conn->prepare("
            INSERT INTO Notification (user_id, message, type)
            VALUES (?, ?, 'sms')
        ");
        $result = $stmt->execute([$user_id, $message]);

        // Get user phone number
        $stmt = $conn->prepare("SELECT phone_number FROM Users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // In a real application, you would send an actual SMS here
            // For now, we'll just log it
            error_log('SMS notification to ' . $user['phone_number'] . ': ' . $message);
        }

        return $result;
    } catch (Exception $e) {
        error_log('Error sending SMS notification: ' . $e->getMessage());
        return false;
    }
}

/**
 * Notify matching donors about a new donation request
 * 
 * @param PDO $conn Database connection
 * @param int $request_id The donation request ID
 * @return int Number of donors notified
 */
function notifyMatchingDonors($conn, $request_id)
{
    try {
        // Get request details
        $stmt = $conn->prepare("
            SELECT dr.*, h.name as hospital_name, u.user_id as requester_user_id
            FROM DonationRequest dr
            JOIN Hospital h ON dr.hospital_id = h.hospital_id
            JOIN Users u ON dr.requester_id = u.user_id
            WHERE dr.request_id = ?
        ");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            return 0;
        }

        // Find matching donors from the Matches table
        $stmt = $conn->prepare("
            SELECT m.donor_id, m.score, d.user_id 
            FROM Matches m
            JOIN Donor d ON m.donor_id = d.donor_id
            WHERE m.request_id = ?
            AND d.user_id != ?
            ORDER BY m.score DESC
        ");
        $stmt->execute([$request_id, $request['requester_user_id']]);
        $matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($matches)) {
            return 0;
        }

        $urgency_text = ucfirst($request['urgency']);
        $message = "URGENT: {$urgency_text} need for {$request['blood_type']} blood type at {$request['hospital_name']}. Please check the donation requests page if you can help.";

        $notified_count = 0;
        foreach ($matches as $match) {
            // Send in-app notification
            sendInAppNotification($conn, $match['user_id'], $message);

            // Send email notification
            sendEmailNotification($conn, $match['user_id'], $message);

            $notified_count++;
        }

        return $notified_count;
    } catch (Exception $e) {
        error_log("Error in notifyMatchingDonors: " . $e->getMessage());
        return 0;
    }
}

/**
 * Notify requester about a new appointment
 * 
 * @param PDO $conn Database connection
 * @param int $appointment_id The appointment ID
 * @return bool Success status
 */
function notifyRequesterAboutAppointment($conn, $appointment_id)
{
    try {
        // Get appointment details with donor and requester info
        $stmt = $conn->prepare("
            SELECT 
                da.*, 
                dr.requester_id,
                dr.blood_type,
                d.blood_type as donor_blood_type,
                u_donor.name as donor_name,
                h.name as hospital_name
            FROM DonationAppointment da
            JOIN DonationRequest dr ON da.request_id = dr.request_id
            JOIN Donor d ON da.donor_id = d.donor_id
            JOIN Users u_donor ON d.user_id = u_donor.user_id
            JOIN Hospital h ON dr.hospital_id = h.hospital_id
            WHERE da.appointment_id = ?
        ");
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            return false;
        }

        $message = "Good news! {$appointment['donor_name']} with blood type {$appointment['donor_blood_type']} has scheduled a donation appointment for your {$appointment['blood_type']} blood request at {$appointment['hospital_name']} on " . date('F j, Y \a\t g:i A', strtotime($appointment['scheduled_time']));

        // Send in-app notification
        sendInAppNotification($conn, $appointment['requester_id'], $message);

        // Send email notification
        sendEmailNotification($conn, $appointment['requester_id'], $message);

        return true;
    } catch (Exception $e) {
        error_log('Error notifying requester about appointment: ' . $e->getMessage());
        return false;
    }
}

/**
 * Notify about appointment status change
 * 
 * @param PDO $conn Database connection
 * @param int $appointment_id The appointment ID
 * @param string $new_status The new status
 * @return bool Success status
 */
function notifyAppointmentStatusChange($conn, $appointment_id, $new_status)
{
    try {
        // Get appointment details with donor and requester info
        $stmt = $conn->prepare("
            SELECT 
                da.*, 
                dr.requester_id,
                dr.blood_type,
                d.user_id as donor_user_id,
                d.blood_type as donor_blood_type,
                u_donor.name as donor_name,
                u_requester.name as requester_name,
                h.name as hospital_name
            FROM DonationAppointment da
            JOIN DonationRequest dr ON da.request_id = dr.request_id
            JOIN Donor d ON da.donor_id = d.donor_id
            JOIN Users u_donor ON d.user_id = u_donor.user_id
            JOIN Users u_requester ON dr.requester_id = u_requester.user_id
            JOIN Hospital h ON dr.hospital_id = h.hospital_id
            WHERE da.appointment_id = ?
        ");
        $stmt->execute([$appointment_id]);
        $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$appointment) {
            return false;
        }

        $status_text = ucfirst($new_status);
        $date_formatted = date('F j, Y \a\t g:i A', strtotime($appointment['scheduled_time']));

        // Notify requester
        $requester_message = "Appointment status update: The donation appointment with {$appointment['donor_name']} for {$appointment['blood_type']} blood at {$appointment['hospital_name']} on {$date_formatted} has been {$status_text}.";
        sendInAppNotification($conn, $appointment['requester_id'], $requester_message);
        sendEmailNotification($conn, $appointment['requester_id'], $requester_message);

        // Notify donor
        $donor_message = "Appointment status update: Your donation appointment for {$appointment['blood_type']} blood at {$appointment['hospital_name']} on {$date_formatted} has been {$status_text}.";
        sendInAppNotification($conn, $appointment['donor_user_id'], $donor_message);
        sendEmailNotification($conn, $appointment['donor_user_id'], $donor_message);

        return true;
    } catch (Exception $e) {
        error_log('Error notifying about appointment status change: ' . $e->getMessage());
        return false;
    }
}
