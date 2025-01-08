<?php
session_start();
include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if all required fields are set
    if (isset($_POST['fullname'], $_POST['phone_number'], $_POST['work'], $_POST['email'], $_POST['password'], $_POST['unit'])) {
        
        // Retrieve form data
        $fullname = $_POST['fullname'];
        $phone_number = $_POST['phone_number'];
        $work = $_POST['work'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $unit = $_POST['unit'];
        $resident = $_POST['residents'];

        // Fixed Signup Wrong Password Requirement Redirect Base 
        $redirect_base = "../signup.php?unit=" . urlencode($unit);
        $_SESSION['rent_unit_access'] = true; 

        // Enhanced password validation
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long.';
        }
        if (!preg_match('/[\W_]/', $password)) {
            $errors[] = 'Password must include at least one special character.';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must include at least one uppercase letter.';
        }

        if (!empty($errors)) {
            header('Location: ' . $redirect_base . '&error=' . urlencode(implode(' ', $errors)));
            exit;
        }

        // Hash the password after passing validation
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Check if the email is already registered in users table
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists > 0) {
            // Redirect back to signup with error and preserve unit number
            header('Location: ' . $redirect_base . '&error=' . urlencode('The email is already registered. Please use a different email.'));
            exit;
        } else {
            // Begin transaction
            $pdo->beginTransaction();
            try {
                // Insert the new user into users table
                $stmt = $pdo->prepare("INSERT INTO users (fullname, phone_number, work, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$fullname, $phone_number, $work, $email, $hashedPassword, 'user']);
                $user_id = $pdo->lastInsertId();

                // Insert the new tenant into tenant table
                $stmt = $pdo->prepare("INSERT INTO tenant (user_id, fullname, phone_number, work, units, residents) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $fullname, $phone_number, $work, "Unit $unit", $resident]);
                $tenant_id = $pdo->lastInsertId();

                // Commit transaction
                $pdo->commit();

                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = 'user';
                $_SESSION['fullname'] = $fullname;
                $_SESSION['phone_number'] = $phone_number;
                $_SESSION['email'] = $email;
                $_SESSION['tenant_id'] = $tenant_id;
                $_SESSION['move_in_date'] = date('m/d/Y');
                $_SESSION['units'] = "Unit $unit";
                $_SESSION['rent_unit_access'] = true;
                $_SESSION['residents'] = $resident;

                // Redirect to the tenant home page
                header('Location: ../../tenant/home.php');
                exit;
            } catch (Exception $e) {
                // Rollback transaction
                $pdo->rollBack();
                // Redirect back with error and preserve unit number
                header('Location: ' . $redirect_base . '&error=' . urlencode('Signup failed. Please try again.'));
                exit;
            }
        }
    } else {
        $_SESSION['rent_unit_access'] = true; // Maintain access flag
        header('Location: ../signup.php?error=' . urlencode('All Fields are required.'));
        exit;
    }
}
?>
