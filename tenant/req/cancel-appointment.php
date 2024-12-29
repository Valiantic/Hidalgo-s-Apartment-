<?php
session_start();
include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $tenant_id = $data['tenant_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE tenant_id = ? AND appointment_status = 'pending'");
        $stmt->execute([$tenant_id]);

        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No pending appointment found.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error cancelling appointment: ' . $e->getMessage()]);
    }
}
?>
