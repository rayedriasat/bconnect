<?php
require_once 'includes/auth_middleware.php';

// Handle availability toggle
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_availability']) && $isDonor) {
    try {
        $stmt = $conn->prepare("UPDATE Donor SET is_available = NOT is_available WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);

        // Refresh the page to show updated status
        header('Location: ' . BASE_URL . '/dashboard.php');
        exit();
    } catch (Exception $e) {
        $error = 'Failed to update availability status';
    }
}

// Get donor details (including availability status)
$donorDetails = null;
if ($isDonor) {
    $stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $donorDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get active donation requests
$stmt = $conn->prepare("
    SELECT dr.*, h.name as hospital_name, h.address as hospital_address 
    FROM DonationRequest dr 
    JOIN Hospital h ON dr.hospital_id = h.hospital_id 
    ORDER BY dr.urgency DESC, dr.created_at DESC 
    LIMIT 5
");
$stmt->execute();
$activeRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get user's notifications
$stmt = $conn->prepare("
    SELECT * FROM Notification 
    WHERE user_id = ? 
    ORDER BY sent_at DESC 
    LIMIT 5
");
$stmt->execute([$user['user_id']]);
$notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once 'includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Profile Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">My Profile</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Email:</span>
                        <span class="font-medium"><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Phone:</span>
                        <span class="font-medium"><?php echo htmlspecialchars($user['phone_number']); ?></span>
                    </div>

                    <?php if ($isDonor): ?>
                        <?php
                        // Fetch donor details
                        $stmt = $conn->prepare("SELECT blood_type, date_of_birth, weight, is_available FROM Donor WHERE user_id = ?");
                        $stmt->execute([$user['user_id']]);
                        $donorDetails = $stmt->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Blood Type:</span>
                            <span class="font-medium text-red-600"><?php echo htmlspecialchars($donorDetails['blood_type']); ?></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Age:</span>
                            <span class="font-medium">
                                <?php
                                $dob = new DateTime($donorDetails['date_of_birth']);
                                $today = new DateTime();
                                echo $today->diff($dob)->y . ' years';
                                ?>
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Weight:</span>
                            <span class="font-medium"><?php echo htmlspecialchars($donorDetails['weight']); ?> kg</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600">Status:</span>
                            <span class="font-medium <?php echo $donorDetails['is_available'] ? 'text-green-600' : 'text-red-600'; ?>">
                                <?php echo $donorDetails['is_available'] ? 'Available' : 'Not Available'; ?>
                            </span>
                        </div>
                        <div class="text-green-600 mt-2">✓ Registered Donor</div>
                    <?php else: ?>
                        <div class="mt-4">
                            <a href="<?php echo BASE_URL; ?>/become-donor.php"
                                class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                Become a Donor
                            </a>
                        </div>
                    <?php endif; ?>

                    <?php if ($isAdmin): ?>
                        <div class="text-blue-600">⚡ Administrator</div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Active Requests -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Active Donation Requests</h2>
                <?php if (empty($activeRequests)): ?>
                    <p class="text-gray-500">No active requests at the moment.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($activeRequests as $request): ?>
                            <div class="border-b pb-2">
                                <div class="font-medium"><?php echo htmlspecialchars($request['hospital_name']); ?></div>
                                <div class="text-sm text-gray-600">
                                    <?php echo htmlspecialchars($request['hospital_address']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Notifications -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Notifications</h2>
                <?php if (empty($notifications)): ?>
                    <p class="text-gray-500">No new notifications.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($notifications as $notification): ?>
                            <div class="border-b pb-2">
                                <div class="text-sm">
                                    <?php echo htmlspecialchars($notification['message']); ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <?php echo date('M j, Y H:i', strtotime($notification['sent_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <?php if ($isAdmin): ?>
                <!-- Admin Quick Actions -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Admin Actions</h2>
                    <div class="space-y-2">
                        <a href="<?php echo BASE_URL; ?>/views/admin/manage-hospitals.php"
                            class="block text-blue-600 hover:text-blue-800">Manage Hospitals</a>
                        <a href="<?php echo BASE_URL; ?>/views/admin/manage-admins.php"
                            class="block text-blue-600 hover:text-blue-800">Manage Administrators</a>
                        <a href="<?php echo BASE_URL; ?>/views/admin/manage-users.php"
                            class="block text-blue-600 hover:text-blue-800">Manage Users</a>
                        <a href="<?php echo BASE_URL; ?>/views/admin/reports.php"
                            class="block text-blue-600 hover:text-blue-800">View Reports</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($isDonor): ?>
                <!-- Donor Information -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold mb-4">Donor Information</h2>
                    <div class="space-y-2">
                        <a href="<?php echo BASE_URL; ?>/appointments.php"
                            class="block text-blue-600 hover:text-blue-800">My Appointments</a>
                        <a href="<?php echo BASE_URL; ?>/donation-history.php"
                            class="block text-blue-600 hover:text-blue-800">Donation History</a>
                        <form method="POST" class="inline">
                            <input type="hidden" name="toggle_availability" value="1">
                            <button type="submit" class="text-blue-600 hover:text-blue-800">
                                <?php echo $donorDetails['is_available'] ? 'Set as Unavailable' : 'Set as Available'; ?>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Messages/Inbox -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">My Inbox</h2>
                <?php
                // Fetch messages
                $stmt = $conn->prepare("
                    SELECT m.*, 
                           CASE 
                               WHEN m.sender_id = ? THEN 'You'
                               ELSE u.email
                           END as sender_name
                    FROM Message m
                    LEFT JOIN Users u ON m.sender_id = u.user_id
                    WHERE m.receiver_id = ?
                    ORDER BY m.sent_at DESC
                    LIMIT 5
                ");
                $stmt->execute([$user['user_id'], $user['user_id']]);
                $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (empty($messages)): ?>
                    <p class="text-gray-500">No messages in your inbox.</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($messages as $message): ?>
                            <div class="border-b pb-3">
                                <div class="flex justify-between items-start">
                                    <span class="font-medium"><?php echo htmlspecialchars($message['sender_name']); ?></span>
                                    <span class="text-xs text-gray-500">
                                        <?php echo date('M j, Y H:i', strtotime($message['sent_at'])); ?>
                                    </span>
                                </div>
                                <p class="text-gray-600 mt-1"><?php echo htmlspecialchars($message['content']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-4">
                        <a href="<?php echo BASE_URL; ?>/messages.php"
                            class="text-blue-600 hover:text-blue-800">View all messages →</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>