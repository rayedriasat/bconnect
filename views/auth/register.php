<?php
session_start();

require_once '../../classes/User.php';
require_once '../../Core/functs.php';
$success = getFlashMessage('success');
$error = getFlashMessage('error');

define('BASE_URL', '/bconnect');

$name = '';
$email = '';
$phone = '';
$password = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conn = require_once '../../includes/db_connect.php';
    $user = new User($conn);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $_SESSION['error_message'] = 'All fields are required';
    }
    // Then validate phone format
    elseif (!preg_match('/^01\d{9}$/', $phone)) {
        $_SESSION['error_message'] = 'Phone must be 11 digits starting with 01 (e.g. 01777158099)';
    } else {
        // Check for existing email or phone
        $stmt = $conn->prepare("SELECT email, phone_number FROM Users WHERE email = ? OR phone_number = ?");
        $stmt->execute([$email, $phone]);

        if ($stmt->rowCount() > 0) {
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing['email'] === $email) {
                $_SESSION['error_message'] = 'Email is already registered';
            } else {
                $_SESSION['error_message'] = 'Phone number is already registered';
            }
        } else {
            $result = $user->register($name, $email, $phone, $password);

            if ($result) {
                $_SESSION['success_message'] = 'Registration successful! Please login with your credentials.';
                header('Location: ' . BASE_URL . '/views/auth/login.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Registration failed due to server error';
            }
        }
    }
    header('Location: ' . BASE_URL . '/views/auth/register.php');
    exit();
}

$pageTitle = 'Register - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-red-600 px-8 py-10 text-white text-center">
                <h1 class="text-4xl font-bold mb-2">BloodConnect</h1>
                <p class="text-red-100 opacity-90">Join our lifesaving community</p>
            </div>

            <div class="p-8">
                <?php require_once '../../includes/_alerts.php'; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Full Name</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input type="text" name="name" required
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="Your Name"
                                value="<?php echo htmlspecialchars($name); ?>">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" name="email" required
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="your@email.com"
                                value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                    </div>

                    <div>
                        <label class="block text-gray-700 text-sm font-medium mb-2">Phone Number</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="fas fa-phone text-gray-400"></i>
                            </div>
                            <input type="tel" name="phone" required
                                pattern="01\d{9}"
                                title="11-digit number starting with 01 (e.g. 01XXXXXXXXX)"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="01XXXXXXXXX"
                                value="<?php echo htmlspecialchars($phone); ?>">
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
                        <p class="text-xs text-gray-500 mt-2">Minimum 6 characters</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                            class="w-full bg-red-600 text-white py-3 px-4 rounded-lg hover:bg-red-700 transition-colors">
                            Create Account
                        </button>
                    </div>

                    <div class="text-center text-sm text-gray-600 mt-4">
                        Already have an account?
                        <a href="<?= BASE_URL ?>/views/auth/login.php"
                            class="text-red-600 hover:text-red-800 font-semibold">
                            Login here
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>