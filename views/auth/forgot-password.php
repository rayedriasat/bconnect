<?php
require_once '../../config/database.php';
require_once '../../classes/User.php';

define('BASE_URL', '/bconnect');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    $user = new User($conn);

    $token = $user->createPasswordReset($_POST['email']);
    if ($token) {
        if ($user->sendPasswordResetEmail($_POST['email'], $token)) {
            $message = 'Password reset instructions have been sent to your email';
        } else {
            $error = 'Failed to send reset instructions. Please try again later.';
        }
    } else {
        $error = 'Email address not found';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-96">
            <h2 class="text-2xl font-bold mb-6 text-center">Forgot Password</h2>

            <?php if ($message): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

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