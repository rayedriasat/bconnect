<?php

/**
 * Helper functions for appointment reminders
 */

/**
 * Create a reminder for an appointment
 * 
 * @param PDO $conn Database connection
 * @param int $appointment_id Appointment ID
 * @param string $method Reminder method ('sms' or 'email')
 * @return bool Success status
 */
function createReminder($conn, $appointment_id, $method = 'email') {
    try {
        $stmt = $conn->prepare("
            INSERT INTO Reminder (appointment_id, method)
            VALUES (?, ?)
        ");
        return $stmt->execute([$appointment_id, $method]);
    } catch (Exception $e) {
        error_log("Error creating reminder: " . $e->getMessage());
        return false;
    }
}

/**
 * Send reminders for upcoming appointments
 * 
 * @param PDO $conn Database connection
 * @return int Number of reminders sent
 */
function sendUpcomingAppointmentReminders($conn) {
    try {
        // Find appointments happening in the next 24 hours
        $stmt = $conn->prepare("
            SELECT 
                da.appointment_id, 
                da.scheduled_time,
                d.user_id as donor_user_id,
                h.name as hospital_name
            FROM DonationAppointment da
            JOIN DonationRequest dr ON da.request_id = dr.request_id
            JOIN Hospital h ON dr.hospital_id = h.hospital_id
            JOIN Donor d ON da.donor_id = d.donor_id
            WHERE da.status IN ('pending', 'confirmed')
            AND da.scheduled_time BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 24 HOUR)
        ");
        $stmt->execute();
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($appointments)) {
            return 0;
        }
        
        $reminder_count = 0;
        foreach ($appointments as $appointment) {
            $formatted_time = date('F j, Y \a\t g:i A', strtotime($appointment['scheduled_time']));
            $message = "Reminder: You have a blood donation appointment scheduled for tomorrow at {$formatted_time} at {$appointment['hospital_name']}.";
            
            // Send email reminder
            if (sendEmailNotification($conn, $appointment['donor_user_id'], $message)) {
                createReminder($conn, $appointment['appointment_id'], 'email');
                $reminder_count++;
            }
        }
        
        return $reminder_count;
    } catch (Exception $e) {
        error_log("Error sending reminders: " . $e->getMessage());
        return 0;
    }
}