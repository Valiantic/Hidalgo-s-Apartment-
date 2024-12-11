<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header('Location: ../authentication/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Landing</title>
</head>
<body>
    <h1>Welcome, User!</h1>
    <p>This is the landing page for normal users.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
