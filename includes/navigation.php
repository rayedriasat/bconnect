<?php
if (!isset($user)) {
    header('Location: ' . BASE_URL . '/views/auth/login.php');
    exit();
}

// Get unread notification count - only count in-app notifications
$stmt = $conn->prepare("
    SELECT COUNT(*) as unread_count 
    FROM Notification 
    WHERE user_id = ? AND type = 'in-app' AND is_read = 0
");
$stmt->execute([$user['user_id']]);
$unreadCount = $stmt->fetch(PDO::FETCH_ASSOC)['unread_count'];
?>
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="flex-shrink-0 flex items-center">
                    <a href="<?php echo BASE_URL; ?>/views/dashboard/index.php" class="text-red-600 font-bold text-xl">BloodConnect</a>
                </div>
            </div>
            <div class="flex items-center">
                <!-- Add notification icon here -->
                <a href="<?php echo BASE_URL; ?>/views/notifications.php" class="relative p-2 mr-4 text-gray-600 hover:text-gray-900">
                    <i class="fas fa-bell"></i>
                    <?php if ($unreadCount > 0): ?>
                        <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"><?php echo $unreadCount; ?></span>
                    <?php endif; ?>
                </a>

                <!-- User dropdown menu -->
                <div class="relative group">
                    <button class="flex items-center text-gray-700 hover:text-red-600 px-3 py-2 rounded-md">
                        <span>My Account</span>
                        <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                        <a href="<?php echo BASE_URL; ?>/views/profile/index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">My Profile</a>
                        <a href="<?php echo BASE_URL; ?>/views/messages/index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Messages</a>
                        <a href="<?php echo BASE_URL; ?>/views/auth/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
                    </div>
                </div>

                <!-- Donor specific dropdown -->
                <?php if ($isDonor): ?>
                    <div class="relative group">
                        <button class="flex items-center text-gray-700 hover:text-red-600 px-3 py-2 rounded-md">
                            <span>Donor Options</span>
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                            <a href="<?php echo BASE_URL; ?>/views/appointments/index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Appointments</a>
                            <a href="<?php echo BASE_URL; ?>/views/donor/donation-history.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Donation History</a>
                            <a href="<?php echo BASE_URL; ?>/views/donor/blood-inventory.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Blood Inventory</a>
                            <a href="<?php echo BASE_URL; ?>/views/donors/index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Find Donors</a>
                            <a href="<?php echo BASE_URL; ?>/views/requests/index.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Donation Requests</a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Admin dropdown -->
                <?php if ($isAdmin): ?>
                    <div class="relative group">
                        <button class="flex items-center text-gray-700 hover:text-red-600 px-3 py-2 rounded-md">
                            <span>Admin</span>
                            <svg class="ml-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
                            <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Admin Panel</a>
                            <a href="<?php echo BASE_URL; ?>/views/admin/manage-inventory.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Manage Inventory</a>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Request donation button - always visible -->
                <a href="<?php echo BASE_URL; ?>/views/requests/create.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md mr-4">Request Donation</a>

                <!-- User email display -->
                <span class="text-sm text-gray-600 hidden lg:inline-block ml-2"><?php echo htmlspecialchars($user['email']); ?></span>
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden flex items-center">
                <button type="button" class="mobile-menu-button text-gray-700 hover:text-red-600 focus:outline-none">
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="mobile-menu hidden md:hidden pb-4">
            <a href="<?php echo BASE_URL; ?>/views/profile/index.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">My Profile</a>
            <?php if ($isDonor): ?>
                <a href="<?php echo BASE_URL; ?>/views/appointments/index.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Appointments</a>
                <a href="<?php echo BASE_URL; ?>/views/donor/donation-history.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Donation History</a>
                <a href="<?php echo BASE_URL; ?>/views/donor/blood-inventory.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Blood Inventory</a>
                <a href="<?php echo BASE_URL; ?>/views/donors/index.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Find Donors</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/views/messages/index.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Messages</a>
            <?php if ($isAdmin): ?>
                <a href="<?php echo BASE_URL; ?>/views/admin/dashboard.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Admin Panel</a>
                <a href="<?php echo BASE_URL; ?>/views/admin/manage-inventory.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Manage Inventory</a>
            <?php endif; ?>
            <?php if ($isDonor): ?>
                <a href="<?php echo BASE_URL; ?>/views/requests/index.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Donation Requests</a>
            <?php endif; ?>
            <a href="<?php echo BASE_URL; ?>/views/requests/create.php" class="block py-2 px-4 text-gray-700 hover:text-red-600">Request Donation</a>
            <div class="py-2 px-4 text-gray-700"><?php echo htmlspecialchars($user['email']); ?></div>
            <a href="<?php echo BASE_URL; ?>/views/auth/logout.php" class="block py-2 px-4 text-red-600 hover:text-red-800">Logout</a>
        </div>
    </div>
</nav>

<!-- JavaScript for mobile menu toggle and dropdown handling -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Mobile menu toggle
        const mobileMenuButton = document.querySelector('.mobile-menu-button');
        const mobileMenu = document.querySelector('.mobile-menu');

        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });

        // Dropdown menu handling
        const dropdownGroups = document.querySelectorAll('.relative.group');

        dropdownGroups.forEach(group => {
            const dropdownContent = group.querySelector('div[class*="absolute"]');
            let timeoutId;

            // Show dropdown on mouse enter
            group.addEventListener('mouseenter', () => {
                clearTimeout(timeoutId);
                dropdownContent.classList.remove('hidden');
            });

            // Hide dropdown on mouse leave with delay
            group.addEventListener('mouseleave', () => {
                timeoutId = setTimeout(() => {
                    dropdownContent.classList.add('hidden');
                }, 300); // 300ms delay before hiding
            });
        });
    });
</script>