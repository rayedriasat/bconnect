<?php
require_once '../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("
            DELETE FROM Notification 
            WHERE user_id = ?
        ");
        $stmt->execute([$user['user_id']]);
        
        $_SESSION['success_message'] = 'All notifications deleted successfully';
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Failed to delete notifications: ' . $e->getMessage();
    }
}

header('Location: ' . BASE_URL . '/views/notifications.php');
exit();