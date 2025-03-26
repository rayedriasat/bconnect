<?php
require_once '../../includes/auth_middleware.php';

$success = '';
$error = '';

// Handle 2FA toggle
if (isset($_POST['toggle_2fa'])) {
    try {
        $new_2fa_status = $_POST['toggle_2fa'] === 'enable' ? 1 : 0;
        $stmt = $conn->prepare("UPDATE Users SET two_factor_enabled = ? WHERE user_id = ?");
        $stmt->execute([$new_2fa_status, $user['user_id']]);

        // Update session
        $_SESSION['user']['two_factor_enabled'] = $new_2fa_status;
        $user = $_SESSION['user'];

        $success = '2-Factor Authentication has been ' . ($new_2fa_status ? 'enabled' : 'disabled');
    } catch (Exception $e) {
        $error = 'Failed to update 2FA settings';
    }
}

// Handle regular profile updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['toggle_2fa'])) {
    try {
        // Start transaction
        $conn->beginTransaction();

        // Update phone number in Users table
        $stmt = $conn->prepare("UPDATE Users SET phone_number = ? WHERE user_id = ?");
        $stmt->execute([$_POST['phone_number'], $user['user_id']]);

        if ($isDonor) {
            // Update donor details
            $stmt = $conn->prepare("
                UPDATE Donor 
                SET weight = ?, 
                    medical_notes = ?
                WHERE user_id = ?
            ");
            $stmt->execute([
                $_POST['weight'],
                $_POST['medical_notes'],
                $user['user_id']
            ]);
        }

        $conn->commit();
        $success = 'Profile updated successfully';

        // Update session data
        $_SESSION['user']['phone_number'] = $_POST['phone_number'];
        $user = $_SESSION['user'];
    } catch (Exception $e) {
        $conn->rollBack();
        $error = 'Failed to update profile';
    }
}

// Fetch current donor details if user is a donor
$donorDetails = null;
if ($isDonor) {
    $stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
    $stmt->execute([$user['user_id']]);
    $donorDetails = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">My Profile</h2>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 bg-gray-100"
                            disabled>
                        <p class="mt-1 text-sm text-gray-500">Email cannot be changed</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                        <input type="tel" name="phone_number"
                            value="<?php echo htmlspecialchars($user['phone_number']); ?>"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300"
                            required>
                    </div>

                    <?php if ($isDonor): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Blood Type</label>
                            <input type="text" value="<?php echo htmlspecialchars($donorDetails['blood_type']); ?>"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300 bg-gray-100"
                                disabled>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Weight (kg)</label>
                            <input type="number" name="weight"
                                value="<?php echo htmlspecialchars($donorDetails['weight']); ?>"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300"
                                min="0" step="0.1" required>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Medical Notes</label>
                            <textarea name="medical_notes" rows="3"
                                class="mt-1 block w-full rounded-md border-2 border-gray-300"><?php echo htmlspecialchars($donorDetails['medical_notes'] ?? ''); ?></textarea>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="border-t mt-6 pt-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Two-Factor Authentication</h3>
                            <p class="text-sm text-gray-500">
                                <?php echo $user['two_factor_enabled']
                                    ? 'Your account is protected with 2FA'
                                    : 'Enable 2FA for additional security'; ?>
                            </p>
                        </div>
                        <form method="POST" class="ml-4">
                            <input type="hidden"
                                name="toggle_2fa"
                                value="<?php echo $user['two_factor_enabled'] ? 'disable' : 'enable'; ?>">
                            <button type="submit"
                                class="<?php echo $user['two_factor_enabled']
                                            ? 'bg-red-600 hover:bg-red-700'
                                            : 'bg-green-600 hover:bg-green-700'; ?> 
                                    text-white px-4 py-2 rounded-md transition duration-150">
                                <?php echo $user['two_factor_enabled'] ? 'Disable 2FA' : 'Enable 2FA'; ?>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="<?php echo BASE_URL; ?>/views/profile/change-password.php"
                        class="text-blue-600 hover:text-blue-800">
                        Change Password
                    </a>
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>