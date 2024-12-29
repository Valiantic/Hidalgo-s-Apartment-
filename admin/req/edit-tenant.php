<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../../connections.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenant_id = $_POST['tenant_id'];
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    $work = htmlspecialchars(trim($_POST['work']));
    $downpayment = htmlspecialchars(trim($_POST['downpayment']));
    $advance = htmlspecialchars(trim($_POST['advance']));
    $electricity = htmlspecialchars(trim($_POST['electricity']));
    $water = htmlspecialchars(trim($_POST['water']));
    $unit = htmlspecialchars(trim($_POST['units']));
  

    // Start a transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Update tenant table
        $tenant_query = "UPDATE tenant 
                         SET fullname = ?, phone_number = ?, work = ?, downpayment = ?, advance = ?, electricity = ?, water = ?, units = ?
                         WHERE tenant_id = ?";
        $tenant_stmt = $conn->prepare($tenant_query);
        $tenant_stmt->bind_param("ssssssssi", $fullname, $phone_number, $work, $downpayment, $advance, $electricity, $water, $unit, $tenant_id);
        $tenant_stmt->execute();

        // Update users table
        $user_query = "UPDATE users 
                       SET fullname = ?, phone_number = ?, work = ? 
                       WHERE id = (SELECT user_id FROM tenant WHERE tenant_id = ?)";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("sssi", $fullname, $phone_number, $work, $tenant_id);
        $user_stmt->execute();

        // Extract unit number from the unit name
        $unit_number = (int) filter_var($unit, FILTER_SANITIZE_NUMBER_INT);

        // Update transaction_info table if there is data in advance, electricity, and water
        if ($advance > 0 || $electricity > 0 || $water > 0) {
            $monthly_rent_status = $advance > 0 ? 'Paid' : 'Not Paid';
            $electricity_status = $electricity > 0 ? 'Paid' : 'Not Paid';
            $water_status = $water > 0 ? 'Paid' : 'Not Paid';

            $transaction_query = "REPLACE INTO transaction_info (tenant_id, unit, monthly_rent_status, electricity_status, water_status) 
                                  VALUES (?, ?, ?, ?, ?)";
            $transaction_stmt = $conn->prepare($transaction_query);
            $transaction_stmt->bind_param("iisss", $tenant_id, $unit_number, $monthly_rent_status, $electricity_status, $water_status);
            $transaction_stmt->execute();
        }

        $conn->commit();

        header("Location: ../tenants.php?success=Record updated successfully!");
    } catch (Exception $e) {
        $conn->rollback();
        die("Error updating records: " . $e->getMessage());
    }
}
?>
