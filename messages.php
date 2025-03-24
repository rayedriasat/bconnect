<?php
require_once 'includes/auth_middleware.php';

// Get request details if coming from donation request
$request_details = null;
if (isset($_GET['request']) && isset($_GET['auto_message'])) {
    $stmt = $conn->prepare("
        SELECT dr.*, h.name as hospital_name 
        FROM DonationRequest dr 
        JOIN Hospital h ON dr.hospital_id = h.hospital_id 
        WHERE dr.request_id = ?
    ");
    $stmt->execute([$_GET['request']]);
    $request_details = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send auto message if it's a new conversation
    if ($request_details && isset($_GET['auto_message'])) {
        $auto_message = "I can donate {$request_details['blood_type']} to {$request_details['hospital_name']}";
        $stmt = $conn->prepare("INSERT INTO Message (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$user['user_id'], $_GET['contact'], $auto_message]);

        // Redirect to remove auto_message parameter
        header('Location: ' . BASE_URL . '/messages.php?contact=' . $_GET['contact'] . '&request=' . $_GET['request']);
        exit();
    }
}

// Handle fulfill donation request
if (isset($_POST['fulfill_request']) && isset($_POST['request_id'])) {
    try {
        $conn->beginTransaction();

        // Get request details
        $stmt = $conn->prepare("SELECT * FROM DonationRequest WHERE request_id = ?");
        $stmt->execute([$_POST['request_id']]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($request) {
            // Insert into history
            $stmt = $conn->prepare("
                INSERT INTO DonationRequestHistory (
                    request_id, hospital_id, requester_id, blood_type, 
                    quantity, urgency, contact_person, contact_phone,
                    created_at, fulfilled_at, fulfilled_by, status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?, 'fulfilled'
                )
            ");
            $stmt->execute([
                $request['request_id'],
                $request['hospital_id'],
                $request['requester_id'],
                $request['blood_type'],
                $request['quantity'],
                $request['urgency'],
                $request['contact_person'],
                $request['contact_phone'],
                $request['created_at'],
                $donor['donor_id']
            ]);

            // Delete from active requests
            $stmt = $conn->prepare("DELETE FROM DonationRequest WHERE request_id = ?");
            $stmt->execute([$_POST['request_id']]);

            $conn->commit();

            // Send confirmation message
            $confirm_message = "I have fulfilled this blood donation request.";
            $stmt = $conn->prepare("INSERT INTO Message (sender_id, receiver_id, content) VALUES (?, ?, ?)");
            $stmt->execute([$user['user_id'], $_GET['contact'], $confirm_message]);

            // Redirect to messages
            header('Location: ' . BASE_URL . '/messages.php?contact=' . $_GET['contact'] . '&success=1');
            exit();
        }
    } catch (Exception $e) {
        $conn->rollBack();
        $error = "Failed to fulfill donation request";
    }
}

// Get list of users the current user has conversations with
$stmt = $conn->prepare("
    SELECT DISTINCT 
        CASE 
            WHEN m.sender_id = ? THEN m.receiver_id
            ELSE m.sender_id
        END as contact_id,
        CASE 
            WHEN m.sender_id = ? THEN r.name
            ELSE s.name
        END as contact_name,
        CASE 
            WHEN m.sender_id = ? THEN r.email
            ELSE s.email
        END as contact_email,
        MAX(m.sent_at) as last_message_time
    FROM Message m
    JOIN Users s ON m.sender_id = s.user_id
    JOIN Users r ON m.receiver_id = r.user_id
    WHERE m.sender_id = ? OR m.receiver_id = ?
    GROUP BY contact_id, contact_name, contact_email
    ORDER BY last_message_time DESC
");
$stmt->execute([$user['user_id'], $user['user_id'], $user['user_id'], $user['user_id'], $user['user_id']]);
$conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle new message to a new user
if (isset($_POST['new_message'])) {
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email = ?");
    $stmt->execute([$email]);
    $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($recipient) {
        // Redirect to the conversation with this user
        header('Location: ' . BASE_URL . '/messages.php?contact=' . $recipient['user_id']);
        exit();
    } else {
        $error = 'User not found';
    }
}

// Get selected conversation messages
$selected_contact = isset($_GET['contact']) ? intval($_GET['contact']) : null;
$messages = [];

if ($selected_contact) {
    // Remove the conversation existence check and donation request validation
    // Get messages for this conversation
    $stmt = $conn->prepare("
        SELECT m.*, 
            CASE WHEN m.sender_id = ? THEN 'sent' ELSE 'received' END as direction
        FROM Message m
        WHERE (sender_id = ? AND receiver_id = ?) 
           OR (sender_id = ? AND receiver_id = ?)
        ORDER BY m.sent_at ASC
    ");
    $stmt->execute([$user['user_id'], $user['user_id'], $selected_contact, $selected_contact, $user['user_id']]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get contact info
    $stmt = $conn->prepare("SELECT email, name FROM Users WHERE user_id = ?");
    $stmt->execute([$selected_contact]);
    $contact_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $contact_email = $contact_info['email'];
    $contact_name = $contact_info['name'];
}

// Handle sending message in conversation
if (isset($_POST['send_message']) && $selected_contact) {
    $content = trim($_POST['content']);

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO Message (sender_id, receiver_id, content) VALUES (?, ?, ?)");
        $stmt->execute([$user['user_id'], $selected_contact, $content]);

        // Redirect to avoid form resubmission
        header('Location: ' . BASE_URL . '/messages.php?contact=' . $selected_contact);
        exit();
    }
}

/**
 * Verifies if a donation request link is valid for messaging
 * 
 * @param int $request_id The donation request ID
 * @param int $contact_id The user ID to contact
 * @return bool True if the link is valid, false otherwise
 */
function verify_donation_request($request_id, $contact_id)
{
    global $conn, $user;

    try {
        // Check if the donation request exists and is active
        $stmt = $conn->prepare("
            SELECT dr.*, d.user_id as donor_user_id, r.user_id as requester_user_id
            FROM DonationRequest dr
            LEFT JOIN Donor d ON dr.donor_id = d.donor_id
            JOIN Users r ON dr.requester_id = r.user_id
            WHERE dr.request_id = ?
            AND dr.status IN ('pending', 'accepted')
            AND dr.expires_at > NOW()
        ");
        $stmt->execute([$request_id]);
        $request = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$request) {
            return false;
        }

        // Check if the contact is either the requester or the donor
        $valid_contact = ($contact_id == $request['requester_user_id'] ||
            $contact_id == $request['donor_user_id']);

        // Check if the current user is either the requester or the donor
        $valid_user = ($user['user_id'] == $request['requester_user_id'] ||
            $user['user_id'] == $request['donor_user_id']);

        return $valid_contact && $valid_user;
    } catch (PDOException $e) {
        // Log error if needed
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - BloodConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <?php require_once 'includes/navigation.php'; ?>

    <?php if (isset($_GET['success'])): ?>
        <div class="max-w-7xl mx-auto px-4 py-2">
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                Donation request has been successfully fulfilled!
            </div>
        </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <!-- Conversations List -->
            <div class="md:col-span-1 bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <form method="POST" class="space-y-2">
                        <input type="email" name="email" placeholder="Enter email to message"
                            class="w-full px-3 py-2 border rounded-lg text-sm" required>
                        <button type="submit" name="new_message"
                            class="w-full bg-red-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700">
                            New Message
                        </button>
                    </form>
                </div>

                <div class="divide-y">
                    <?php foreach ($conversations as $conv): ?>
                        <a href="?contact=<?php echo $conv['contact_id']; ?>"
                            class="block p-4 hover:bg-gray-50 <?php echo ($selected_contact == $conv['contact_id']) ? 'bg-gray-100' : ''; ?>">
                            <div class="font-medium"><?php echo htmlspecialchars($conv['contact_name']); ?></div>
                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($conv['contact_email']); ?></div>
                            <div class="text-xs text-gray-500">
                                <?php echo date('M j, Y g:i A', strtotime($conv['last_message_time'])); ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="md:col-span-3 bg-white rounded-lg shadow flex flex-col h-[calc(100vh-12rem)]">
                <?php if ($selected_contact): ?>
                    <!-- Contact Header -->
                    <div class="p-4 border-b">
                        <h2 class="font-medium"><?php echo htmlspecialchars($contact_name); ?></h2>
                        <div class="text-sm text-gray-600"><?php echo htmlspecialchars($contact_email); ?></div>
                    </div>

                    <!-- Messages -->
                    <div class="flex-1 overflow-y-auto p-4 space-y-4">
                        <?php foreach ($messages as $message): ?>
                            <div class="flex <?php echo $message['direction'] === 'sent' ? 'justify-end' : 'justify-start'; ?>">
                                <div class="max-w-[70%] <?php echo $message['direction'] === 'sent'
                                                            ? 'bg-red-600 text-white'
                                                            : 'bg-gray-200 text-gray-900'; ?> rounded-lg px-4 py-2">
                                    <p class="whitespace-pre-wrap"><?php echo htmlspecialchars($message['content']); ?></p>
                                    <span class="text-xs <?php echo $message['direction'] === 'sent'
                                                                ? 'text-red-100'
                                                                : 'text-gray-500'; ?> block mt-1">
                                        <?php echo date('g:i A', strtotime($message['sent_at'])); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Message Input -->
                    <form method="POST" class="p-4 border-t">
                        <div class="flex gap-2">
                            <input type="text" name="content" placeholder="Type a message..."
                                class="flex-1 px-4 py-2 border rounded-lg" required
                                autocomplete="off">
                            <button type="submit" name="send_message"
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700">
                                Send
                            </button>
                        </div>
                    </form>

                    <?php if (isset($_GET['request'])): ?>
                        <div class="mt-4 border-t pt-4">
                            <form method="POST" class="flex justify-end" onsubmit="return confirm('Are you sure you want to mark this donation request as fulfilled?');">
                                <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($_GET['request']); ?>">
                                <button type="submit"
                                    name="fulfill_request"
                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                    Fulfill Donation Request
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="flex-1 flex items-center justify-center text-gray-500">
                        Select a conversation or start a new one
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // Auto-scroll to bottom of messages
        function scrollToBottom() {
            const messagesDiv = document.querySelector('.overflow-y-auto');
            if (messagesDiv) {
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }
        }

        // Scroll on page load
        scrollToBottom();
    </script>
</body>

</html>