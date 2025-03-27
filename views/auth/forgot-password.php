<?php
session_start();
require_once '../../classes/User.php';
require_once '../../Core/functs.php';
$success = getFlashMessage('success');
$error = getFlashMessage('error');

define('BASE_URL', '/bconnect');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = require_once '../../includes/db_connect.php';
    $user = new User($conn);

    $token = $user->createPasswordReset($_POST['email']);
    if ($token) {
        if ($user->sendPasswordResetEmail($_POST['email'], $token)) {
            $_SESSION['success_message'] = 'Password reset instructions have been sent to your email';
        } else {
            $_SESSION['error_message'] = 'Failed to send reset instructions. Please try again later.';
        }
    } else {
        $_SESSION['error_message'] = 'Email address not found';
    }
    header('Location: ' . BASE_URL . '/views/auth/forgot-password.php');
    exit();
}

$pageTitle = 'Forgot Password - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Forgot Password</h2>
            <?php require_once '../../includes/_alerts.php'; ?>

            <form method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="email" required
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 text-white rounded-md py-2 hover:bg-blue-600">
                    Send Reset Link
                </button>
            </form>

            <p class="mt-4 text-center text-sm">
                <a href="login.php" class="text-blue-500 hover:text-blue-700">Back to Login</a>
            </p>
        </div>
    </div>
</body>

</html>