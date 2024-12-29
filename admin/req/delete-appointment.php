<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../../authentication/login.php');
    exit;
}

include "../../connections.php";

if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];
    
    $delete_query = "DELETE FROM appointments WHERE tenant_id = ?";
    $stmt = $conn->prepare($delete_query);
    
    if ($stmt === false) {
        header("Location: ../tenants.php?error=Error preparing delete statement");
        exit;
    }
    
    $stmt->bind_param("i", $tenant_id);
    
    if ($stmt->execute()) {
        header("Location: ../tenants.php?success=Appointment deleted successfully");
    } else {
        header("Location: ../tenants.php?error=Error deleting appointment");
    }
    
    $stmt->close();
} else {
    header("Location: ../tenants.php?error=Invalid request");
}

$conn->close();
?>
