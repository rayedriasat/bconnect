<?php
require_once '../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Mark all notifications as read for the current user
    $stmt = $conn->prepare("
        UPDATE Notification 
        SET is_read = 1 
        WHERE user_id = ?
    ");
    $stmt->execute([$user_id]);
}

// Redirect back to notifications page
header('Location: ' . BASE_URL . '/views/notifications.php');
exit();