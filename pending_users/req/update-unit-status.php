<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unitId = $_POST['unit'];

    // Include the database connection
    include '../../connections.php';

    // Check connection
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }

    // Update unit status
    $stmt = $conn->prepare("UPDATE units SET status = 'Rented' WHERE unit_name = ?");
    $unitName = "Unit $unitId";
    $stmt->bind_param('s', $unitName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Unit status updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update unit status.']);
    }

    $stmt->close();
    $conn->close();
}
?>
