<?php
session_start();

// Set session variable to indicate access from "Rent this Unit" button
$_SESSION['rent_unit_access'] = true;

// Redirect to signup page
header("Location: signup.php");
exit();
?>
