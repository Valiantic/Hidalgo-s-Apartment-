<?php
session_start();

if (!isset($_SESSION['tenant_id'])) {
    header('Location: ../../authentication/login.php');
    exit;
}

include '../../connections.php';

// Get all booked dates
$query = "SELECT appointment_date FROM appointments WHERE appointment_status != 'cancelled'";
$result = $conn->query($query);

$booked_dates = array();
while($row = $result->fetch_assoc()) {
    // Format date for JavaScript
    $booked_dates[] = date('Y-m-d H:i:s', strtotime($row['appointment_date']));
}

echo json_encode($booked_dates);
$conn->close();
?>