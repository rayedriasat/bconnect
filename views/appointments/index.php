<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';

// Add this function at the top of the file
function canUpdateStatus($currentStatus)
{
    return !in_array($currentStatus, ['cancelled', 'completed']);
}

// Redirect if not a donor
if (!$isDonor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

// First fetch donor details
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

// Then fetch all appointments for the donor
$stmt = $conn->prepare("
    SELECT 
        da.*,
        dr.blood_type,
        dr.quantity,
        h.name as hospital_name,
        h.address as hospital_address,
        h.phone_number as hospital_phone
    FROM DonationAppointment da
    JOIN DonationRequest dr ON da.request_id = dr.request_id
    JOIN Hospital h ON dr.hospital_id = h.hospital_id
    WHERE da.donor_id = ?
    ORDER BY da.scheduled_time DESC
");

$stmt->execute([$donor['donor_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get flash messages
$success = getFlashMessage('success');
$error = getFlashMessage('error');

// Set page title
$pageTitle = 'My Appointments - BloodConnect';

// Include header
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>
    <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">My Appointments</h2>
                <a href="<?php echo BASE_URL; ?>/views/requests/index.php"
                    class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Find Donation Requests
                </a>
            </div>


            <?php if (empty($appointments)): ?>
                <p class="text-gray-500 text-center py-4">No appointments found.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hospital
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Blood Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Units
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contact
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($appointments as $appointment): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo date('M j, Y g:i A', strtotime($appointment['scheduled_time'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($appointment['hospital_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($appointment['hospital_address']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $appointment['blood_type']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $appointment['quantity']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-2">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php
                                                switch ($appointment['status']) {
                                                    case 'pending':
                                                        echo 'bg-yellow-100 text-yellow-800';
                                                        break;
                                                    case 'confirmed':
                                                        echo 'bg-green-100 text-green-800';
                                                        break;
                                                    case 'completed':
                                                        echo 'bg-blue-100 text-blue-800';
                                                        break;
                                                    case 'cancelled':
                                                        echo 'bg-red-100 text-red-800';
                                                        break;
                                                }
                                                ?>">
                                                <?php echo ucfirst($appointment['status']); ?>
                                            </span>

                                            <?php if (canUpdateStatus($appointment['status'])): ?>
                                                <form method="POST" action="<?php echo BASE_URL; ?>/views/appointments/update-status.php"
                                                    class="flex items-center space-x-2"
                                                    onsubmit="return confirm('Are you sure you want to update this appointment status?');">
                                                    <input type="hidden" name="appointment_id" value="<?php echo htmlspecialchars($appointment['appointment_id']); ?>">
                                                    <select name="status" required
                                                        class="text-sm rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                                        <option value="">Change Status</option>
                                                        <?php if ($appointment['status'] !== 'pending'): ?>
                                                            <option value="pending">Pending</option>
                                                        <?php endif; ?>
                                                        <?php if ($appointment['status'] !== 'confirmed'): ?>
                                                            <option value="confirmed">Confirmed</option>
                                                        <?php endif; ?>
                                                        <?php if ($appointment['status'] !== 'completed'): ?>
                                                            <option value="completed">Completed</option>
                                                        <?php endif; ?>
                                                        <?php if ($appointment['status'] !== 'cancelled'): ?>
                                                            <option value="cancelled">Cancelled</option>
                                                        <?php endif; ?>
                                                    </select>
                                                    <button type="submit"
                                                        class="bg-red-600 text-white px-2 py-1 rounded text-sm hover:bg-red-700">
                                                        Update
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($appointment['hospital_phone']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>