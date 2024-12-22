<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../../connections.php";

if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // First, fetch tenant data
        $stmt = $conn->prepare("SELECT t.*, u.id as user_id 
                              FROM tenant t 
                              LEFT JOIN users u ON t.fullname = u.fullname 
                              AND t.phone_number = u.phone_number 
                              WHERE t.tenant_id = ?");
        $stmt->bind_param("i", $tenant_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $tenant = $result->fetch_assoc();
            
            // 1. Insert into tenant_history
            $insert_stmt = $conn->prepare(
                "INSERT INTO tenant_history (tenant_id, fullname, phone_number, work, 
                                          downpayment, advance, electricity, water, 
                                          units, move_in_date, move_out_date) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $move_out_date = date('Y-m-d');
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
            
            // 2. Delete from transaction_info
            $delete_transaction_stmt = $conn->prepare("DELETE FROM transaction_info WHERE tenant_id = ?");
            $delete_transaction_stmt->bind_param("i", $tenant_id);
            $delete_transaction_stmt->execute();
            
            // 3. Delete from messages (using user_id)
            if ($tenant['user_id']) {
                $delete_message_stmt = $conn->prepare("DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?");
                $delete_message_stmt->bind_param("ii", $tenant['user_id'], $tenant['user_id']);
                $delete_message_stmt->execute();
            }
            
            // 4. Delete from tenant
            $delete_tenant_stmt = $conn->prepare("DELETE FROM tenant WHERE tenant_id = ?");
            $delete_tenant_stmt->bind_param("i", $tenant_id);
            $delete_tenant_stmt->execute();
            
            // 5. Finally, delete from users
            if ($tenant['user_id']) {
                $delete_user_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                $delete_user_stmt->bind_param("i", $tenant['user_id']);
                $delete_user_stmt->execute();
            }
            
            // If all operations successful, commit the transaction
            $conn->commit();
            
            header("Location: ../tenants.php?success=Tenant and user credentials deleted successfully");
            exit;
        } else {
            throw new Exception("Tenant not found!");
        }
    } catch (Exception $e) {
        // If any operation fails, roll back the entire transaction
        $conn->rollback();
        die("Error: " . $e->getMessage());
    } finally {
        // Close all prepared statements
        if (isset($stmt)) $stmt->close();
        if (isset($insert_stmt)) $insert_stmt->close();
        if (isset($delete_transaction_stmt)) $delete_transaction_stmt->close();
        if (isset($delete_message_stmt)) $delete_message_stmt->close();
        if (isset($delete_tenant_stmt)) $delete_tenant_stmt->close();
        if (isset($delete_user_stmt)) $delete_user_stmt->close();
        $conn->close();
    }
} else {
    die("No tenant ID provided!");
}
?>