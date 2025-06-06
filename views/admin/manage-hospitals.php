<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';

// Redirect if not an admin
if (!$isAdmin) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$error = getFlashMessage('error');
$success = getFlashMessage('success');

// Handle hospital operations
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $conn->prepare("INSERT INTO Hospital (name, address, email, phone_number) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$_POST['name'], $_POST['address'], $_POST['email'], $_POST['phone_number']]);
                    $_SESSION['success_message'] = 'Hospital added successfully';
                    break;

                case 'edit':
                    $stmt = $conn->prepare("UPDATE Hospital SET name = ?, address = ?, email = ?, phone_number = ? WHERE hospital_id = ?");
                    $stmt->execute([$_POST['name'], $_POST['address'], $_POST['email'], $_POST['phone_number'], $_POST['hospital_id']]);
                    $_SESSION['success_message'] = 'Hospital updated successfully';
                    break;

                case 'delete':
                    $stmt = $conn->prepare("DELETE FROM Hospital WHERE hospital_id = ?");
                    $stmt->execute([$_POST['hospital_id']]);
                    $_SESSION['success_message'] = 'Hospital deleted successfully';
                    break;
            }
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Operation failed: ' . $e->getMessage();
    }

    // Redirect to refresh the page and show messages
    header('Location: ' . BASE_URL . '/views/admin/manage-hospitals.php');
    exit();
}

$pageTitle = 'Manage Hospitals - BloodConnect Admin';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once '../../includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Manage Hospitals</h2>

                <!-- Search Form -->
                <div class="flex gap-2">
                    <select id="searchBy" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <option value="name">Name</option>
                        <option value="address">Address</option>
                        <option value="email">Email</option>
                        <option value="phone">Phone</option>
                    </select>
                    <input type="text"
                        id="searchInput"
                        placeholder="Search hospitals..."
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <button type="button"
                        id="clearSearch"
                        class="hidden bg-gray-500 text-white px-4 py-2 rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Clear
                    </button>
                </div>
            </div>

            <?php require_once '../../includes/_alerts.php'; ?>
            <!-- Add New Hospital Form -->
            <form method="POST" class="mb-8 border-b pb-8">
                <input type="hidden" name="action" value="add">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Hospital Name</label>
                        <input type="text"
                            name="name"
                            required
                            class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <input type="text"
                            name="address"
                            required
                            class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel"
                            name="phone_number"
                            required
                            class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email"
                            name="email"
                            required
                            class="w-full px-3 py-2 rounded-md border-2 border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit"
                        class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Add Hospital
                    </button>
                </div>
            </form>

            <!-- Hospitals Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="hospitalsTableBody" class="bg-white divide-y divide-gray-200">
                        <!-- Table content will be loaded dynamically -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        let searchTimeout;

        function performSearch() {
            const searchValue = document.getElementById('searchInput').value;
            const searchBy = document.getElementById('searchBy').value;

            // Show/hide clear button
            document.getElementById('clearSearch').classList.toggle('hidden', !searchValue);

            // Make AJAX request
            fetch('<?php echo BASE_URL; ?>/views/admin/ajax/search-hospitals.php?search=' +
                    encodeURIComponent(searchValue) + '&searchBy=' + encodeURIComponent(searchBy))
                .then(response => response.json())
                .then(data => {
                    document.getElementById('hospitalsTableBody').innerHTML = data.html;
                })
                .catch(error => {
                    console.error('Search failed:', error);
                });
        }

        // Initial load
        document.addEventListener('DOMContentLoaded', function() {
            performSearch();
        });

        // Search input event handler with debouncing
        document.getElementById('searchInput').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(performSearch, 300); // 300ms delay
        });

        // Search by dropdown change handler
        document.getElementById('searchBy').addEventListener('change', performSearch);

        // Clear button handler
        document.getElementById('clearSearch').addEventListener('click', function() {
            document.getElementById('searchInput').value = '';
            performSearch();
        });
    </script>
</body>

</html>