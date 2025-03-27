<?php
require_once '../../includes/auth_middleware.php';
require_once '../../Core/functs.php';
require_once '../../includes/notification_helper.php';

// Redirect if not a donor
if (!$isDonor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

// Get request ID from URL
$request_id = $_GET['request_id'] ?? 0;

// Get donor details
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donor) {
    $_SESSION['error_message'] = 'Donor profile not found';
} else {
    // Get request details
    $stmt = $conn->prepare("
        SELECT dr.*, h.name as hospital_name, h.address as hospital_address
        FROM DonationRequest dr
        JOIN Hospital h ON dr.hospital_id = h.hospital_id
        WHERE dr.request_id = ?
    ");
    $stmt->execute([$request_id]);
    $request = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$request) {
        $_SESSION['error_message'] = 'Donation request not found';
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduled_time = $_POST['scheduled_date'] . ' ' . $_POST['scheduled_time'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO DonationAppointment 
            (request_id, donor_id, scheduled_time, status) 
            VALUES (?, ?, ?, 'pending')
        ");
        $params = [
            $request_id,
            $donor['donor_id'],
            $scheduled_time
        ];

        // After successfully creating the appointment
        if ($stmt->execute($params)) {
            $appointment_id = $conn->lastInsertId();

            // Notify the requester about the new appointment
            notifyRequesterAboutAppointment($conn, $appointment_id);

            // Get requester information to establish communication
            $stmt = $conn->prepare("
                SELECT dr.requester_id 
                FROM DonationRequest dr
                WHERE dr.request_id = ?
            ");
            $stmt->execute([$request_id]);
            $requester = $stmt->fetch(PDO::FETCH_ASSOC);

            // Send initial message to establish communication
            if ($requester) {
                $initialMessage = "Hello, I've scheduled an appointment to donate blood for your request. Please let me know if you need any additional information from me.";

                $stmt = $conn->prepare("
                    INSERT INTO Message (sender_id, receiver_id, content, sent_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmt->execute([
                    $user['user_id'],
                    $requester['requester_id'],
                    $initialMessage
                ]);
            }

            $_SESSION['success_message'] = 'Appointment has been successfully scheduled!';
            header('Location: ' . BASE_URL . '/views/appointments/index.php');
            exit();
        } else {
            // Set flash message instead of passing via GET
            $_SESSION['success_message'] = 'Appointment scheduled successfully! The requester has been notified.';
        }

        // Redirect to appointments page
        header('Location: ' . BASE_URL . '/views/appointments/index.php');
        exit();
    } catch (Exception $e) {
        $_SESSION['error_message'] = 'Failed to schedule appointment: ' . $e->getMessage();
    }
}

$pageTitle = 'Schedule Donation Appointment - BloodConnect';
require_once __DIR__ . '/../../includes/header.php';
?>

<body class="bg-gray-100">
    <?php require_once __DIR__ . '/../../includes/navigation.php'; ?>
    <?php require_once __DIR__ . '/../../includes/_alerts.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Schedule Donation Appointment</h2>

            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2">Request Details</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Hospital:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($request['hospital_address']); ?></p>
                    <p><strong>Blood Type:</strong> <?php echo $request['blood_type']; ?></p>
                    <p><strong>Units Needed:</strong> <?php echo $request['quantity']; ?></p>
                    <p><strong>Requested:</strong> <?php echo date('M j, Y g:i A', strtotime($request['created_at'])); ?></p>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Date</label>
                        <input type="date" name="scheduled_date" required
                            min="<?php echo date('Y-m-d'); ?>" class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Scheduled Time</label>
                        <input type="time" name="scheduled_time" required class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="<?php echo BASE_URL; ?>/views/requests/index.php"
                        class="text-gray-600 hover:text-gray-800">
                        Back to Requests
                    </a>
                    <button type="submit"
                        class="bg-red-600 text-white px-6 py-2 rounded hover:bg-red-700">
                        Schedule Appointment
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>