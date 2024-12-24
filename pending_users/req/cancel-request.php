<?php
// Create a new file: req/cancel_request.php
session_start();
include '../../connections.php';

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $unit_number = $_POST['unit_number'] ?? '';
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Get the document path first
        $stmt = $conn->prepare("SELECT document_path FROM verification_documents WHERE user_id = ? AND unit_number = ? AND status = 'pending'");
        $stmt->bind_param("is", $user_id, $unit_number);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            $document_path = '../../uploads/ids/' . $row['document_path'];
            
            // Delete the record from database
            $delete_stmt = $conn->prepare("DELETE FROM verification_documents WHERE user_id = ? AND unit_number = ? AND status = 'pending'");
            $delete_stmt->bind_param("is", $user_id, $unit_number);
            
            if ($delete_stmt->execute()) {
                // Delete the file
                if (file_exists($document_path)) {
                    unlink($document_path);
                }
                
                $conn->commit();
                $response['success'] = true;
                $response['message'] = 'Request cancelled successfully';
            } else {
                throw new Exception('Error deleting record');
            }
        } else {
            throw new Exception('Record not found');
        }
    } catch (Exception $e) {
        $conn->rollback();
        $response['message'] = 'Error: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>