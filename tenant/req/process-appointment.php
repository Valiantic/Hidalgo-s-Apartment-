<?php
session_start();

if (!isset($_SESSION['tenant_id'])) {
    header('Location: ../../authentication/login.php');
    exit;
}

include '../../connections.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tenant_id = $_POST['tenant_id'];
    $units = $_POST['units'];
    $appointment_date = $_POST['appointment_date'];
    
    // Verify tenant and unit match
    $verify_stmt = $conn->prepare("SELECT units FROM tenant WHERE tenant_id = ?");
    $verify_stmt->bind_param("i", $tenant_id);
    $verify_stmt->execute();
    $result = $verify_stmt->get_result();
    $tenant_data = $result->fetch_assoc();
    
    if ($tenant_data['units'] != $units) {
        die("Error: Invalid unit association");
    }
    
    // File upload handling
    $valid_id = $_FILES['valid_id'];
    $upload_dir = "./uploads/";
    
    // Create unique filename
    $file_extension = pathinfo($valid_id['name'], PATHINFO_EXTENSION);
    $unique_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $unique_filename;
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($valid_id['type'], $allowed_types)) {
        die("Error: Invalid file type. Please upload a JPEG or PNG image.");
    }
    
    // Move uploaded file
    if (move_uploaded_file($valid_id['tmp_name'], $upload_path)) {
        // Insert into database with tenant and unit information
        $stmt = $conn->prepare("INSERT INTO appointments (tenant_id, units, appointment_date, valid_id_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $tenant_id, $units, $appointment_date, $upload_path);
        
        if ($stmt->execute()) {
            echo "Appointment booked successfully!";
            // Redirect to success page
            header("Location: ../home.php");
        } else {
            echo "Error booking appointment: " . $conn->error;
        }
        
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
    
    $verify_stmt->close();
}

$conn->close();
?>

Ver