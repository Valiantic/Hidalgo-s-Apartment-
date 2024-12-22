<?php
session_start();
include '../connections.php';

// Check if user is logged in and is a tenant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

// Get admin user
$admin_query = "SELECT id, fullname FROM users WHERE role = 'admin' LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);
$admin_id = $admin['id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: 70vh;
            overflow-y: auto;
        }
        .message-bubble {
            max-width: 70%;
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
        }
        .sender {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        .receiver {
            background-color: #e9ecef;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Chat with Admin (<?php echo htmlspecialchars($admin['fullname']); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="chat-container" id="chatContainer">
                    <?php
                    $message_query = "SELECT * FROM messages WHERE 
                        (sender_id = ? AND receiver_id = ?) OR
                        (sender_id = ? AND receiver_id = ?)
                        ORDER BY timestamp ASC";
                    $stmt = mysqli_prepare($conn, $message_query);
                    mysqli_stmt_bind_param($stmt, "iiii", $_SESSION['user_id'], $admin_id, $admin_id, $_SESSION['user_id']);
                    mysqli_stmt_execute($stmt);
                    $messages = mysqli_stmt_get_result($stmt);
                    
                    while($message = mysqli_fetch_assoc($messages)):
                    ?>
                        <div class="message-bubble <?php echo ($message['sender_id'] == $_SESSION['user_id']) ? 'sender' : 'receiver'; ?>">
                            <?php echo htmlspecialchars($message['message_text']); ?>
                            <br>
                            <small><?php echo date('M d, Y H:i', strtotime($message['timestamp'])); ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
                <form method="POST" action="./req/send-message.php" class="mt-3">
                    <input type="hidden" name="receiver_id" value="<?php echo $admin_id; ?>">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-scroll to bottom of chat
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Refresh chat every 5 seconds
        setInterval(function() {
            location.reload();
        }, 5000);
    </script>
</body>
</html>