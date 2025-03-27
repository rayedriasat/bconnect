<?php
require_once '../../includes/auth_middleware.php';
require_once '../../includes/notification_helper.php';
require_once '../../Core/functs.php';

// Get hospitals for dropdown
$stmt = $conn->prepare("SELECT hospital_id, name FROM Hospital ORDER BY name");
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get flash messages
$success = getFlashMessage('success');
$error = getFlashMessage('error');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $hospital_id = $_POST['hospital_id'] ?? '';
    $blood_type = $_POST['blood_type'] ?? '';
    $quantity = $_POST['quantity'] ?? '';
    $urgency = $_POST['urgency'] ?? 'medium';
    $contact_person = $_POST['contact_person'] ?? '';
    $contact_phone = $_POST['contact_phone'] ?? '';

    if (empty($hospital_id) || empty($blood_type) || empty($quantity) || empty($contact_person) || empty($contact_phone)) {
        $_SESSION['error_message'] = 'All fields are required';
        header('Location: ' . BASE_URL . '/views/requests/create.php');
        exit();
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO DonationRequest 
                (hospital_id, requester_id, blood_type, quantity, urgency, contact_person, contact_phone) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $hospital_id,
                $user['user_id'],
                $blood_type,
                $quantity,
                $urgency,
                $contact_person,
                $contact_phone
            ]);

            $request_id = $conn->lastInsertId();

            // Generate matches for the new request
            require_once '../../includes/matching_helper.php';
            generateMatches($conn, $request_id);

            // Notify matching donors
            $notified_count = notifyMatchingDonors($conn, $request_id);

            $_SESSION['success_message'] = 'Donation request created successfully! ' .
                ($notified_count > 0 ? $notified_count . ' potential donors have been notified.' : 'No matching donors found at this time.');

            // Redirect to avoid form resubmission
            header('Location: ' . BASE_URL . '/views/requests/index.php');
            exit();
        } catch (Exception $e) {
            $_SESSION['error_message'] = 'Failed to create donation request: ' . $e->getMessage();
            header('Location: ' . BASE_URL . '/views/requests/create.php');
            exit();
        }
    }
}

$pageTitle = 'Request Blood Donation - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Request Blood Donation</h2>

            <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Hospital</label>
                    <select name="hospital_id" required class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm">
                        <option value="">Select Hospital</option>
                        <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?php echo $hospital['hospital_id']; ?>">
                                <?php echo htmlspecialchars($hospital['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Blood Type Needed</label>
                    <select name="blood_type" required class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm">
                        <option value="">Select Blood Type</option>
                        <?php
                        $blood_types = ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];
                        foreach ($blood_types as $type): ?>
                            <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity (units)</label>
                    <input type="number" name="quantity" min="1" required
                        class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Urgency Level</label>
                    <select name="urgency" required class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person</label>
                    <input type="text" name="contact_person" required
                        class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Contact Phone</label>
                    <input type="tel" name="contact_phone" required
                        class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm">
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="<?php echo BASE_URL; ?>/views/dashboard/index.php"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>