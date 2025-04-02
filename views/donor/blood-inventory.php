<?php
require_once '../../includes/auth_middleware.php';

// Get user's location (if available)
$user_location = null;
if ($user['user_id']) {
    $stmt = $conn->prepare("
        SELECT latitude, longitude 
        FROM Location 
        WHERE user_id = ? 
        ORDER BY location_id DESC 
        LIMIT 1
    ");
    $stmt->execute([$user['user_id']]);
    $user_location = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle search and filtering
$blood_type = isset($_GET['blood_type']) ? $_GET['blood_type'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build query with location-based sorting if user location is available
$query = "
    SELECT 
        bi.*,
        h.name as hospital_name,
        h.address as hospital_address,
        l.latitude,
        l.longitude,
        l.address as location_address
    FROM BloodInventory bi
    JOIN Hospital h ON bi.hospital_id = h.hospital_id
    LEFT JOIN Location l ON h.hospital_id = l.hospital_id
    WHERE 1=1
";

if ($blood_type) {
    $query .= " AND bi.blood_type = :blood_type";
}

if ($search) {
    $query .= " AND (h.name LIKE :search OR h.address LIKE :search)";
}

if ($user_location) {
    // Add distance calculation using Haversine formula
    $query .= "
        ORDER BY 
        (
            6371 * acos(
                cos(radians(:lat)) * 
                cos(radians(l.latitude)) * 
                cos(radians(l.longitude) - radians(:lng)) + 
                sin(radians(:lat)) * 
                sin(radians(l.latitude))
            )
        )
    ";
} else {
    $query .= " ORDER BY h.name";
}

$stmt = $conn->prepare($query);

if ($blood_type) {
    $stmt->bindParam(':blood_type', $blood_type);
}

if ($search) {
    $search_param = "%$search%";
    $stmt->bindParam(':search', $search_param);
}

if ($user_location) {
    $stmt->bindParam(':lat', $user_location['latitude']);
    $stmt->bindParam(':lng', $user_location['longitude']);
}

$stmt->execute();
$inventory = $stmt->fetchAll(PDO::FETCH_ASSOC);
// Set page title
$pageTitle = 'Blood Inventory - BloodConnect';

// Add Select2 CSS and JS
$additionalHeadContent = '
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
';

require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once '../../includes/navigation.php'; ?>
    <?php require_once '../../includes/_alerts.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Blood Inventory</h2>
                <?php if ($isAdmin): ?>
                    <a href="<?php echo BASE_URL; ?>/views/admin/manage-inventory.php"
                        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Manage Inventory
                    </a>
                <?php endif; ?>
            </div>

            <!-- Search and Filter Form -->
            <form class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                    <select name="blood_type" class="w-full rounded border-gray-300">
                        <option value="">All Types</option>
                        <?php
                        $blood_types = ['O-', 'O+', 'A-', 'A+', 'B-', 'B+', 'AB-', 'AB+'];
                        foreach ($blood_types as $type) {
                            $selected = $blood_type === $type ? 'selected' : '';
                            echo "<option value=\"$type\" $selected>$type</option>";
                        }
                        ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search Hospital</label>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        class="w-full rounded border-gray-300"
                        placeholder="Enter hospital name or location">
                </div>
                <div class="flex items-end">
                    <button type="submit"
                        class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Search
                    </button>
                </div>
            </form>

            <!-- Inventory Table -->
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
                                Available Units
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Updated
                            </th>
                            <?php if ($user_location): ?>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Distance
                                </th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($inventory as $item): ?>
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">
                                        <?php echo htmlspecialchars($item['hospital_name']); ?>
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        <?php if (!empty($item['latitude']) && !empty($item['longitude'])): ?>
                                            <a href="#" class="hover:text-blue-600 show-map-link"
                                                data-lat="<?php echo htmlspecialchars($item['latitude']); ?>"
                                                data-lng="<?php echo htmlspecialchars($item['longitude']); ?>"
                                                data-name="<?php echo htmlspecialchars($item['hospital_name']); ?>"
                                                data-address="<?php echo htmlspecialchars($item['location_address'] ?? $item['hospital_address']); ?>">
                                                <?php echo htmlspecialchars($item['location_address'] ?? $item['hospital_address']); ?>
                                            </a>
                                        <?php else: ?>
                                            <?php echo htmlspecialchars($item['location_address'] ?? $item['hospital_address']); ?>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo $item['blood_type']; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="<?php echo $item['quantity'] < 10 ? 'text-red-600' : 'text-green-600'; ?>">
                                        <?php echo $item['quantity']; ?> units
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <?php echo date('M j, Y g:i A', strtotime($item['last_updated'])); ?>
                                </td>
                                <?php if ($user_location && isset($item['latitude']) && isset($item['longitude'])): ?>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php
                                        $distance = calculateDistance(
                                            $user_location['latitude'],
                                            $user_location['longitude'],
                                            $item['latitude'],
                                            $item['longitude']
                                        );
                                        echo number_format($distance, 1) . ' km';
                                        ?>
                                    </td>
                                <?php endif; ?>
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

<?php
function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $radius = 6371; // Earth's radius in kilometers

    $dlat = deg2rad($lat2 - $lat1);
    $dlon = deg2rad($lon2 - $lon1);

    $a = sin($dlat / 2) * sin($dlat / 2) +
        cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
        sin($dlon / 2) * sin($dlon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $distance = $radius * $c;

    return $distance;
}
?>

<!-- Map Modal -->
<div id="mapModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold" id="mapModalTitle">Hospital Location</h3>
            <button id="closeMapModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="mapContainer" class="h-96 w-full rounded-lg border border-gray-300"></div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Map modal functionality
        const mapModal = document.getElementById('mapModal');
        const mapContainer = document.getElementById('mapContainer');
        const mapModalTitle = document.getElementById('mapModalTitle');
        const closeMapModal = document.getElementById('closeMapModal');
        let map = null;

        // Add click event to all map links
        document.querySelectorAll('.show-map-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const lat = parseFloat(this.getAttribute('data-lat'));
                const lng = parseFloat(this.getAttribute('data-lng'));
                const name = this.getAttribute('data-name');
                const address = this.getAttribute('data-address');

                if (isNaN(lat) || isNaN(lng)) {
                    alert('Location coordinates not available');
                    return;
                }

                // Show modal
                mapModal.classList.remove('hidden');
                mapModalTitle.textContent = name + ' - Location';

                // Initialize map if not already done
                if (map) {
                    map.remove();
                }

                // Create new map
                map = L.map(mapContainer).setView([lat, lng], 15);

                // Add OpenStreetMap tiles
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // Add marker
                L.marker([lat, lng]).addTo(map)
                    .bindPopup('<strong>' + name + '</strong><br>' + address)
                    .openPopup();

                // Force map to recalculate its size
                setTimeout(() => {
                    map.invalidateSize();
                }, 100);
            });
        });

        // Close modal
        closeMapModal.addEventListener('click', function() {
            mapModal.classList.add('hidden');
        });

        // Close modal when clicking outside
        mapModal.addEventListener('click', function(e) {
            if (e.target === mapModal) {
                mapModal.classList.add('hidden');
            }
        });
    });
</script>