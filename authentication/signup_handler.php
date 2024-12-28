<?php
session_start();

$unit_number = isset($_GET['unit']) ? (int)$_GET['unit'] : null;
if (!$unit_number) {
    header("Location: ../tenant_view_info.php");
    exit();
}

// Set session variable to indicate access from "Rent this Unit" button
$_SESSION['rent_unit_access'] = true;

// Redirect to signup page with unit number in URL
header("Location: signup.php?unit=$unit_number");
exit();
?>
