<?php
require_once '../includes/auth_middleware.php';

// Redirect if not an admin
if (!$isAdmin) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

$success = '';
$error = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'add':
                    $stmt = $conn->prepare("
                        INSERT INTO BloodInventory (hospital_id, blood_type, quantity)
                        VALUES (?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        quantity = quantity + VALUES(quantity),
                        last_updated = CURRENT_TIMESTAMP
                    ");
                    $stmt->execute([
                        $_POST['hospital_id'],
                        $_POST['blood_type'],
                        $_POST['quantity']
                    ]);
                    $success = 'Inventory updated successfully';
                    break;

                case 'update':
                    $stmt = $conn->prepare("
                        UPDATE BloodInventory 
                        SET quantity = ?, last_updated = CURRENT_TIMESTAMP
                        WHERE inventory_id = ?
                    ");
                    $stmt->execute([
                        $_POST['quantity'],
                        $_POST['inventory_id']
                    ]);
                    $success = 'Inventory updated successfully';
                    break;

                case 'delete':
                    $stmt = $conn->prepare("
                        DELETE FROM BloodInventory 
                        WHERE inventory_id = ?
                    ");
                    $stmt->execute([$_POST['inventory_id']]);
                    $success = 'Inventory record deleted successfully';
                    break;
            }
        }
    } catch (Exception $e) {
        $error = 'Operation failed: ' . $e->getMessage();
    }
}

// Get all hospitals
$stmt = $conn->prepare("SELECT hospital_id, name FROM Hospital ORDER BY name");
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get current inventory
$stmt = $conn->prepare("
    SELECT 
        bi.*,
        h.name as hospital_name
    FROM BloodInventory bi
    JOIN Hospital h ON bi.hospital_id = h.hospital_id
    ORDER BY h.name, bi.blood_type
");
$stmt->execute();
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blood Inventory - BloodConnect Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="bg-gray-100">
    <?php require_once '../includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <?php if ($success): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <!-- Add/Update Inventory Form -->
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h2 class="text-xl font-semibold mb-4">Add/Update Inventory</h2>
            <form method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="hidden" name="action" value="add">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hospital</label>
                    <select name="hospital_id" required class="w-full rounded border-gray-300">
                        <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?php echo $hospital['hospital_id']; ?>">
                                <?php echo htmlspecialchars($hospital['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                    <select name="blood_type" required class="w-full rounded border-gray-300">
                        <?php
                        $blood_types = ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];
                        foreach ($blood_types as $type) {
                            echo "<option value=\"$type\">$type</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                    <input type="number" name="quantity" required min="0"
                        class="w-full rounded border-gray-300">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Add/Update Inventory
                    </button>
                </div>
            </form>
        </div>

        <!-- Current Inventory Table -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Current Inventory</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Hospital
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Blood Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Updated
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($item['hospital_name']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $item['blood_type']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="flex items-center space-x-2">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="inventory_id"
                                            value="<?php echo $item['inventory_id']; ?>">
                                        <input type="number" name="quantity"
                                            value="<?php echo $item['quantity']; ?>"
                                            class="w-20 rounded border-gray-300">
                                        <button type="submit"
                                            class="text-blue-600 hover:text-blue-800">
                                            Update
                                        </button>
                                    </form>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo date('M j, Y g:i A', strtotime($item['last_updated'])); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <form method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this record?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="inventory_id"
                                            value="<?php echo $item['inventory_id']; ?>">
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-800">
                                            Delete
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

    <script>
        $(document).ready(function() {
            $('select').select2();
        });
    </script>
</body>

</html>