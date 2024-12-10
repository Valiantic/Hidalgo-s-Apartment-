<?php

include "../../connections.php";

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../../authentication/phpmailer/src/Exception.php';
require '../../authentication/phpmailer/src/PHPMailer.php';
require '../../authentication/phpmailer/src/SMTP.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $fullname = $_POST['fullname'];
    $phone_number = $_POST['phone_number'];
    $work = $_POST['work'];
    $downpayment = $_POST['downpayment'];
    $units = isset($_POST['units']) ? $_POST['units'] : '';
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validate downpayment
    if ($downpayment < 0) {
        die("Downpayment cannot be negative.");
    }

     // Check if email already exists in the `users` table
     $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
     $stmt_check->bind_param("s", $email);
     $stmt_check->execute();
     $stmt_check->store_result();

     if ($stmt_check->num_rows > 0) {
        die("Error: The email address is already registered. Please use a different email.");
    }
    $stmt_check->close();
 

    // Insert data into `tenant` table
    $stmt1 = $conn->prepare("INSERT INTO tenant (fullname, phone_number, work, downpayment, units) VALUES (?, ?, ?, ?, ?)");
    $stmt1->bind_param("sssds", $fullname, $phone_number, $work, $downpayment, $units);
    $stmt1->execute();

    // Insert data into `users` table
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $stmt2 = $conn->prepare("INSERT INTO users (fullname, phone_number, work, email, password) VALUES (?, ?, ?, ?, ?)");
    $stmt2->bind_param("sssss", $fullname, $phone_number, $work, $email, $hashed_password);
    $stmt2->execute();

    // Send email notification
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        // STEVEN'S GMAIL ACCOUNT USED FOR SENDING EMAIL NOTIFICATIONS
        $mail->Username = 'stevenmadali17@gmail.com'; // Your Gmail address
        $mail->Password = 'odei efvp hufg rccu'; // Your app password
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('hidalgosapartment@gmail.com');
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject = 'Account Created';
        $mail->Body = "<h1>Welcome! $fullname Thank you for choosing Hidalgo's Apartment! </h1><p>Use this credentials to Log-in to your account.</p><ul><li><strong>Email: $email</strong> </li><li><strong>Password: $password</strong> </li></ul>";

        $mail->send();
        echo " <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>;
    <script>
        Swal.fire({
            title: 'Success!',
            text: 'Tenant added successfully. An email notification has been sent to the tenant.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../add-tenant.php';
            }
        });
    </script>";

        
    } catch (Exception $e) {
        echo "
         <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>;
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'An error occurred: " . $mail->ErrorInfo . "',
                icon: 'error'
            });
        </script>";
    }
}



?>