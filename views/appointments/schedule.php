<?php
require_once 'includes/auth_middleware.php';

// Redirect if not a donor
if (!$isDonor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

// Get donor details
$stmt = $conn->prepare("SELECT * FROM Donor WHERE user_id = ?");
$stmt->execute([$user['user_id']]);
$donor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$donor) {
    header('Location: ' . BASE_URL . '/dashboard.php');
    exit();
}

// Get request details
if (!isset($_GET['request_id'])) {
    header('Location: ' . BASE_URL . '/donation-requests.php');
    exit();
}

$stmt = $conn->prepare("
    SELECT dr.*, h.name as hospital_name, h.address as hospital_address 
    FROM DonationRequest dr
    JOIN Hospital h ON dr.hospital_id = h.hospital_id
    WHERE dr.request_id = ? AND dr.blood_type = ?
");
$stmt->execute([$_GET['request_id'], $donor['blood_type']]);
$request = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$request) {
    header('Location: ' . BASE_URL . '/donation-requests.php?error=invalid_request');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $scheduled_date = $_POST['date'];
    $scheduled_time = $_POST['time'];
    $scheduled_datetime = $scheduled_date . ' ' . $scheduled_time;

    if (strtotime($scheduled_datetime) < time()) {
        $error = 'Cannot schedule appointments in the past';
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO DonationAppointment (
                    request_id, donor_id, scheduled_time, status
                ) VALUES (?, ?, ?, 'pending')
            ");
            $stmt->execute([
                $request['request_id'],
                $donor['donor_id'],
                $scheduled_datetime
            ]);

            // Send notification to requester
            $stmt = $conn->prepare("
                INSERT INTO Message (sender_id, receiver_id, content) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $user['user_id'],
                $request['requester_id'],
                "A donor has scheduled an appointment for your blood donation request."
            ]);

            header('Location: ' . BASE_URL . '/appointments.php?success=1');
            exit();
        } catch (Exception $e) {
            $error = 'Failed to schedule appointment. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Donation Appointment - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once 'includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Schedule Donation Appointment</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="mb-6">
                <h3 class="text-lg font-medium mb-2">Request Details</h3>
                <div class="bg-gray-50 p-4 rounded">
                    <p><strong>Hospital:</strong> <?php echo htmlspecialchars($request['hospital_name']); ?></p>
                    <p><strong>Address:</strong> <?php echo htmlspecialchars($request['hospital_address']); ?></p>
                    <p><strong>Blood Type:</strong> <?php echo $request['blood_type']; ?></p>
                    <p><strong>Units Needed:</strong> <?php echo $request['quantity']; ?></p>
                </div>
            </div>

            <form method="POST" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Date</label>
                        <input type="date"
                            name="date"
                            required
                            min="<?php echo date('Y-m-d'); ?>"
                            class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Time</label>
                        <input type="time"
                            name="time"
                            required
                            class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4">
                    <a href="<?php echo BASE_URL; ?>/donation-requests.php"
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