<?php
require_once '../config/database.php';
require_once '../includes/notification_helper.php';

echo "<h1>BloodConnect Demo Data Generator</h1>";
echo "<p>Creating realistic data with Bangladesh context...</p>";

try {
    // Create database connection
    $db = new Database();
    $conn = $db->connect();

    // Store original AUTO_INCREMENT values before starting transaction
    $tables = [
        'Notification',
        'Message',
        'DonationAppointment',
        'Matches',
        'DonationRequestHistory',
        'DonationRequest',
        'BloodInventory',
        'Location',
        'Donor',
        'Hospital',
        'Admin',
        'Users'
    ];

    $originalAutoIncrements = [];
    foreach ($tables as $table) {
        $stmt = $conn->prepare("SHOW TABLE STATUS LIKE ?");
        $stmt->execute([$table]);
        $tableInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($tableInfo) {
            $originalAutoIncrements[$table] = $tableInfo['Auto_increment'];
        }
    }

    // Begin transaction
    $conn->beginTransaction();

    // Clear existing data (optional - comment out if you want to keep existing data)
    // echo "<h3>Clearing existing data...</h3>";
    // foreach ($tables as $table) {
    //     $conn->exec("DELETE FROM $table");
    //     echo "Cleared table: $table<br>";
    // }

    // // Reset auto-increment counters
    // foreach ($tables as $table) {
    //     $conn->exec("ALTER TABLE $table AUTO_INCREMENT = 1");
    // }

    // 1. Create users with Bangladeshi names
    echo "<h3>Creating users...</h3>";

    $firstNames = [
        'Mohammad',
        'Abdul',
        'Fatema',
        'Ayesha',
        'Rahim',
        'Nur',
        'Jahan',
        'Kamal',
        'Sabrina',
        'Tahmid',
        'Nasir',
        'Farida',
        'Imran',
        'Nusrat',
        'Rafiq',
        'Sadia',
        'Zahir',
        'Tasnim',
        'Farid',
        'Rabeya'
    ];

    $lastNames = [
        'Hossain',
        'Rahman',
        'Khan',
        'Chowdhury',
        'Ahmed',
        'Akter',
        'Begum',
        'Islam',
        'Alam',
        'Sultana',
        'Uddin',
        'Khatun',
        'Miah',
        'Siddique',
        'Jahan',
        'Molla',
        'Sarkar',
        'Talukder',
        'Huq',
        'Mahmud'
    ];

    $userIds = [];
    $donorIds = [];
    $adminIds = [];

    // Create admin users
    for ($i = 1; $i <= 3; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $name = "$firstName $lastName";
        $email = "admin$i@bloodconnect.com";
        $phone = "01" . rand(7, 9) . rand(10000000, 99999999); // Bangladeshi phone format

        $stmt = $conn->prepare("
            INSERT INTO Users (email, password_hash, name, phone_number)
            VALUES (?, ?, ?, ?)
        ");
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$email, $password, $name, $phone]);
        $userId = $conn->lastInsertId();
        $userIds[] = $userId;

        // Create admin record
        $stmt = $conn->prepare("INSERT INTO Admin (user_id) VALUES (?)");
        $stmt->execute([$userId]);
        $adminIds[] = $conn->lastInsertId();

        echo "Created admin user: $name ($email)<br>";
    }

    // Create donor users
    for ($i = 1; $i <= 15; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $name = "$firstName $lastName";
        $email = "donor$i@example.com";
        $phone = "01" . rand(7, 9) . rand(10000000, 99999999);

        $stmt = $conn->prepare("
            INSERT INTO Users (email, password_hash, name, phone_number)
            VALUES (?, ?, ?, ?)
        ");
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$email, $password, $name, $phone]);
        $userId = $conn->lastInsertId();
        $userIds[] = $userId;

        // Create donor profile
        $bloodTypes = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
        // Weight distribution for Bangladesh (typically 50-80kg)
        $weight = rand(50, 80);
        // Calculate a birthdate for someone between 18-60 years old
        $birthYear = date('Y') - rand(18, 60);
        $birthMonth = rand(1, 12);
        $birthDay = rand(1, 28);
        $dob = "$birthYear-$birthMonth-$birthDay";

        $stmt = $conn->prepare("
            INSERT INTO Donor (user_id, blood_type, date_of_birth, weight, is_available, last_donation_date, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");

        // Some donors have donated recently, others haven't
        $lastDonation = (rand(0, 1) == 1) ? date('Y-m-d', strtotime('-' . rand(30, 180) . ' days')) : NULL;
        $isAvailable = rand(0, 3) > 0 ? 1 : 0; // 75% chance of being available

        $stmt->execute([
            $userId,
            $bloodTypes[array_rand($bloodTypes)],
            $dob,
            $weight,
            $isAvailable,
            $lastDonation
        ]);
        $donorIds[] = $conn->lastInsertId();

        echo "Created donor: $name ($email)<br>";
    }

    // Create regular users
    for ($i = 1; $i <= 10; $i++) {
        $firstName = $firstNames[array_rand($firstNames)];
        $lastName = $lastNames[array_rand($lastNames)];
        $name = "$firstName $lastName";
        $email = "user$i@example.com";
        $phone = "01" . rand(7, 9) . rand(10000000, 99999999);

        $stmt = $conn->prepare("
            INSERT INTO Users (email, password_hash, name, phone_number)
            VALUES (?, ?, ?, ?)
        ");
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$email, $password, $name, $phone]);
        $userId = $conn->lastInsertId();
        $userIds[] = $userId;

        echo "Created regular user: $name ($email)<br>";
    }

    // 2. Create hospitals in Bangladesh
    echo "<h3>Creating hospitals...</h3>";

    $hospitals = [
        ['Dhaka Medical College Hospital', 'Secretariat Road, Dhaka 1000', 'dmch@example.com', '01712345678', 23.7272, 90.3854],
        ['Bangabandhu Sheikh Mujib Medical University', 'Shahbag, Dhaka 1000', 'bsmmu@example.com', '01712345679', 23.7399, 90.3721],
        ['Square Hospital', '18/F West Panthapath, Dhaka 1205', 'square@example.com', '01712345680', 23.7465, 90.3760],
        ['Chittagong Medical College Hospital', 'K.B. Fazlul Kader Road, Chittagong', 'cmch@example.com', '01712345681', 22.3569, 91.8317],
        ['Rajshahi Medical College Hospital', 'Medical College Road, Rajshahi', 'rmch@example.com', '01712345682', 24.3636, 88.6241],
        ['Sylhet MAG Osmani Medical College Hospital', 'Medical College Road, Sylhet', 'somch@example.com', '01712345683', 24.8949, 91.8687],
        ['Khulna Medical College Hospital', 'KDA Avenue, Khulna', 'kmch@example.com', '01712345684', 22.8456, 89.5403],
        ['Ibn Sina Hospital', 'House-48, Road-9/A, Dhanmondi, Dhaka', 'ibnsina@example.com', '01712345685', 23.7977, 90.3557],
        ['Evercare Hospital Dhaka', 'Plot 81, Block E, Bashundhara R/A, Dhaka', 'evercare@example.com', '01712345686', 23.8103, 90.4125],
        ['United Hospital Limited', 'Plot 15, Road 71, Gulshan, Dhaka', 'united@example.com', '01712345687', 23.8018, 90.4189]
    ];

    $hospitalIds = [];

    foreach ($hospitals as $hospital) {
        $stmt = $conn->prepare("
            INSERT INTO Hospital (name, address, email, phone_number)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$hospital[0], $hospital[1], $hospital[2], $hospital[3]]);
        $hospitalId = $conn->lastInsertId();
        $hospitalIds[] = $hospitalId;

        // Add hospital location to Location table
        $stmt = $conn->prepare("
            INSERT INTO Location (hospital_id, latitude, longitude, address)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$hospitalId, $hospital[4], $hospital[5], $hospital[1]]);

        echo "Created hospital: {$hospital[0]} with location data<br>";
    }

    // Function to get address from OpenStreetMap reverse geocoding
    function getAddressFromCoordinates($lat, $lng)
    {
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lng}&zoom=18&addressdetails=1";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'BloodConnect Demo Data Generator');

        $response = curl_exec($ch);
        curl_close($ch);

        $data = json_decode($response, true);

        if (isset($data['display_name'])) {
            return $data['display_name'];
        } else {
            // Fallback if API fails
            return "Address near coordinates: $lat, $lng";
        }
    }

    // 3. Create locations for donors
    echo "<h3>Creating donor locations...</h3>";

    // Major cities in Bangladesh with coordinates
    $cities = [
        ['Dhaka', 23.8103, 90.4125],
        ['Chittagong', 22.3569, 91.7832],
        ['Rajshahi', 24.3636, 88.6283],
        ['Khulna', 22.8456, 89.5403],
        ['Sylhet', 24.8949, 91.8687],
        ['Barisal', 22.7010, 90.3535],
        ['Rangpur', 25.7439, 89.2752],
        ['Comilla', 23.4607, 91.1809],
        ['Mymensingh', 24.7471, 90.4203],
        ['Narayanganj', 23.6238, 90.5000]
    ];

    foreach ($userIds as $userId) {
        // 80% chance of having location data
        if (rand(1, 100) <= 80) {
            $city = $cities[array_rand($cities)];

            // Add slight randomness to coordinates to spread users around the city
            $lat = $city[1] + (rand(-1000, 1000) / 10000);
            $lng = $city[2] + (rand(-1000, 1000) / 10000);

            // Get real address from OpenStreetMap
            $address = getAddressFromCoordinates($lat, $lng);
            $locationName = "{$city[0]} Residence";

            // Add a small delay to avoid hitting rate limits
            usleep(500000); // 0.5 second delay

            $stmt = $conn->prepare("
                INSERT INTO Location (user_id, latitude, longitude, address, location_name)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([$userId, $lat, $lng, $address, $locationName]);

            echo "Created location for user ID $userId with real address in {$city[0]}<br>";
        }
    }

    // 4. Create blood inventory for hospitals
    echo "<h3>Creating blood inventory...</h3>";

    $bloodTypes = ['A+', 'B+', 'O+', 'AB+', 'A-', 'B-', 'O-', 'AB-'];

    foreach ($hospitalIds as $hospitalId) {
        foreach ($bloodTypes as $bloodType) {
            // Bangladesh has higher prevalence of B+ and O+
            $baseQuantity = 10;
            if ($bloodType == 'B+') $baseQuantity = 25;
            if ($bloodType == 'O+') $baseQuantity = 20;
            if ($bloodType == 'A+') $baseQuantity = 15;

            // Negative blood types are less common
            if (strpos($bloodType, '-') !== false) {
                $baseQuantity = max(5, floor($baseQuantity / 3));
            }

            $quantity = rand(max(0, $baseQuantity - 10), $baseQuantity + 10);

            $stmt = $conn->prepare("
                INSERT INTO BloodInventory (hospital_id, blood_type, quantity, last_updated)
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$hospitalId, $bloodType, $quantity]);

            echo "Added $quantity units of $bloodType blood to hospital ID $hospitalId<br>";
        }
    }

    // 5. Create donation requests
    echo "<h3>Creating donation requests...</h3>";

    $urgencyLevels = ['low', 'medium', 'high'];
    $requestIds = [];

    // Create 20 donation requests
    for ($i = 1; $i <= 20; $i++) {
        $hospitalId = $hospitalIds[array_rand($hospitalIds)];
        $requesterId = $userIds[array_rand($userIds)];
        $bloodType = $bloodTypes[array_rand($bloodTypes)];
        $quantity = rand(1, 5);
        $urgency = $urgencyLevels[array_rand($urgencyLevels)];

        // Create a Bangladeshi contact person name
        $contactFirstName = $firstNames[array_rand($firstNames)];
        $contactLastName = $lastNames[array_rand($lastNames)];
        $contactPerson = "Dr. $contactFirstName $contactLastName";

        // Bangladeshi phone format
        $contactPhone = "01" . rand(7, 9) . rand(10000000, 99999999);

        // Request created between 30 days ago and now
        $createdDaysAgo = rand(0, 30);
        $createdAt = date('Y-m-d H:i:s', strtotime("-$createdDaysAgo days"));

        $stmt = $conn->prepare("
            INSERT INTO DonationRequest 
            (hospital_id, requester_id, blood_type, quantity, urgency, 
             contact_person, contact_phone, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $hospitalId,
            $requesterId,
            $bloodType,
            $quantity,
            $urgency,
            $contactPerson,
            $contactPhone,
            $createdAt
        ]);
        $requestId = $conn->lastInsertId();
        $requestIds[] = $requestId;

        echo "Created donation request #$requestId for $quantity units of $bloodType blood<br>";
    }

    // 6. Create matches between donors and requests
    echo "<h3>Creating donor-request matches...</h3>";

    foreach ($requestIds as $requestId) {
        // Get request details
        $stmt = $conn->prepare("SELECT blood_type FROM DonationRequest WHERE request_id = ?");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        // Find compatible donors
        $stmt = $conn->prepare("
            SELECT d.donor_id 
            FROM Donor d 
            WHERE d.blood_type = ?
        ");
        $stmt->execute([$request['blood_type']]);
        $compatibleDonors = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($compatibleDonors)) {
            // Create 2-5 matches per request
            $matchCount = min(count($compatibleDonors), rand(2, 5));
            $selectedDonors = array_rand(array_flip($compatibleDonors), $matchCount);

            if (!is_array($selectedDonors)) {
                $selectedDonors = [$selectedDonors];
            }

            foreach ($selectedDonors as $donorId) {
                $score = rand(50, 100);

                $stmt = $conn->prepare("
                    INSERT INTO Matches (request_id, donor_id, score)
                    VALUES (?, ?, ?)
                ");
                $stmt->execute([$requestId, $donorId, $score]);

                echo "Created match between request #$requestId and donor #$donorId with score $score<br>";
            }
        }
    }

    // 7. Create appointments
    echo "<h3>Creating donation appointments...</h3>";

    $statuses = ['pending', 'confirmed', 'completed', 'cancelled'];

    foreach ($requestIds as $requestId) {
        // Get request details
        $stmt = $conn->prepare("
            SELECT dr.*, h.name as hospital_name 
            FROM DonationRequest dr
            JOIN Hospital h ON dr.hospital_id = h.hospital_id
            WHERE dr.request_id = ?
        ");
        $stmt->execute([$requestId]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        // Find matches for this request
        $stmt = $conn->prepare("
            SELECT m.donor_id 
            FROM Matches m 
            WHERE m.request_id = ?
        ");
        $stmt->execute([$requestId]);
        $matches = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($matches)) {
            // Create 1-3 appointments per request
            $appointmentCount = min(count($matches), rand(1, 3));
            $selectedDonors = array_rand(array_flip($matches), $appointmentCount);

            if (!is_array($selectedDonors)) {
                $selectedDonors = [$selectedDonors];
            }

            foreach ($selectedDonors as $donorId) {
                // Schedule between now and 14 days in the future
                $daysInFuture = rand(-7, 14); // Some in the past, some in future
                $scheduledTime = date('Y-m-d H:i:s', strtotime("+$daysInFuture days"));

                // Status logic: past appointments are completed/cancelled, future are pending/confirmed
                $status = $daysInFuture < 0
                    ? ($statuses[array_rand(array_slice($statuses, 2, 2))]) // completed or cancelled
                    : ($statuses[array_rand(array_slice($statuses, 0, 2))]); // pending or confirmed

                $stmt = $conn->prepare("
                    INSERT INTO DonationAppointment 
                    (request_id, donor_id, scheduled_time, status, created_at)
                    VALUES (?, ?, ?, ?, NOW())
                ");
                $stmt->execute([$requestId, $donorId, $scheduledTime, $status]);
                $appointmentId = $conn->lastInsertId();

                echo "Created $status appointment #$appointmentId for request #$requestId with donor #$donorId<br>";

                // Create notifications for this appointment
                if ($status == 'confirmed' || $status == 'completed') {
                    notifyAppointmentStatusChange($conn, $appointmentId, $status);
                    echo "Created notifications for appointment #$appointmentId<br>";
                }
            }
        }
    }

    // 8. Create donation history (fulfilled requests)
    echo "<h3>Creating donation history...</h3>";

    // Select some completed appointments to move to history
    $stmt = $conn->prepare("
        SELECT da.*, dr.* 
        FROM DonationAppointment da
        JOIN DonationRequest dr ON da.request_id = dr.request_id
        WHERE da.status = 'completed'
        LIMIT 5
    ");
    $stmt->execute();
    $completedAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($completedAppointments as $appointment) {
        // Move to history
        $stmt = $conn->prepare("
            INSERT INTO DonationRequestHistory
            (request_id, hospital_id, requester_id, blood_type, quantity, urgency, 
             contact_person, contact_phone, created_at, fulfilled_at, fulfilled_by, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'fulfilled')
        ");
        $stmt->execute([
            $appointment['request_id'],
            $appointment['hospital_id'],
            $appointment['requester_id'],
            $appointment['blood_type'],
            $appointment['quantity'],
            $appointment['urgency'],
            $appointment['contact_person'],
            $appointment['contact_phone'],
            $appointment['created_at'],
            $appointment['donor_id']
        ]);

        echo "Moved request #{$appointment['request_id']} to donation history<br>";

        // Create fulfillment notification
        $stmt = $conn->prepare("
            SELECT u.user_id FROM Users u
            JOIN Donor d ON u.user_id = d.user_id
            WHERE d.donor_id = ?
        ");
        $stmt->execute([$appointment['donor_id']]);
        $donorUserId = $stmt->fetch(PDO::FETCH_COLUMN);

        $fulfillmentMessage = "Thank you for your blood donation! Your contribution has helped save lives.";
        sendInAppNotification($conn, $donorUserId, $fulfillmentMessage);

        echo "Created fulfillment notification for donor user #$donorUserId<br>";
    }

    // 9. Create additional notifications
    echo "<h3>Creating additional notifications...</h3>";

    $notificationMessages = [
        "Your donation appointment at Dhaka Medical College Hospital has been confirmed",
        "New blood request matching your B+ type in Chittagong",
        "Thank you for your recent donation at Square Hospital",
        "Reminder: Your donation appointment at Rajshahi Medical College Hospital is tomorrow",
        "Admin message: Your donation offer needs verification",
        "URGENT: O+ blood needed at Chittagong Medical College Hospital",
        "Today's appointment: Ibn Sina Hospital at 2:30 PM",
        "Appointment cancelled: Sylhet MAG Osmani Medical College Hospital",
        "Request fulfilled: Your AB+ donation at Khulna Medical College Hospital",
        "Donor found for your request at Bangabandhu Sheikh Mujib Medical University"
    ];

    foreach ($userIds as $userId) {
        // Create 1-5 random notifications per user
        $notificationCount = rand(1, 5);

        for ($i = 0; $i < $notificationCount; $i++) {
            $message = $notificationMessages[array_rand($notificationMessages)];
            $daysAgo = rand(0, 30);
            $sentAt = date('Y-m-d H:i:s', strtotime("-$daysAgo days"));
            $isRead = rand(0, 1);

            $stmt = $conn->prepare("
                INSERT INTO Notification (user_id, message, type, is_read, sent_at)
                VALUES (?, ?, 'in-app', ?, ?)
            ");
            $stmt->execute([$userId, $message, $isRead, $sentAt]);

            echo "Created notification for user #$userId<br>";
        }
    }

    // 10. Create messages between users
    echo "<h3>Creating messages between users...</h3>";

    $messageTemplates = [
        "Hello, I saw your blood donation request. I'd like to help.",
        "Thank you for offering to donate. When would be a good time?",
        "Is the donation center at {hospital} easy to find?",
        "I have some questions about the donation process.",
        "Can you please confirm the appointment details?",
        "I might be running 10 minutes late for my appointment.",
        "Do I need to bring any identification documents?",
        "How long will the donation process take?",
        "I've donated before at {hospital}. The staff there is excellent.",
        "Thank you for your quick response!"
    ];

    // Create 30 random messages between users
    for ($i = 0; $i < 30; $i++) {
        $senderId = $userIds[array_rand($userIds)];

        // Don't send message to self
        do {
            $receiverId = $userIds[array_rand($userIds)];
        } while ($senderId == $receiverId);

        $messageTemplate = $messageTemplates[array_rand($messageTemplates)];
        $hospital = $hospitals[array_rand($hospitals)][0];
        $message = str_replace('{hospital}', $hospital, $messageTemplate);

        $daysAgo = rand(0, 30);
        $sentAt = date('Y-m-d H:i:s', strtotime("-$daysAgo days"));

        $stmt = $conn->prepare("
            INSERT INTO Message (sender_id, receiver_id, content, sent_at)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$senderId, $receiverId, $message, $sentAt]);

        echo "Created message from user #$senderId to user #$receiverId<br>";
    }

    // Commit all changes
    $conn->commit();

    echo "<h2>Demo data creation complete!</h2>";
    echo "<p>You can now log in with the following credentials:</p>";
    echo "<ul>";
    echo "<li>Admin: admin1@bloodconnect.com / password123</li>";
    echo "<li>Donor: donor1@example.com / password123</li>";
    echo "<li>Regular user: user1@example.com / password123</li>";
    echo "</ul>";
} catch (Exception $e) {
    // Roll back transaction on error
    if (isset($conn)) {
        $conn->rollBack();

        // Restore original AUTO_INCREMENT values
        if (!empty($originalAutoIncrements)) {
            echo "<h3>Restoring AUTO_INCREMENT values...</h3>";
            foreach ($originalAutoIncrements as $table => $value) {
                try {
                    $conn->exec("ALTER TABLE $table AUTO_INCREMENT = $value");
                    echo "Restored AUTO_INCREMENT for $table to $value<br>";
                } catch (Exception $restoreError) {
                    echo "Failed to restore AUTO_INCREMENT for $table: " . $restoreError->getMessage() . "<br>";
                }
            }
        }
    }

    echo "<h2>Error:</h2>";
    echo "<p>" . $e->getMessage() . "</p>";
}
