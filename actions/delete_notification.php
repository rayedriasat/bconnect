<?php
require_once '../includes/auth_middleware.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notification_id'])) {
    $notification_id = $_POST['notification_id'];
    
    // Verify the notification belongs to the current user
    $stmt = $conn->prepare("
        SELECT user_id FROM Notification 
        WHERE notification_id = ?
    ");
    $stmt->execute([$notification_id]);
    $notification = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($notification && $notification['user_id'] == $user_id) {
        // Delete the notification
        $stmt = $conn->prepare("
            DELETE FROM Notification 
            WHERE notification_id = ?
        ");
        $stmt->execute([$notification_id]);
    }
}

// Redirect back to notifications page
header('Location: ' . BASE_URL . '/views/notifications.php');
exit();