<?php
session_start();
include "../connections.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Get list of all tenants
$tenant_query = "SELECT * FROM tenant_users WHERE role = 'user'";
$tenant_result = mysqli_query($conn, $tenant_query);

// Get selected tenant's messages if any
$selected_tenant = isset($_GET['tenant_id']) ? $_GET['tenant_id'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .chat-container {
            height: 60vh;
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
        <div class="row">
            <!-- Tenant List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Tenants</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php while($tenant = mysqli_fetch_assoc($tenant_result)): ?>
                                <a href="?tenant_id=<?php echo $tenant['user_id']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo ($selected_tenant == $tenant['user_id']) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($tenant['fullname']); ?>
                                    <br>
                                    <small>Unit: <?php echo htmlspecialchars($tenant['units']); ?></small>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div class="col-md-8">
                <?php if($selected_tenant): ?>
                    <?php
                    $tenant_query = "SELECT fullname FROM users WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $tenant_query);
                    mysqli_stmt_bind_param($stmt, "i", $selected_tenant);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $tenant_name = mysqli_fetch_assoc($result)['fullname'];
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Chat with <?php echo htmlspecialchars($tenant_name); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="chat-container" id="chatContainer">
                                <?php
                                $message_query = "SELECT * FROM messages WHERE 
                                    (sender_id = ? AND receiver_id = ?) OR
                                    (sender_id = ? AND receiver_id = ?)
                                    ORDER BY timestamp ASC";
                                $stmt = mysqli_prepare($conn, $message_query);
                                mysqli_stmt_bind_param($stmt, "iiii", $_SESSION['user_id'], $selected_tenant, $selected_tenant, $_SESSION['user_id']);
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
                                <input type="hidden" name="receiver_id" value="<?php echo $selected_tenant; ?>">
                                <div class="input-group">
                                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Select a tenant to start messaging</div>
                <?php endif; ?>
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
            if (window.location.search.includes('tenant_id')) {
                location.reload();
            }
        }, 5000);
    </script>
</body>
</html>