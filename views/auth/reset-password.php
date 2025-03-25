<?php
session_start();
require_once '../../classes/User.php';
require_once '../../Core/functs.php';
$success = getFlashMessage('success');
$error = getFlashMessage('error');
define('BASE_URL', '/bconnect');

$_SESSION['token'] = $_GET['token'] ?? '';
$_SESSION['user_id'] = $_GET['user_id'] ?? '';

$conn = require_once '../../includes/db_connect.php';
$user = new User($conn);

$user_id = $_SESSION['user_id'];
$token = $_SESSION['token'];
$result = $user->verifyPasswordReset($user_id, $token);
// Verify token validity
if (!$result) {
    $error = 'Invalid or expired reset token';
    $_SESSION['error_message'] = $error;
    header('Location: ' . BASE_URL . '/views/auth/forgot-password.php');
    exit();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($_POST['password'] !== $_POST['password_confirm']) {
        $_SESSION['error_message'] = 'Passwords do not match';
    } else {
        if ($user->resetPassword($user_id, $token, $_POST['password'])) {
            $_SESSION['success_message'] = 'Password has been reset successfully';
            header('Location: ' . BASE_URL . '/views/auth/login.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Password reset failed';
        }
        header('Location: ' . BASE_URL . '/views/auth/reset-password.php?token=' . $token . '&user_id=' . $user_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Reset Password</h2>
            <?php require_once '../../includes/_alerts.php'; ?>
            <?php if (!$error): ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" name="password" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <input type="password" name="password_confirm" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-500 text-white rounded-md py-2 hover:bg-blue-600">
                        Reset Password
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>