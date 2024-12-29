<?php
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

        // Check if the email is already registered in users table
        $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $checkStmt->execute([$email]);
        $emailExists = $checkStmt->fetchColumn();

        if ($emailExists > 0) {
            // Redirect back to the form with an error message
            header('Location: ../signup.php?error=' . urlencode('The email is already registered. Please use a different email.'));
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
                $stmt = $pdo->prepare("INSERT INTO tenant (user_id, fullname, phone_number, work, units) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$user_id, $fullname, $phone_number, $work, "Unit $unit"]);
                $tenant_id = $pdo->lastInsertId();

                // Commit transaction
                $pdo->commit();

                session_start();
                // Set session variables
                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = 'user';
                $_SESSION['fullname'] = $fullname;
                $_SESSION['phone_number'] = $phone_number;
                $_SESSION['email'] = $email;
                $_SESSION['tenant_id'] = $tenant_id;

                // Redirect to the tenant home page
                header('Location: ../../tenant/home.php');
                exit;
            } catch (Exception $e) {
                // Rollback transaction
                $pdo->rollBack();
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
