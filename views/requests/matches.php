<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';

// Check if request ID is provided
if (!isset($_GET['id'])) {
    $_SESSION['error_message'] = 'Request ID is required';
    header('Location: ' . BASE_URL . '/views/requests/index.php');
    exit();
}

$request_id = $_GET['id'];

// Verify the request exists and belongs to the current user
$stmt = $conn->prepare("
    SELECT dr.*, h.name as hospital_name
    FROM DonationRequest dr
    JOIN Hospital h ON dr.hospital_id = h.hospital_id
    WHERE dr.request_id = ? AND dr.requester_id = ?
");
$stmt->execute([$request_id, $user['user_id']]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    $_SESSION['error_message'] = 'Request not found or access denied';
    header('Location: ' . BASE_URL . '/views/requests/index.php');
    exit();
}

// Get matches for this request
$stmt = $conn->prepare("
    SELECT m.*, d.blood_type, d.user_id as donor_user_id, u.name as donor_name
    FROM Matches m
    JOIN Donor d ON m.donor_id = d.donor_id
    JOIN Users u ON d.user_id = u.user_id
    WHERE m.request_id = ?
    ORDER BY m.score DESC
");
$stmt->execute([$request_id]);
$matches = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pageTitle = 'Potential Donors - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>
    <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Potential Donors for Request #<?php echo $request_id; ?></h2>
                <a href="<?php echo BASE_URL; ?>/views/requests/index.php"
                    class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                    Back to Requests
                </a>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2">Request Details</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Hospital:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                    <p><strong>Blood Type:</strong> <?php echo $request['blood_type']; ?></p>
                    <p><strong>Units Needed:</strong> <?php echo $request['quantity']; ?></p>
                    <p><strong>Urgency:</strong> <?php echo ucfirst($request['urgency']); ?></p>
                </div>
            </div>

            <?php if (empty($matches)): ?>
                <p class="text-gray-500 text-center py-4">No potential donors found for this request.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Donor Name
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Blood Type
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Match Score
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($matches as $match): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php echo htmlspecialchars($match['donor_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo $match['blood_type']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                <div class="bg-red-600 h-2.5 rounded-full" style="width: <?php echo $match['score']; ?>%"></div>
                                            </div>
                                            <span class="ml-2 text-sm text-gray-700"><?php echo $match['score']; ?>%</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="<?php echo BASE_URL; ?>/views/messages/index.php?contact=<?php echo $match['donor_user_id']; ?>"
                                            class="text-blue-600 hover:text-blue-900">
                                            Send Message
                                        </a>
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