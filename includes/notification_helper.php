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

        // Get user email
        $stmt = $conn->prepare("SELECT email FROM Users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // In a real application, you would send an actual email here
            // For now, we'll just log it
            error_log('Email notification to ' . $user['email'] . ': ' . $message);
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
