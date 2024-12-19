<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $unit = $_POST['unit'];
    $status = $_POST['status'];

    $sql = "UPDATE maintenance_request SET status = ? WHERE unit = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $unit);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Maintenance status updated successfully!";
    } else {
        $_SESSION['error_message'] = "Failed to update maintenance status.";
    }

    header("Location: ../unit-maintenance.php?unit=" . urlencode($unit));
    exit;
}
?>
