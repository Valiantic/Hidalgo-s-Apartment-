<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../../connections.php";

if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];

    // Fetch tenant data to store in history
    $stmt = $conn->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tenant = $result->fetch_assoc();

        // Insert tenant data into tenant_history
        $insert_stmt = $conn->prepare(
            "INSERT INTO tenant_history (tenant_id, fullname, phone_number, work, downpayment, units, move_in_date, move_out_date) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $move_out_date = date('Y-m-d'); // Set move out date to current date
        $insert_stmt->bind_param(
            "isssssss",
            $tenant['tenant_id'],
            $tenant['fullname'],
            $tenant['phone_number'],
            $tenant['work'],
            $tenant['downpayment'],
            $tenant['units'],
            $tenant['move_in_date'],
            $move_out_date
        );
        $insert_stmt->execute();
        $insert_stmt->close();

        // Delete tenant from main table
        $delete_stmt = $conn->prepare("DELETE FROM tenant WHERE tenant_id = ?");
        $delete_stmt->bind_param("i", $tenant_id);
        $delete_stmt->execute();
        $delete_stmt->close();

        header("Location: ../tenants.php?success=Tenant deleted");
        exit;
    } else {
        die("Tenant not found!");
    }
    $stmt->close();
} else {
    die("No tenant ID provided!");
}

$conn->close();
?>
