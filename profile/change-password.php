<?php
require_once '../includes/auth_middleware.php';

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } else {
        // Verify current password and update to new password
        $stmt = $conn->prepare("SELECT password_hash FROM Users WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($currentPassword, $result['password_hash'])) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE Users SET password_hash = ? WHERE user_id = ?");
            if ($stmt->execute([$newPasswordHash, $user['user_id']])) {
                $success = 'Password updated successfully';
            } else {
                $error = 'Failed to update password';
            }
        } else {
            $error = 'Current password is incorrect';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/navigation.php'; ?>

    <div class="max-w-2xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Change Password</h2>

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
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" required
                        class="mt-1 block w-full rounded-md border-2 border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="new_password" required
                        class="mt-1 block w-full rounded-md border-2 border-gray-300">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input type="password" name="confirm_password" required
                        class="mt-1 block w-full rounded-md border-2 border-gray-300">
                </div>

                <div class="flex items-center justify-between pt-4 border-t">
                    <a href="<?php echo BASE_URL; ?>/profile/index.php"
                        class="text-gray-600 hover:text-gray-800">
                        Back to Profile
                    </a>
                    <button type="submit"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>