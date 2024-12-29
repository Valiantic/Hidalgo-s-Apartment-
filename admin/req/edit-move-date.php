<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../../connections.php";

if (isset($_POST['tenant_id']) && isset($_POST['move_in_date'])) {
    $tenant_id = $_POST['tenant_id'];
    $move_in_date = $_POST['move_in_date'];

    $stmt = $conn->prepare("UPDATE tenant SET move_in_date = ? WHERE tenant_id = ?");
    $stmt->bind_param("si", $move_in_date, $tenant_id);

    if ($stmt->execute()) {
        header("Location: ../tenants.php?success=Record updated successfully!");
    } else {
        header("Location: ../edit-tenant.php?tenant_id=$tenant_id&error=Failed to update move-in date");
    }

    $stmt->close();
} else {
    header("Location: ../edit-tenant.php?error=Invalid request");
}

$conn->close();
?>
