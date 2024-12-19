<?php
session_start();

if (!isset($_SESSION['tenant_id'])) {
    header('Location: ../../authentication/login.php');
    exit;
}

include '../../connections.php';

$tenant_id = $_SESSION['tenant_id'];
$unit = $_SESSION['unit']; 
$description = $_POST['report_description'];

if (empty($description)) {
    die("Please provide a description for the issue.");
}

try {
    $stmt = $conn->prepare("INSERT INTO maintenance_request (tenant_id, unit, description) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $tenant_id, $unit, $description);
    $stmt->execute();

    $_SESSION['success_message'] = "Maintenance request submitted successfully!";
    header('Location: ../report-issue.php'); 
    exit;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
