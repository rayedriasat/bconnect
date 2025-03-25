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

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $result = $user->login($_POST['email'], $_POST['password']);

        if ($result) {
            if ($result['two_factor_enabled']) {
                $code = $user->generateVerificationCode($result['user_id']);

                if ($user->sendVerificationEmail($result['email'], $code)) {
                    $_SESSION['2fa_required'] = true;
                    $_SESSION['temp_user_id'] = $result['user_id'];
                    $_SESSION['success_message'] = 'Verification code sent! Please check your email.';
                } else {
                    $_SESSION['error_message'] = 'Failed to send verification code. Please try again or contact support.';
                }
            } else {
                unset($_SESSION['2fa_required']);
                unset($_SESSION['temp_user_id']);
                $_SESSION['user'] = $result;
                session_regenerate_id(true);
                header('Location: ' . BASE_URL . '/dashboard.php');
                exit();
            }
        } else {
            $_SESSION['error_message'] = 'Invalid email or password';
        }
    } elseif (isset($_POST['verification_code']) && isset($_SESSION['temp_user_id'])) {
        if ($user->verifyCode($_SESSION['temp_user_id'], $_POST['verification_code'])) {
            $sql = "SELECT * FROM Users WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => $_SESSION['temp_user_id']]);
            $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
            session_regenerate_id(true);
            unset($_SESSION['2fa_required']);
            unset($_SESSION['temp_user_id']);
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit();
        } else {
            $_SESSION['error_message'] = 'Invalid or expired verification code';
        }
    }
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-red-600 px-8 py-10 text-white text-center">
                <h1 class="text-4xl font-bold mb-2">BloodConnect</h1>
                <p class="text-red-100 opacity-90">Life is in your blood</p>
            </div>

            <div class="p-8">
                <?php require_once '../../includes/_alerts.php'; ?>

                <?php if ($_SESSION['2fa_required'] ?? false): ?>
                    <form method="POST" class="space-y-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Verification Code</label>
                            <input type="text" name="verification_code"
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                placeholder="Enter 6-digit code" required>
                        </div>
                        <button type="submit"
                            class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                            Verify Code
                        </button>
                    </form>
                <?php else: ?>
                    <form method="POST" class="space-y-6">
                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-envelope text-gray-400"></i>
                                </div>
                                <input type="email" name="email" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="your@email.com">
                            </div>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-medium mb-2">Password</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                    <i class="fas fa-lock text-gray-400"></i>
                                </div>
                                <input type="password" name="password" required
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                    placeholder="••••••••">
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            Sign In
                        </button>

                        <div class="text-center text-sm text-gray-600 mt-2">
                            <a href="<?= BASE_URL ?>/views/auth/forgot-password.php"
                                class="text-red-600 hover:text-red-800 font-semibold">
                                Forgot Password?
                            </a>
                        </div>

                        <div class="text-center text-sm text-gray-600 mt-4">
                            Don't have an account?
                            <a href="<?= BASE_URL ?>/views/auth/register.php"
                                class="text-red-600 hover:text-red-800 font-semibold">
                                Create account
                            </a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>