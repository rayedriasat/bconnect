<?php
require_once '../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("
            UPDATE Notification 
            SET is_read = 1 
            WHERE user_id = ?
        ");
        $stmt->execute([$user['user_id']]);
        
        $_SESSION['success_message'] = 'All notifications marked as read';
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Failed to mark notifications as read: ' . $e->getMessage();
    }
}

header('Location: ' . BASE_URL . '/views/notifications.php');
exit();