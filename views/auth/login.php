<?php
session_start();
require_once '../../config/database.php';
require_once '../../classes/User.php';

define('BASE_URL', '/bconnect');

$error = '';
$requires_2fa = false;
$user_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    $user = new User($conn);

    if (isset($_POST['email']) && isset($_POST['password'])) {
        $result = $user->login($_POST['email'], $_POST['password']);

        if ($result) {
            if ($result['requires_2fa']) {
                $requires_2fa = true;
                $user_id = $result['user_id'];
                $code = $user->generateVerificationCode($user_id);
                $_SESSION['2fa_required'] = true;
                $_SESSION['temp_user_id'] = $user_id;
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
            $error = 'Invalid verification code';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
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

            <?php if ($requires_2fa): ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Verification Code</label>
                        <input type="text" name="verification_code" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-500 text-white rounded-md py-2 hover:bg-blue-600">
                        Verify Code
                    </button>
                </form>
            <?php else: ?>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Password</label>
                        <input type="password" name="password" required
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm">
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="forgot-password.php" class="text-sm text-blue-500 hover:text-blue-700">
                            Forgot Password?
                        </a>
                    </div>

                    <button type="submit"
                        class="w-full bg-blue-500 text-white rounded-md py-2 hover:bg-blue-600">
                        Login
                    </button>
                </form>
            <?php endif; ?>

            <p class="mt-4 text-center text-sm">
                Don't have an account?
                <a href="register.php" class="text-blue-500 hover:text-blue-700">Register here</a>
            </p>
        </div>
    </div>
</body>

</html>