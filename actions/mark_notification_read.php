<?php
require_once '../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];
    
    try {
        // Verify the notification belongs to the current user
        $stmt = $conn->prepare("
            SELECT * FROM Notification 
            WHERE notification_id = ? AND user_id = ?
        ");
        $stmt->execute([$notification_id, $user['user_id']]);
        $notification = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($notification) {
            $stmt = $conn->prepare("
                UPDATE Notification 
                SET is_read = 1 
                WHERE notification_id = ?
            ");
            $stmt->execute([$notification_id]);
            
            $_SESSION['success_message'] = 'Notification marked as read';
        } else {
            $_SESSION['error_message'] = 'Notification not found or access denied';
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Failed to mark notification as read: ' . $e->getMessage();
    }
}

header('Location: ' . BASE_URL . '/views/notifications.php');
exit();