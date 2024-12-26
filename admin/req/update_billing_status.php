<?php
session_start();
include "../../connections.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenant_id = $_POST['tenant_id'];
    $unit = $_POST['unit'];
    $monthly_rent_status = $_POST['monthly_rent_status'];
    $electricity_status = $_POST['electricity_status'];
    $water_status = $_POST['water_status'];

    try {
        $stmt = $pdo->prepare("REPLACE INTO transaction_info (tenant_id, unit, monthly_rent_status, electricity_status, water_status) 
                               VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$tenant_id, $unit, $monthly_rent_status, $electricity_status, $water_status]);
        header('Location: ../tenants.php?success=Billing status updated successfully!');
        exit;
    } catch (PDOException $e) {
        echo "Error updating billing status: " . $e->getMessage();
    }
}
?>