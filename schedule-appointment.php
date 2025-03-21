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

// Get list of hospitals
$stmt = $conn->prepare("
    SELECT hospital_id, name, address, phone_number 
    FROM Hospital 
    ORDER BY name
");
$stmt->execute();
$hospitals = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$success = '';

// Handle appointment scheduling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hospital_id = $_POST['hospital_id'];
    $scheduled_date = $_POST['date'];
    $scheduled_time = $_POST['time'];

    // Combine date and time
    $scheduled_datetime = $scheduled_date . ' ' . $scheduled_time;

    // Validate datetime
    $scheduled_timestamp = strtotime($scheduled_datetime);
    $current_timestamp = time();

    if ($scheduled_timestamp < $current_timestamp) {
        $error = 'Cannot schedule appointments in the past';
    } else {
        try {
            $stmt = $conn->prepare("
                INSERT INTO Appointment (donor_id, hospital_id, scheduled_time, status)
                VALUES (?, ?, ?, 'pending')
            ");
            $stmt->execute([$donor['donor_id'], $hospital_id, $scheduled_datetime]);

            $success = 'Appointment scheduled successfully!';

            // Redirect to appointments page after 2 seconds
            header("refresh:2;url=" . BASE_URL . "/appointments.php");
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
    <title>Schedule Appointment - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once 'includes/navigation.php'; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-2xl font-semibold mb-6">Schedule New Appointment</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Hospital</label>
                    <select name="hospital_id" required class="w-full rounded-md border-gray-300 shadow-sm">
                        <option value="">Choose a hospital...</option>
                        <?php foreach ($hospitals as $hospital): ?>
                            <option value="<?php echo $hospital['hospital_id']; ?>">
                                <?php echo htmlspecialchars($hospital['name']); ?> -
                                <?php echo htmlspecialchars($hospital['address']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

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
                    <a href="<?php echo BASE_URL; ?>/appointments.php"
                        class="text-gray-600 hover:text-gray-800">
                        Back to Appointments
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