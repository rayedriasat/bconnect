<?php
require_once '../includes/auth_middleware.php';
require_once '../Core/functs.php';

// Get notifications for the current user - only show in-app notifications
$stmt = $conn->prepare("
    SELECT * FROM Notification 
    WHERE user_id = ? AND type = 'in-app'
    ORDER BY sent_at DESC
");
$stmt->execute([$user['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get flash messages
$success = getFlashMessage('success');
$error = getFlashMessage('error');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/navigation.php'; ?>

    <div class="max-w-6xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Your Notifications</h1>
                <?php if (count($notifications) > 0): ?>
                    <div class="flex space-x-4">
                        <form method="POST" action="<?php echo BASE_URL; ?>/actions/mark_notifications_read.php">
                            <button type="submit" class="text-blue-600 hover:text-blue-800">
                                Mark all as read
                            </button>
                        </form>
                        <form method="POST" action="<?php echo BASE_URL; ?>/actions/delete_all_notifications.php"
                            onsubmit="return confirm('Are you sure you want to delete all notifications?');">
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                Delete all
                            </button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <?php require_once '../includes/_alerts.php'; ?>

            <?php if (count($notifications) > 0): ?>
                <div class="space-y-4">
                    <?php foreach ($notifications as $notification): ?>
                        <div class="p-4 border rounded-lg <?php echo $notification['is_read'] ? 'bg-gray-50' : 'bg-blue-50 border-blue-200'; ?>">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-3">
                                    <?php if ($notification['type'] === 'email'): ?>
                                        <i class="fas fa-envelope text-blue-500"></i>
                                    <?php elseif ($notification['type'] === 'sms'): ?>
                                        <i class="fas fa-sms text-green-500"></i>
                                    <?php else: ?>
                                        <i class="fas fa-bell text-yellow-500"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="flex-1">
                                    <p class="text-gray-800"><?php echo htmlspecialchars($notification['message']); ?></p>
                                    <p class="text-sm text-gray-500 mt-1">
                                        <?php echo date('M j, Y g:i A', strtotime($notification['sent_at'])); ?>
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <?php if (!$notification['is_read']): ?>
                                        <form method="POST" action="<?php echo BASE_URL; ?>/actions/mark_notification_read.php">
                                            <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                            <button type="submit" class="text-blue-500 hover:text-blue-700" title="Mark as read">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>/actions/delete_notification.php">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700" title="Delete notification">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8">
                    <i class="fas fa-bell-slash text-gray-400 text-5xl mb-4"></i>
                    <p class="text-gray-500">You don't have any notifications yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>