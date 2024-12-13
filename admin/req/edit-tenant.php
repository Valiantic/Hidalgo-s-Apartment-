<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../../connections.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve and sanitize input data
    $tenant_id = $_POST['tenant_id']; 
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $work = htmlspecialchars(trim($_POST['work']));
    $downpayment = htmlspecialchars(trim($_POST['downpayment']));
    $advance = htmlspecialchars(trim($_POST['advance']));
    $electricity = htmlspecialchars(trim($_POST['electricity']));
    $water = htmlspecialchars(trim($_POST['water']));
    $unit = htmlspecialchars(trim($_POST['units']));
    $move_in_date = htmlspecialchars(trim($_POST['move_in_date']));

    // if (empty($fullname) || empty($phone_number) || empty($work) || empty($downpayment) || empty($unit) || empty($move_in_date)) {
    //     die("All fields are required!");
    // }

    // Update tenant information in the database
    $query = "UPDATE tenant 
              SET fullname = ?, phone_number = ?, work = ?, downpayment = ?,  advance = ?, electricity = ?, water = ?, units = ?, move_in_date = ? 
              WHERE tenant_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssssssi", $fullname, $phone_number, $work, $downpayment, $advance, $electricity, $water, $unit, $move_in_date, $tenant_id);

    if ($stmt->execute()) {
        // Redirect or show success message
        header("Location: ../tenants.php?success=Record updated successfully!");
        exit;
    } else {
        die("Error updating record: " . $stmt->error);
    }

    $stmt->close();
    $conn->close();
} else {
    die("Invalid request!");
}
?>
