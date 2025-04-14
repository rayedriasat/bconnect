<?php
require_once '../../includes/auth_middleware.php';
require_once '../../classes/Location.php';
require_once '../../Core/functs.php';

$error = getFlashMessage('error');
$success = getFlashMessage('success');

// Initialize Location class
$locationManager = new Location($conn);

// Get current location
$currentLocation = $locationManager->getUserLocation($user['user_id']);

// Handle location update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_location'])) {
    try {
        if (empty($_POST['latitude']) || empty($_POST['longitude'])) {
            throw new Exception('Location data is required');
        }

        if ($locationManager->updateUserLocation(
            $user['user_id'],
            $_POST['latitude'],
            $_POST['longitude'],
            $_POST['address'] ?? ''
        )) {
            $_SESSION['success_message'] = 'Your location has been updated successfully';
            header('Location: ' . BASE_URL . '/views/profile/location.php');
            exit();
        } else {
            throw new Exception('Failed to update location');
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        header('Location: ' . BASE_URL . '/views/profile/location.php');
        exit();
    }
}

// Set page title
$pageTitle = 'Manage Location - BloodConnect';

// Add Leaflet CSS and JS
$additionalHeadContent = '
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
';

require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>

    <div class="max-w-4xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold mb-6">Manage Your Location</h2>

            <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

            <div class="mb-6">
                <p class="text-gray-600 mb-2">Your current location helps us match you with nearby donation requests and hospitals.</p>

                <?php if ($currentLocation): ?>
                    <div class="bg-gray-50 p-4 rounded mb-4">
                        <h3 class="font-medium text-gray-900 mb-2">Current Location</h3>
                        <p class="text-gray-700"><?php echo htmlspecialchars($currentLocation['address'] ?? 'Address not available'); ?></p>

                        <p class="text-sm text-gray-500 mt-1">Location ID: <?php echo htmlspecialchars($currentLocation['location_id']); ?></p>
                    </div>

                    <div id="map" class="h-64 rounded-lg border border-gray-300 mb-4"></div>
                <?php else: ?>
                    <div class="bg-yellow-50 p-4 rounded mb-4">
                        <p class="text-yellow-700">You haven't set your location yet. Please update your location below.</p>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Latitude</label>
                        <input type="text" id="latitude" name="latitude"
                            value="<?php echo htmlspecialchars($currentLocation['latitude'] ?? ''); ?>"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm"
                            readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Longitude</label>
                        <input type="text" id="longitude" name="longitude"
                            value="<?php echo htmlspecialchars($currentLocation['longitude'] ?? ''); ?>"
                            class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm"
                            readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Address</label>
                    <input type="text" id="address" name="address"
                        value="<?php echo htmlspecialchars($currentLocation['address'] ?? ''); ?>"
                        class="mt-1 block w-full rounded-md border-2 border-gray-300 shadow-sm"
                        readonly>
                </div>

                <div class="flex items-center justify-between">
                    <button type="button" id="getLocationBtn"
                        class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Update My Location
                    </button>

                    <button type="submit" name="update_location" value="1"
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize map if location exists
        <?php if ($currentLocation): ?>
            const map = L.map('map').setView([
                <?php echo $currentLocation['latitude']; ?>,
                <?php echo $currentLocation['longitude']; ?>
            ], 13);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            L.marker([
                    <?php echo $currentLocation['latitude']; ?>,
                    <?php echo $currentLocation['longitude']; ?>
                ]).addTo(map)
                .bindPopup('Your location')
                .openPopup();
        <?php endif; ?>

        // Get location button
        document.getElementById('getLocationBtn').addEventListener('click', function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;

                    // Reverse geocoding to get address
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${position.coords.latitude}&lon=${position.coords.longitude}`)
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('address').value = data.display_name;

                            // Update map if it exists
                            if (typeof map !== 'undefined') {
                                map.setView([position.coords.latitude, position.coords.longitude], 13);
                                L.marker([position.coords.latitude, position.coords.longitude]).addTo(map)
                                    .bindPopup('Your new location')
                                    .openPopup();
                            }

                            alert('Location captured successfully!');
                        });
                });
            } else {
                alert('Geolocation is not supported by this browser.');
            }
        });
    </script>
</body>

</html>