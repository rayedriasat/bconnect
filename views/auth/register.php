<?php
session_start();
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
} else {
    $success = '';
}
require_once '../../config/database.php';
require_once '../../classes/User.php';

define('BASE_URL', '/bconnect');

$name = '';
$email = '';
$phone = '';
$password = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new Database();
    $conn = $db->connect();
    $user = new User($conn);

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    if (empty($name) || empty($email) || empty($phone) || empty($password)) {
        $error = 'All fields are required';
    }
    // Then validate phone format
    elseif (!preg_match('/^01\d{9}$/', $phone)) {
        $error = 'Phone must be 11 digits starting with 01 (e.g. 01777158099)';
    } else {
        // Check for existing email or phone
        $stmt = $conn->prepare("SELECT email, phone_number FROM Users WHERE email = ? OR phone_number = ?");
        $stmt->execute([$email, $phone]);
        
        if ($stmt->rowCount() > 0) {
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($existing['email'] === $email) {
                $error = 'Email is already registered';
            } else {
                $error = 'Phone number is already registered';
            }
        } else {
            $result = $user->register($name, $email, $phone, $password);

            if ($result) {
                // Set success message in session
                session_start();
                $_SESSION['success_message'] = 'Registration successful! Please login with your credentials.';
                header('Location: ' . BASE_URL . '/views/auth/login.php');
                exit();
            } else {
                $error = 'Registration failed due to server error';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="max-w-md w-full mx-4">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="bg-red-600 px-8 py-10 text-white text-center">
                <h1 class="text-4xl font-bold mb-2">BloodConnect</h1>
                <p class="text-red-100 opacity-90">Join our lifesaving community</p>
            </div>

            <div class="p-8">
                <?php if (!empty($success)): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

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