<?php
require_once '../includes/auth_middleware.php';

// Get notifications for the current user
$stmt = $conn->prepare("
    SELECT * FROM Notification 
    WHERE user_id = ? 
    ORDER BY sent_at DESC
");
$stmt->execute([$user['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
                    <form method="POST" action="../actions/mark_notifications_read.php">
                        <button type="submit" class="text-blue-600 hover:text-blue-800">
                            Mark all as read
                        </button>
                    </form>
                <?php endif; ?>
            </div>

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
                                <div>
                                    <form method="POST" action="../actions/delete_notification.php">
                                        <input type="hidden" name="notification_id" value="<?php echo $notification['notification_id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
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