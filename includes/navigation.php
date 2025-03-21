<?php
if (!isset($user)) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit();
}
?>
<nav class="bg-white shadow-lg">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex items-center space-x-8">
                <a href="<?php echo BASE_URL; ?>/dashboard.php" class="text-2xl font-semibold text-red-600">BloodConnect</a>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="<?php echo BASE_URL; ?>/profile/index.php" class="text-gray-700 hover:text-red-600">My Profile</a>
                    <?php if ($isDonor): ?>
                        <a href="<?php echo BASE_URL; ?>/appointments.php" class="text-gray-700 hover:text-red-600">Appointments</a>
                        <a href="<?php echo BASE_URL; ?>/donation-history.php" class="text-gray-700 hover:text-red-600">Donation History</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/messages.php" class="text-gray-700 hover:text-red-600">Messages</a>
                    <?php if ($isAdmin): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/dashboard.php" class="text-gray-700 hover:text-red-600">Admin Panel</a>
                        <a href="<?php echo BASE_URL; ?>/admin/manage-inventory.php" class="text-gray-700 hover:text-red-600">Manage Inventory</a>
                    <?php endif; ?>
                    <?php if ($isDonor): ?>
                        <a href="<?php echo BASE_URL; ?>/donation-requests.php" class="text-gray-700 hover:text-red-600">Donation Requests</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/request-donation.php" class="text-gray-700 hover:text-red-600">Request Donation</a>
                </div>
            </div>
            <div class="flex items-center space-x-4">
                <span class="text-gray-700"><?php echo htmlspecialchars($user['email']); ?></span>
                <a href="<?php echo BASE_URL; ?>/logout.php" class="text-red-600 hover:text-red-800">Logout</a>
            </div>
        </div>
    </div>
</nav>