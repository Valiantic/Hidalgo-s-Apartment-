<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

session_start(); 

if (isset($_POST["send"])) {
    $mail = new PHPMailer(true);

    try {

        $verificationCode = rand(100000, 999999);

        $_SESSION['verification_code'] = $verificationCode;

        $subject = "Your Verification Code";
        $message = "Your verification code is: " . $verificationCode;

        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hidalgosapartment@gmail.com'; 
        $mail->Password = 'sgqgqejxbhlccnvw'; 
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('hidalgosapartment@gmail.com');
        $mail->addAddress($_POST["email"]);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();

        echo "
        <h3>Email Sent Successfully!</h3>
        <p>Please enter the verification code sent to your email:</p>
        <form action='verify.php' method='post'>
            <label for='verification_code'>Tenant Password Reset Verification Code:</label>
            <input type='text' id='verification_code' name='verification_code' required>
            <button type='submit'>Submit</button>
        </form>
        ";
    } catch (Exception $e) {
        echo "
        <script>
        alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');
        </script>
        ";
    }
}
?>