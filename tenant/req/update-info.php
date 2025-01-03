<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}


include "../../connections.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tenant_id = $_POST['tenant_id'];
    $fullname = htmlspecialchars(trim($_POST['fullname']));
    $phone_number = htmlspecialchars(trim($_POST['phone_number']));
    

    // Start a transaction to ensure atomicity
    $conn->begin_transaction();

    try {
        // Update tenant table
        $tenant_query = "UPDATE tenant 
                         SET fullname = ?, phone_number = ?
                         WHERE tenant_id = ?";
        $tenant_stmt = $conn->prepare($tenant_query);
        $tenant_stmt->bind_param("ssi", $fullname, $phone_number, $tenant_id);
        $tenant_stmt->execute();

        // Update users table
        $user_query = "UPDATE users 
                       SET fullname = ?, phone_number = ?
                       WHERE id = (SELECT user_id FROM tenant WHERE tenant_id = ?)";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("ssi", $fullname, $phone_number, $tenant_id);
        $user_stmt->execute();

        $conn->commit();

        header("Location: ../settings.php?info_success=Information updated successfully! Re-login to see changes.");
    } catch (Exception $e) {
        $conn->rollback();
        die("Error updating records: " . $e->getMessage());
    }
}
?>
