<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';

// Get donor details if user is a donor
$donor = null;
if ($isDonor) {
    $stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get active donation requests with requester details
$stmt = $conn->prepare("
    SELECT 
        dr.*,
        h.name as hospital_name,
        h.address as hospital_address,
        COALESCE(da.total_appointments, 0) as appointment_count,
        u.name as requester_name,
        u.email as requester_email,
        u.phone_number as requester_phone
    FROM DonationRequest dr
    JOIN Hospital h ON dr.hospital_id = h.hospital_id
    JOIN Users u ON dr.requester_id = u.user_id
    LEFT JOIN (
        SELECT request_id, COUNT(*) as total_appointments 
        FROM DonationAppointment 
        WHERE status = 'pending' OR status = 'confirmed'
        GROUP BY request_id
    ) da ON dr.request_id = da.request_id
    " . ($isDonor ? "WHERE dr.blood_type = ?" : "") . "
    ORDER BY 
        CASE dr.urgency 
            WHEN 'high' THEN 1 
            WHEN 'medium' THEN 2 
            WHEN 'low' THEN 3 
        END,
        dr.created_at DESC
");

if ($isDonor) {
    $stmt->execute([$donor['blood_type']]);
} else {
    $stmt->execute();
}
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get flash messages
$success = getFlashMessage('success');
$error = getFlashMessage('error');

$pageTitle = 'Blood Donation Requests - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Active Blood Donation Requests</h2>
                <?php if ($isDonor): ?>
                    <a href="<?php echo BASE_URL; ?>/views/appointments/index.php"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        My Appointments
                    </a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/views/requests/create.php"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Create New Request
                    </a>
                <?php endif; ?>
            </div>

            <?php if (empty($requests)): ?>
                <p class="text-gray-500">
                    <?php echo $isDonor ?
                        "No active requests matching your blood type at the moment." :
                        "No active donation requests available."; ?>
                </p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($requests as $request): ?>
                        <?php $isOwnRequest = $request['requester_id'] === $user['user_id']; ?>
                        <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                            <?php if ($isOwnRequest): ?>
                                <div class="mb-2">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Your Request</span>
                                </div>
                            <?php endif; ?>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-medium text-lg">
                                    <?php echo htmlspecialchars($request['hospital_name']); ?>
                                </span>
                                <span class="<?php
                                                echo $request['urgency'] === 'high' ? 'bg-red-100 text-red-800' : ($request['urgency'] === 'medium' ? 'bg-yellow-100 text-yellow-800' :
                                                    'bg-green-100 text-green-800'); ?> 
                                    px-2 py-1 rounded text-sm">
                                    <?php echo ucfirst($request['urgency']); ?>
                                </span>
                            </div>
                            <div class="text-sm text-gray-600 mb-4">
                                <?php echo htmlspecialchars($request['hospital_address']); ?>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Blood Type:</span>
                                    <span class="font-medium"><?php echo $request['blood_type']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Units Needed:</span>
                                    <span class="font-medium"><?php echo $request['quantity']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Appointments:</span>
                                    <span class="font-medium"><?php echo $request['appointment_count']; ?></span>
                                </div>
                                <div class="border-t pt-2 mt-2">
                                    <p class="text-gray-600 font-medium">Requested by:</p>
                                    <p class="text-sm">
                                        <?php echo htmlspecialchars($request['requester_name']); ?>
                                        <?php if ($isOwnRequest): ?>
                                            <span class="text-xs text-gray-500">(You)</span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($request['requester_email']); ?></p>
                                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($request['requester_phone']); ?></p>
                                </div>
                                <div class="text-sm text-gray-500">
                                    Requested: <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?>
                                </div>
                            </div>
                            <div class="flex justify-end border-t pt-4 mt-4">
                                <?php if ($isDonor && !$isOwnRequest): ?>
                                    <a href="<?php echo BASE_URL; ?>/views/appointments/schedule.php?request_id=<?php echo $request['request_id']; ?>"
                                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                        Schedule Appointment
                                    </a>
                                <?php endif; ?>
                                <?php if ($isOwnRequest): ?>
                                    <form method="POST" action="<?php echo BASE_URL; ?>/views/requests/cancel.php"
                                        onsubmit="return confirm('Are you sure you want to cancel this donation request?');">
                                        <input type="hidden" name="request_id" value="<?php echo $request['request_id']; ?>">
                                        <button type="submit" name="cancel_request"
                                            class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                            Cancel Request
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>