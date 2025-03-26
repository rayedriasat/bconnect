<?php
require_once '../../includes/auth_middleware.php';

// Get donor details if user is a donor
$donor = null;
if ($isDonor) {
    $stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $donor = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get active donation requests
$stmt = $conn->prepare("
    SELECT 
        dr.*,
        h.name as hospital_name,
        h.address as hospital_address,
        COALESCE(da.total_appointments, 0) as appointment_count
    FROM DonationRequest dr
    JOIN Hospital h ON dr.hospital_id = h.hospital_id
    LEFT JOIN (
        SELECT request_id, COUNT(*) as total_appointments 
        FROM DonationAppointment 
        WHERE status = 'pending' OR status = 'confirmed'
        GROUP BY request_id
    ) da ON dr.request_id = da.request_id
    " . ($isDonor ? "WHERE dr.blood_type = ?" : "WHERE dr.requester_id = ?") . "
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
    $stmt->execute([$user['user_id']]);
}
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blood Donation Requests - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php if (isset($_GET['message']) && $_GET['message'] === 'cancelled'): ?>
                    Donation request has been successfully cancelled.
                <?php else: ?>
                    Appointment has been successfully scheduled!
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php if (isset($_GET['message']) && $_GET['message'] === 'cancel_failed'): ?>
                    Failed to cancel donation request. Please try again.
                <?php else: ?>
                    Failed to schedule appointment. Please try again.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Active Blood Donation Requests</h2>

            <?php if (empty($requests)): ?>
                <p class="text-gray-500">No active requests matching your blood type at the moment.</p>
            <?php else: ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($requests as $request): ?>
                        <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
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
                                    <span class="text-gray-600">Scheduled Appointments:</span>
                                    <span class="font-medium"><?php echo $request['appointment_count']; ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Contact:</span>
                                    <span class="font-medium">
                                        <?php echo htmlspecialchars($request['contact_person']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="flex justify-end border-t pt-4 mt-4">
                                <?php if ($isDonor): ?>
                                    <a href="<?php echo BASE_URL; ?>/views/appointments/schedule.php?request_id=<?php echo $request['request_id']; ?>"
                                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                                        Schedule Appointment
                                    </a>
                                <?php else: ?>
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