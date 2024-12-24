<?php
session_start();
include '../../connections.php';  // Updated path to connections.php

$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'User not logged in';
    echo json_encode($response);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $unit_number = $_POST['unit_number'] ?? '';
    
    if (isset($_FILES['id_image'])) {
        $file = $_FILES['id_image'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileError = $file['error'];
        
        // Create uploads directory if it doesn't exist
        $uploadDir = '../../uploads/ids/';  // Updated path to uploads directory
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Generate unique filename
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('id_', true) . '.' . $fileExt;
        $uploadPath = $uploadDir . $newFileName;
        
        // Check file type
        $allowed = array('jpg', 'jpeg', 'png', 'pdf');
        if (in_array($fileExt, $allowed)) {
            if ($fileError === 0) {
                if (move_uploaded_file($fileTmpName, $uploadPath)) {
                    // Insert document record
                    $stmt = $conn->prepare("INSERT INTO verification_documents (user_id, unit_number, document_path) VALUES (?, ?, ?)");
                    $stmt->bind_param("iss", $user_id, $unit_number, $newFileName);
                    
                    if ($stmt->execute()) {
                        $response['success'] = true;
                        $response['message'] = 'ID uploaded successfully!';
                    } else {
                        $response['message'] = 'Error saving document information: ' . $conn->error;
                    }
                } else {
                    $response['message'] = 'Error uploading file. Please check directory permissions.';
                }
            } else {
                $response['message'] = 'Error occurred while uploading. Error code: ' . $fileError;
            }
        } else {
            $response['message'] = 'Invalid file type. Please upload JPG, JPEG, PNG, or PDF.';
        }
    } else {
        $response['message'] = 'No file uploaded.';
    }
}

echo json_encode($response);
?>