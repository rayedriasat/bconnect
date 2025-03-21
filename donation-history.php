<?php
require_once 'includes/auth_middleware.php';

// Redirect if not a donor
if (!$isDonor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

// Get donor details
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

// Get donation history
$stmt = $conn->prepare("
    SELECT 
        drh.*,
        h.name as hospital_name,
        h.address as hospital_address,
        u.email as requester_email
    FROM DonationRequestHistory drh
    JOIN Hospital h ON drh.hospital_id = h.hospital_id
    JOIN Users u ON drh.requester_id = u.user_id
    WHERE drh.fulfilled_by = ?
    ORDER BY drh.fulfilled_at DESC
");
$stmt->execute([$donor['donor_id']]);
$donations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation History - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once 'includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">My Donation History</h2>

            <?php if (empty($donations)): ?>
                <p class="text-gray-500">You haven't made any donations yet.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Hospital
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Blood Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($donations as $donation): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M j, Y', strtotime($donation['fulfilled_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($donation['hospital_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($donation['hospital_address']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $donation['blood_type']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $donation['quantity']; ?> units
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $donation['status'] === 'fulfilled'
                                                ? 'bg-green-100 text-green-800'
                                                : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo ucfirst($donation['status']); ?>
                                        </span>
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