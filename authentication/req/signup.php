<?php
include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    if (isset($_POST['fullname'], $_POST['phone_number'], $_POST['work'], $_POST['email'], $_POST['password'])) {
        
        // Retrieve form data
        $fullname = $_POST['fullname'];
        $phone_number = $_POST['phone_number'];
        $work = $_POST['work'];
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Check password strength
        if (strlen($password) <= 7) {
            header('Location: ../signup.php?error=' . urlencode('Password must be longer than 7 characters.'));
            exit;
        } elseif (!preg_match('/[\W_]/', $password)) {
            header('Location: ../signup.php?error=' . urlencode('Password must include special character.'));
            exit;
        }

        // Hash the password after passing validation
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email is already registered
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists > 0) {
            // Redirect back to the form with an error message
            header('Location: ../signup.php?error=' . urlencode('The email is already registered. Please use a different email.'));
            exit;
        } else {
            // Proceed to insert the new user
            $stmt = $pdo->prepare("INSERT INTO users (fullname, phone_number, work, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            try {
                $stmt->execute([$fullname, $phone_number, $work, $email, $hashedPassword, 'user']);
                session_start();
                // Set session variables
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['role'] = 'user';
                // Redirect to the tenant home page
                header('Location: ../../tenant/home.php');
                exit;
            } catch (Exception $e) {
                // Redirect back to the form with an error message
                header('Location: ../signup.php?error=' . urlencode('Signup failed. Please try again.'));
                exit;
            }
        }
    } else {
        // Redirect back to the form with an error message for missing fields
        header('Location: ../signup.php?error=' . urlencode('All Fields are required.'));
        exit;
    }
}
?>
