<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../../connections.php";

if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];

    // Fetch tenant data to store in tenant_history
    $stmt = $conn->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tenant = $result->fetch_assoc();

        // Insert tenant data into tenant_history
        $insert_stmt = $conn->prepare(
            "INSERT INTO tenant_history (tenant_id, fullname, phone_number, work, downpayment, advance, electricity, water, units, move_in_date, move_out_date) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $move_out_date = date('Y-m-d'); // Set move-out date to current date
        $insert_stmt->bind_param(
            "issssssssss",
            $tenant['tenant_id'],
            $tenant['fullname'],
            $tenant['phone_number'],
            $tenant['work'],
            $tenant['downpayment'],
            $tenant['advance'],
            $tenant['electricity'],
            $tenant['water'],
            $tenant['units'],
            $tenant['move_in_date'],
            $move_out_date
        );
        $insert_stmt->execute();
        $insert_stmt->close();

        // Fetch user ID from the users table
        $user_stmt = $conn->prepare("SELECT id FROM users WHERE fullname = ? AND phone_number = ?");
        $user_stmt->bind_param("ss", $tenant['fullname'], $tenant['phone_number']);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $user_id = $user['id'];

            // Delete user record from the users table
            $delete_user_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $delete_user_stmt->bind_param("i", $user_id);
            $delete_user_stmt->execute();
            $delete_user_stmt->close();
        }

        $user_stmt->close();

        // Delete tenant from the main tenant table
        $delete_tenant_stmt = $conn->prepare("DELETE FROM tenant WHERE tenant_id = ?");
        $delete_tenant_stmt->bind_param("i", $tenant_id);
        $delete_tenant_stmt->execute();
        $delete_tenant_stmt->close();

        header("Location: ../tenants.php?success=Tenant and user credentials deleted successfully");
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
