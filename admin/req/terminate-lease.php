<?php
session_start();
include '../../connections.php';

// Verify admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

// Check for tenant_id in the request
if (!isset($_GET['tenant_id'])) {
    header('Location: ../tenants.php?error=No tenant selected for lease termination.');
    exit;
}

$tenant_id = $_GET['tenant_id'];

try {
    // Start a transaction
    $conn->begin_transaction();

    // Fetch tenant data
    $select_stmt = $conn->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
    $select_stmt->bind_param("i", $tenant_id);
    $select_stmt->execute();
    $result = $select_stmt->get_result();
    $tenant = $result->fetch_assoc();
    $select_stmt->close();

    if (!$tenant) {
        throw new Exception("Tenant not found.");
    }

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

    // Get the user_id associated with the tenant
    $user_id = $tenant['user_id'];

    // Delete from transaction_info table
    $delete_transaction_stmt = $conn->prepare("DELETE FROM transaction_info WHERE tenant_id = ?");
    $delete_transaction_stmt->bind_param("i", $tenant_id);
    $delete_transaction_stmt->execute();
    $delete_transaction_stmt->close();

    // Delete from tenant table
    $delete_tenant_stmt = $conn->prepare("DELETE FROM tenant WHERE tenant_id = ?");
    $delete_tenant_stmt->bind_param("i", $tenant_id);
    $delete_tenant_stmt->execute();
    $delete_tenant_stmt->close();

    // Delete from users table
    $delete_user_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_user_stmt->bind_param("i", $user_id);
    $delete_user_stmt->execute();
    $delete_user_stmt->close();

    // Commit the transaction
    $conn->commit();

    header('Location: ../tenants.php?success=Tenant lease successfully terminated.');
    exit;

} catch (Exception $e) {
    // Rollback transaction in case of an error
    $conn->rollback();
    $_SESSION['error_message'] = $e->getMessage();
    header('Location: ../tenants.php?error=An error occurred while terminating tenant lease.');
    exit;
}
?>