<?php
require_once '../../includes/auth_middleware.php';

// Redirect if not an admin
if (!$isAdmin) {
    header('HTTP/1.1 403 Forbidden');
    exit('Unauthorized');
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$searchBy = isset($_GET['searchBy']) ? $_GET['searchBy'] : 'name';

// Build query
$query = "SELECT * FROM Hospital WHERE 1=1";
if (!empty($search)) {
    switch ($searchBy) {
        case 'name':
            $query .= " AND name LIKE ?";
            break;
        case 'address':
            $query .= " AND address LIKE ?";
            break;
        case 'email':
            $query .= " AND email LIKE ?";
            break;
        case 'phone':
            $query .= " AND phone_number LIKE ?";
            break;
    }
    $query .= " ORDER BY name";
    $stmt = $conn->prepare($query);
    $stmt->execute(["%$search%"]);
} else {
    $stmt = $conn->prepare($query . " ORDER BY name");
    $stmt->execute();
}

$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return HTML for the hospitals table
ob_start();
?>
<?php if (empty($hospitals)): ?>
    <tr>
        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No hospitals found</td>
    </tr>
<?php else: ?>
    <?php foreach ($hospitals as $hospital): ?>
        <tr>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($hospital['name']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($hospital['address']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($hospital['email']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($hospital['phone_number']); ?></td>
            <td class="px-6 py-4 whitespace-nowrap text-right">
                <form method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this hospital?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="hospital_id" value="<?php echo $hospital['hospital_id']; ?>">
                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
<?php
$html = ob_get_clean();
header('Content-Type: application/json');
echo json_encode(['html' => $html]);
?>