<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Get the logged-in user ID
    $unit = $_POST['unit']; // Get the unit
    $targetDir = "uploads";
    $fileName = basename($_FILES["validId"]["name"]);
    $targetFilePath = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["validId"]["tmp_name"], $targetFilePath)) {
        // Insert or update the record in pending_users table
        $stmt = $conn->prepare("INSERT INTO pending_users (fullname, phone_number, work, email, password, role) 
                                SELECT fullname, phone_number, work, email, password, role
                                FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Request submitted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to upload ID.']);
    }
}



?>