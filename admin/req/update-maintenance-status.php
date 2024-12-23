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
        $_SESSION['success'] = "Maintenance status updated successfully!";
        // Extract the unit number from the unit name
        $unit_number = (int) filter_var($unit, FILTER_SANITIZE_NUMBER_INT);
        header("Location: ../unit-maintenance.php?unit=" . urlencode($unit_number) . "&success=" . urlencode("Maintenance status updated successfully!"));
        exit;
    } else {
        $_SESSION['error'] = "Failed to update maintenance status.";
        header("Location: ../unit-maintenance.php?unit=" . urlencode($unit_number) . "&error=" . urlencode("Failed to update maintenance status."));
        exit;
    }
}
?>
