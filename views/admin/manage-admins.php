<?php
require_once '../../includes/auth_middleware.php';

// Redirect if not an admin
if (!$isAdmin) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$error = '';
$success = '';

// Handle admin addition
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'add') {
        $email = trim($_POST['email']);

        if (empty($email)) {
            $error = 'Email is required';
        } else {
            try {
                // First check if user exists
                $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    // Check if already admin
                    $stmt = $conn->prepare("SELECT * FROM Admin WHERE user_id = ?");
                    $stmt->execute([$user['user_id']]);
                    if (!$stmt->fetch()) {
                        // Make user admin
                        $stmt = $conn->prepare("INSERT INTO Admin (user_id) VALUES (?)");
                        $stmt->execute([$user['user_id']]);
                        $success = 'User has been made admin successfully!';
                    } else {
                        $error = 'User is already an admin';
                    }
                } else {
                    $error = 'User not found';
                }
            } catch (Exception $e) {
                $error = 'Failed to add admin. Please try again.';
            }
        }
    }

    // Handle admin removal
    if (isset($_POST['action']) && $_POST['action'] === 'remove') {
        $admin_user_id = $_POST['admin_user_id'];

        try {
            $stmt = $conn->prepare("DELETE FROM Admin WHERE user_id = ?");
            $stmt->execute([$admin_user_id]);
            $success = 'Admin privileges removed successfully!';
        } catch (Exception $e) {
            $error = 'Failed to remove admin privileges.';
        }
    }
}

// Get list of admins
$stmt = $conn->prepare("
    SELECT u.user_id, u.email, u.phone_number 
    FROM Users u 
    JOIN Admin a ON u.user_id = a.user_id 
    ORDER BY u.email
");
$stmt->execute();
$admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Admins - BloodConnect Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once '../../includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Manage Administrators</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Add New Admin Form -->
            <form method="POST" class="mb-8 border-b pb-8">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">User Email</label>
                        <input type="email"
                            name="email"
                            required
                            class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div class="flex items-end">
                        <button type="submit"
                            class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Make Admin
                        </button>
                    </div>
                </div>
            </form>

            <!-- Admins List -->
            <h3 class="text-xl font-semibold mb-4">Current Administrators</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($admin['email']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php echo htmlspecialchars($admin['phone_number']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" class="inline">
                                        <input type="hidden" name="action" value="remove">
                                        <input type="hidden" name="admin_user_id" value="<?php echo $admin['user_id']; ?>">
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to remove admin privileges?')"
                                            class="text-red-600 hover:text-red-900">
                                            Remove Admin
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>