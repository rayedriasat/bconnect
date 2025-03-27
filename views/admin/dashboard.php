<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';

// Redirect if not an admin
if (!$isAdmin) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$error = getFlashMessage('error');
$success = getFlashMessage('success');

// Get quick statistics
try {
    // Total number of donors
    $stmt = $conn->prepare("SELECT COUNT(*) as donor_count FROM Donor");
    $stmt->execute();
    $donorCount = $stmt->fetch(PDO::FETCH_ASSOC)['donor_count'];

    // Total number of hospitals
    $stmt = $conn->prepare("SELECT COUNT(*) as hospital_count FROM Hospital");
    $stmt->execute();
    $hospitalCount = $stmt->fetch(PDO::FETCH_ASSOC)['hospital_count'];

    // Active donation requests
    $stmt = $conn->prepare("SELECT COUNT(*) as request_count FROM DonationRequest");
    $stmt->execute();
    $requestCount = $stmt->fetch(PDO::FETCH_ASSOC)['request_count'];

    // Total donations made
    $stmt = $conn->prepare("
        SELECT COUNT(*) as donation_count 
        FROM DonationRequestHistory 
        WHERE status = 'fulfilled'
    ");
    $stmt->execute();
    $donationCount = $stmt->fetch(PDO::FETCH_ASSOC)['donation_count'];

    // Recent donation requests
    $stmt = $conn->prepare("
        SELECT 
            dr.*,
            h.name as hospital_name,
            u.email as requester_email
        FROM DonationRequest dr
        JOIN Hospital h ON dr.hospital_id = h.hospital_id
        JOIN Users u ON dr.requester_id = u.user_id
        ORDER BY dr.created_at DESC
        LIMIT 5
    ");
    $stmt->execute();
    $recentRequests = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $_SESSION['error_message'] = 'Error fetching statistics: ' . $e->getMessage();
    header('Location: ' . BASE_URL . '/views/admin/dashboard.php');
    exit();
}

$pageTitle = 'Admin Dashboard - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>
    <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-red-600"><?php echo $donorCount; ?></div>
                <div class="text-gray-600">Registered Donors</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-blue-600"><?php echo $hospitalCount; ?></div>
                <div class="text-gray-600">Partner Hospitals</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-yellow-600"><?php echo $requestCount; ?></div>
                <div class="text-gray-600">Active Requests</div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="text-2xl font-bold text-green-600"><?php echo $donationCount; ?></div>
                <div class="text-gray-600">Total Donations</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="<?php echo BASE_URL; ?>/views/admin/manage-hospitals.php"
                        class="bg-blue-100 text-blue-700 p-4 rounded-lg hover:bg-blue-200">
                        <div class="font-semibold">Manage Hospitals</div>
                        <div class="text-sm">Add or remove hospital partners</div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/admin/manage-inventory.php"
                        class="bg-orange-100 text-orange-700 p-4 rounded-lg hover:bg-orange-200">
                        <div class="font-semibold">Manage Inventory</div>
                        <div class="text-sm">Manage blood inventory levels</div>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/views/admin/manage-admins.php"
                        class="bg-purple-100 text-purple-700 p-4 rounded-lg hover:bg-purple-200">
                        <div class="font-semibold">Manage Admins</div>
                        <div class="text-sm">Control admin access</div>
                    </a>
                </div>
            </div>

            <!-- Recent Donation Requests -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-4">Recent Donation Requests</h2>
                <?php if (empty($recentRequests)): ?>
                    <p class="text-gray-500">No recent requests</p>
                <?php else: ?>
                    <div class="space-y-4">
                        <?php foreach ($recentRequests as $request): ?>
                            <div class="border-b pb-3">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium"><?php echo htmlspecialchars($request['hospital_name']); ?></div>
                                        <div class="text-sm text-gray-600">
                                            Blood Type: <?php echo $request['blood_type']; ?> (<?php echo $request['quantity']; ?> units)
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            Requested by: <?php echo htmlspecialchars($request['requester_email']); ?>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        <?php echo $request['urgency'] === 'high'
                                            ? 'bg-red-100 text-red-800'
                                            : ($request['urgency'] === 'medium'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : 'bg-green-100 text-green-800'); ?>">
                                        <?php echo ucfirst($request['urgency']); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>