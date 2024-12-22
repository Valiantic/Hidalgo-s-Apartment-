<?php
session_start();
include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $query = "INSERT INTO messages (sender_id, receiver_id, message_text) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "iis", $sender_id, $receiver_id, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            // Redirect back to the chat with the same tenant
            header('Location: ../message-admin.php?tenant_id=' . $receiver_id);
        } else {
            echo "Error sending message.";
        }
    }
} else {
    header('Location: ../authentication/login.php');
}
exit();