<?php
session_start();



if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

include '../../connections.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $old_password = trim($_POST['old_password']);
    $new_password = trim($_POST['new_password']);

    // Check if the user is logged in and retrieve their current user ID
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../settings.php?account_error=Unauthorized access.");
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Validate input fields
    if (!$email || !$old_password || !$new_password) {
        header("Location: ../settings.php?account_error=All fields are required.");
        exit();
    }

    // Check password strength
    if (strlen( $new_password) <= 7) {
        header('Location: ../settings.php?account_error=Password must be longer than 7 characters.');
        exit;
    } elseif (!preg_match('/[\W_]/',  $new_password)) {
        header('Location: ../settings.php?account_error=Password must include at least one special character.');
        exit;
    }


    try {
        // Fetch the current user data
        $stmt = $pdo->prepare("SELECT password, email FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();

        if (!$user) {
            header("Location: ../settings.php?account_error=User not found.");
            exit();
        }

        // Verify the old password
        if (!password_verify($old_password, $user['password'])) {
            header("Location: ../settings.php?account_error=Incorrect old password.");
            exit();
        }

        // Check if the email is already taken by another user
        $email_check_stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $email_check_stmt->execute([$email, $user_id]);
        if ($email_check_stmt->fetch()) {
            header("Location: ../settings.php?account_error=Email is already in use.");
            exit();
        }

        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update email and password
        $update_stmt = $pdo->prepare("UPDATE users SET email = ?, password = ? WHERE id = ?");
        $update_stmt->execute([$email, $hashed_password, $user_id]);

        // Redirect with a success message
        header("Location: ../settings.php?account_success=Account updated successfully.");
    } catch (Exception $e) {
        // Log error and redirect with an error message
        error_log($e->getMessage());
        header("Location: ../settings.php?account_error=An error occurred. Please try again.");
    }
} else {
    // Redirect if not a POST request
    header("Location: ../settings.php");
    exit();
}
?>