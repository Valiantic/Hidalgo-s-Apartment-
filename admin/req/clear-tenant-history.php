<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../../connections.php';


if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $query = "DELETE FROM tenant_history";
    if (mysqli_query($conn, $query)) {
        header('Location: ../tenant-history.php?success=' . urlencode('Tenant History data cleared successfully.'));
        exit;
    } else {
        header('Location: ../tenant-history.php?error=' . urlencode('Failed to delete tenant history data.'));
        exit;
    }
}
?>
