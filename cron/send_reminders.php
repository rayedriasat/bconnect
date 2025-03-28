<?php
// Script to send reminders for upcoming appointments
// This should be scheduled to run daily via cron job or Windows Task Scheduler

// Set error reporting
ini_set('display_errors', 0);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/notification_helper.php';
require_once __DIR__ . '/../includes/reminder_helper.php';

// Log file for tracking
$log_file = __DIR__ . '/reminder_log.txt';
$log_message = date('Y-m-d H:i:s') . " - Starting reminder process\n";
file_put_contents($log_file, $log_message, FILE_APPEND);

try {
    // Connect to database
    $conn = require_once __DIR__ . '/../includes/db_connect.php';

    // Send reminders
    $reminder_count = sendUpcomingAppointmentReminders($conn);

    // Log results
    $log_message = date('Y-m-d H:i:s') . " - Sent {$reminder_count} reminders\n";
    file_put_contents($log_file, $log_message, FILE_APPEND);

    echo "Successfully sent {$reminder_count} reminders.";
} catch (Exception $e) {
    $error_message = date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n";
    file_put_contents($log_file, $error_message, FILE_APPEND);
    echo "Error: " . $e->getMessage();
}
