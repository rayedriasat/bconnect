<?php
require_once 'includes/auth_middleware.php';

// Get list of hospitals
$stmt = $conn->prepare("SELECT hospital_id, name FROM Hospital ORDER BY name");
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Trim and validate all inputs
        $hospital_id = trim($_POST['hospital_id']);
        $blood_type = trim($_POST['blood_type']);
        $quantity = (int)trim($_POST['quantity']);
        $urgency = trim($_POST['urgency']);
        $contact_person = trim($_POST['contact_person']);
        $contact_phone = trim($_POST['contact_phone']);

        // Validate inputs
        if (empty($hospital_id) || empty($blood_type) || empty($quantity) || 
            empty($urgency) || empty($contact_person) || empty($contact_phone)) {
            throw new Exception('All fields are required');
        }

        if ($quantity <= 0) {
            throw new Exception('Quantity must be greater than 0');
        }

        // Insert the request
        $stmt = $conn->prepare("
            INSERT INTO DonationRequest (
                hospital_id, requester_id, blood_type, quantity, 
                urgency, contact_person, contact_phone
            ) VALUES (?, ?, ?, ?, ?, ?, ?)
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

        $success = 'Blood donation request created successfully!';
        
        // Redirect to dashboard after 2 seconds
        header("refresh:2;url=" . BASE_URL . "/dashboard.php");
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Blood Donation - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <?php require_once 'includes/navigation.php'; ?>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Request Blood Donation</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

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
                    <a href="<?php echo BASE_URL; ?>/dashboard.php"
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