<?php
session_start();

if (!isset($_SESSION['tenant_id'])) {
    header('Location: ../../authentication/login.php');
    exit;
}

include '../../connections.php';

$tenant_id = $_POST['tenant_id'];

try {
    $stmt = $conn->prepare("DELETE FROM maintenance_request WHERE tenant_id = ?");
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();

    $_SESSION['success_message'] = "Maintenance requests confirmed and deleted successfully!";
    header('Location: ../home.php'); 
    exit;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
