<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/User.php';

define('BASE_URL', '/bconnect');

$error = '';
$requires_2fa = false;
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    $user = new User($conn);

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $result = $user->login($_POST['email'], $_POST['password']);

        if ($result) {
            if (isset($result['requires_2fa']) && $result['requires_2fa']) {
                $requires_2fa = true;
                $user_id = $result['user_id'];
                $code = $user->generateVerificationCode($user_id);

                if ($user->sendVerificationEmail($result['email'], $code)) {
                    $_SESSION['2fa_required'] = true;
                    $_SESSION['temp_user_id'] = $user_id;
                    $success_message = 'Verification code sent! Please check your email.';
                } else {
                    $error = 'Failed to send verification code. Please try again or contact support.';
                }
            } else {
                $_SESSION['user'] = $result;
                header('Location: ' . BASE_URL . '/dashboard.php');
                exit();
            }
        } else {
            $error = 'Invalid email or password';
        }
    } elseif (isset($_POST['verification_code']) && isset($_SESSION['temp_user_id'])) {
        if ($user->verifyCode($_SESSION['temp_user_id'], $_POST['verification_code'])) {
            $sql = "SELECT * FROM Users WHERE user_id = :user_id";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':user_id' => $_SESSION['temp_user_id']]);
            $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
            unset($_SESSION['2fa_required']);
            unset($_SESSION['temp_user_id']);
            header('Location: ' . BASE_URL . '/dashboard.php');
            exit();
        } else {
            $error = 'Invalid or expired verification code';
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login - BloodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Login</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success_message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if ($requires_2fa || isset($_SESSION['2fa_required'])): ?>
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
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                        <input type="email" name="email"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                            required>
                    </div>
                    <div>
                        <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                        <input type="password" name="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                            required>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        Login
                    </button>
                </form>
            <?php endif; ?>

            <div class="mt-4 text-center">
                <a href="<?php echo BASE_URL; ?>/views/auth/forgot-password.php"
                    class="text-sm text-blue-500 hover:text-blue-700">
                    Forgot Password?
                </a>
            </div>
        </div>
    </div>
</body>

</html>