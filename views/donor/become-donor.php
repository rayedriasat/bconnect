<?php
require_once 'includes/auth_middleware.php';

$error = '';
$success = '';

// Redirect if already a donor
if ($isDonor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validate inputs
        if (
            empty($_POST['blood_type']) || empty($_POST['date_of_birth']) ||
            empty($_POST['weight']) || !isset($_POST['has_medical_condition'])
        ) {
            throw new Exception('All fields are required');
        }

        // Calculate age
        $dob = new DateTime($_POST['date_of_birth']);
        $today = new DateTime();
        $age = $today->diff($dob)->y;

        if ($age < 18) {
            throw new Exception('You must be at least 18 years old to become a donor');
        }

        if ($_POST['weight'] < 50) {
            throw new Exception('Minimum weight requirement is 50 kg');
        }

        // Start transaction before any database operations
        $conn->beginTransaction();

        try {
            // Insert into Donor table
            $stmt = $conn->prepare("
                INSERT INTO Donor (
                    user_id, 
                    blood_type, 
                    date_of_birth, 
                    weight, 
                    has_medical_condition, 
                    medical_notes,
                    last_donation_date,
                    is_available
                ) VALUES (?, ?, ?, ?, ?, ?, NULL, TRUE)
            ");

            $stmt->execute([
                $user['user_id'],
                $_POST['blood_type'],
                $_POST['date_of_birth'],
                $_POST['weight'],
                $_POST['has_medical_condition'] ? 1 : 0,
                $_POST['medical_notes'] ?? null
            ]);

            // If we get here, commit the transaction
            $conn->commit();
            $success = 'You have successfully registered as a donor!';

            // Redirect to dashboard after 2 seconds
            header("refresh:2;url=" . BASE_URL . "/dashboard.php");
        } catch (PDOException $e) {
            // Roll back the transaction if there was a database error
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            throw new Exception('Database error occurred. Please try again.');
        }
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
    <title>Become a Donor - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-2xl font-semibold text-red-600">
                        BloodConnect
                    </a>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, <?php echo htmlspecialchars($user['email']); ?></span>
                    <a href="<?php echo BASE_URL; ?>/logout.php" class="text-red-600 hover:text-red-800">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Become a Blood Donor</h2>

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

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Blood Type</label>
                        <select name="blood_type" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                            <option value="">Select Blood Type</option>
                            <option value="A+">A+</option>
                            <option value="A-">A-</option>
                            <option value="B+">B+</option>
                            <option value="B-">B-</option>
                            <option value="AB+">AB+</option>
                            <option value="AB-">AB-</option>
                            <option value="O+">O+</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date of Birth</label>
                        <input type="date" name="date_of_birth" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                        <input type="number" name="weight" min="0" step="0.1" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Do you have any medical conditions?</label>
                        <div class="mt-2 space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" name="has_medical_condition" value="1" class="form-radio">
                                <span class="ml-2">Yes</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" name="has_medical_condition" value="0" class="form-radio">
                                <span class="ml-2">No</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Medical Notes (if any)</label>
                    <textarea name="medical_notes" rows="3"
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm"
                        placeholder="Please describe any medical conditions, medications, or relevant health information"></textarea>
                </div>

                <div class="bg-gray-50 p-4 rounded">
                    <h3 class="font-medium text-gray-900 mb-2">Requirements for Blood Donation:</h3>
                    <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                        <li>Must be at least 18 years old</li>
                        <li>Must weigh at least 50 kg</li>
                        <li>Must be in good health condition</li>
                        <li>Must not have donated blood in the last 3 months</li>
                        <li>Must not have any blood-borne diseases</li>
                    </ul>
                </div>

                <div class="flex items-center justify-end space-x-4">
                    <a href="<?php echo BASE_URL; ?>/dashboard.php"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                        Cancel
                    </a>
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Register as Donor
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>