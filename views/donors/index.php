<?php
require_once '../../includes/auth_middleware.php';
require_once '../../classes/Location.php';

// Initialize variables
$search = $_GET['search'] ?? '';
$bloodType = $_GET['blood_type'] ?? '';
$sortBy = $_GET['sort_by'] ?? 'name';
$sortOrder = $_GET['sort_order'] ?? 'asc';
$locationManager = new Location($conn);

// Get user's location for distance calculation
$userLocation = null;
if ($user) {
    $userLocation = $locationManager->getUserLocation($user['user_id']);
}

// Build the query
$query = "
    SELECT d.donor_id, d.blood_type, d.is_available, d.weight, 
           u.user_id, u.name, u.email, u.phone_number,
           l.latitude, l.longitude, l.address
    FROM Donor d
    JOIN Users u ON d.user_id = u.user_id
    LEFT JOIN Location l ON u.user_id = l.user_id
    WHERE 1=1
";

$params = [];

// Add search conditions
if (!empty($search)) {
    $query .= " AND (u.name LIKE ? OR u.email LIKE ? OR d.blood_type LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

if (!empty($bloodType)) {
    $query .= " AND d.blood_type = ?";
    $params[] = $bloodType;
}

// Add sorting - but for location sorting, we'll handle it after fetching the data
$validSortColumns = ['name', 'blood_type', 'address'];
$validSortOrders = ['asc', 'desc'];

if (!in_array($sortBy, $validSortColumns)) {
    $sortBy = 'name';
}

if (!in_array($sortOrder, $validSortOrders)) {
    $sortOrder = 'asc';
}

// Only add ORDER BY clause if not sorting by location/distance
if ($sortBy !== 'address') {
    $query .= " ORDER BY " . ($sortBy === 'name' ? 'u.name' : 'd.blood_type') . " $sortOrder";
}

// Execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate distances and sort by distance if needed
if ($sortBy === 'address' && $userLocation) {
    // Calculate distance for each donor
    foreach ($donors as &$donor) {
        if (!empty($donor['latitude']) && !empty($donor['longitude'])) {
            $donor['distance'] = $locationManager->calculateDistance(
                $userLocation['latitude'],
                $userLocation['longitude'],
                $donor['latitude'],
                $donor['longitude']
            );
        } else {
            // Donors without location will be at the end or beginning depending on sort order
            $donor['distance'] = $sortOrder === 'asc' ? PHP_FLOAT_MAX : -1;
        }
    }

    // Sort the array by distance
    usort($donors, function ($a, $b) use ($sortOrder) {
        if ($sortOrder === 'asc') {
            return $a['distance'] <=> $b['distance'];
        } else {
            return $b['distance'] <=> $a['distance'];
        }
    });
}

// Get unique blood types for filter dropdown
$bloodTypesStmt = $conn->query("SELECT DISTINCT blood_type FROM Donor ORDER BY blood_type");
$bloodTypes = $bloodTypesStmt->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Donor Directory - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>
    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Donor Directory</h2>

            <!-- Search and Filter Form -->
            <form method="GET" class="mb-8 bg-gray-50 p-4 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Name, Email, Blood Type"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Blood Type</label>
                        <select name="blood_type" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                            <option value="">All Blood Types</option>
                            <?php foreach ($bloodTypes as $type): ?>
                                <option value="<?php echo htmlspecialchars($type); ?>" <?php echo $bloodType === $type ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                        <select name="sort_by" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                            <option value="name" <?php echo $sortBy === 'name' ? 'selected' : ''; ?>>Name</option>
                            <option value="blood_type" <?php echo $sortBy === 'blood_type' ? 'selected' : ''; ?>>Blood Type</option>
                            <option value="address" <?php echo $sortBy === 'address' ? 'selected' : ''; ?>>Distance</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sort Order</label>
                        <select name="sort_order" class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring focus:ring-red-200">
                            <option value="asc" <?php echo $sortOrder === 'asc' ? 'selected' : ''; ?>>Ascending</option>
                            <option value="desc" <?php echo $sortOrder === 'desc' ? 'selected' : ''; ?>>Descending</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4 flex justify-end">
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700">
                        Apply Filters
                    </button>
                </div>
            </form>

            <!-- Results -->
            <div class="overflow-x-auto">
                <?php if (empty($donors)): ?>
                    <div class="text-center py-8">
                        <p class="text-gray-500">No donors found matching your criteria.</p>
                    </div>
                <?php else: ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Donor
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Blood Type
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Contact
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Location
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($donors as $donor): ?>
                                <?php $isSelf = ($donor['user_id'] == $user['user_id']); ?>
                                <tr class="<?php echo $isSelf ? 'bg-blue-50' : ''; ?>">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            <?php echo htmlspecialchars($donor['name']); ?>
                                            <?php if ($isSelf): ?>
                                                <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">
                                                    You
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                            <?php echo htmlspecialchars($donor['blood_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars($donor['email']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <?php echo htmlspecialchars($donor['phone_number']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php if (!empty($donor['address'])): ?>
                                                <?php
                                                // Truncate long addresses
                                                $address = $donor['address'];
                                                $displayAddress = htmlspecialchars(strlen($address) > 70 ? substr($address, 0, 70) . '...' : $address);
                                                ?>
                                                <a href="#" class="hover:text-blue-600 show-map-link"
                                                    data-lat="<?php echo htmlspecialchars($donor['latitude']); ?>"
                                                    data-lng="<?php echo htmlspecialchars($donor['longitude']); ?>"
                                                    data-address="<?php echo htmlspecialchars($donor['address']); ?>">
                                                    <?php echo $displayAddress; ?>
                                                </a>

                                                <?php if ($userLocation && !empty($donor['latitude']) && !empty($donor['longitude'])): ?>
                                                    <?php
                                                    // Calculate distance if both user and donor have locations
                                                    $distance = $locationManager->calculateDistance(
                                                        $userLocation['latitude'],
                                                        $userLocation['longitude'],
                                                        $donor['latitude'],
                                                        $donor['longitude']
                                                    );
                                                    ?>
                                                    <div class="text-xs text-gray-500 mt-1">
                                                        <i class="fas fa-map-marker-alt text-red-500"></i>
                                                        <?php echo number_format($distance, 1); ?> km away
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-500">Location not provided</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($donor['is_available']): ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Available
                                            </span>
                                        <?php else: ?>
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Not Available
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if (!$isSelf): ?>
                                            <a href="<?php echo BASE_URL; ?>/views/messages/index.php?contact=<?php echo $donor['user_id']; ?>"
                                                class="text-blue-600 hover:text-blue-900">
                                                <i class="fas fa-envelope mr-1"></i> Message
                                            </a>
                                        <?php else: ?>
                                            <span class="text-gray-400">
                                                <i class="fas fa-user mr-1"></i> Self
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>

<!-- Add this at the end of the file, before the closing </body> tag -->
<!-- Map Modal -->
<div id="mapModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-xl p-6 w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold" id="mapModalTitle">Location Map</h3>
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
                const address = this.getAttribute('data-address');

                if (isNaN(lat) || isNaN(lng)) {
                    alert('Location coordinates not available');
                    return;
                }

                // Show modal
                mapModal.classList.remove('hidden');
                mapModalTitle.textContent = 'Location: ' + address;

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
                    .bindPopup(address)
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