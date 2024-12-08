<?php
include '.././connections.php';
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: forgot-password.php?error=' . urlencode('Please provide your email address first.'));
    exit;
}

if (isset($_POST['reset'])) {
    // Ensure that the password field is being sent as a POST request
    if (isset($_POST['new_password']) && !empty($_POST['new_password'])) {
        $email = $_SESSION['email'];
        $newPassword = mysqli_real_escape_string($conn, $_POST['new_password']);  // New password input

        // Check password strength
        if (strlen($newPassword) <= 7) {
            header('Location: reset-password.php?error=' . urlencode('Password must be longer than 7 characters.'));
            exit;
        } elseif (!preg_match('/[\W_]/', $newPassword)) {
            header('Location: reset-password.php?error=' . urlencode('Password must include at least one special character.'));
            exit;
        }

        // Hash the new password before storing it in the database
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Prepare the SQL query to update the user's password
        $updateQuery = "UPDATE users SET password = '$hashedPassword' WHERE email = '$email'";

        // Execute the query and check for success
        if (mysqli_query($conn, $updateQuery)) {
            unset($_SESSION['email']); // Clear session email after password reset
            echo "<script>alert('Password reset successful!'); window.location.href = 'auth.php';</script>";
        } else {
            echo "<script>alert('Error resetting password. Please try again.');</script>";
        }
    } else {
        echo "<script>alert('New password cannot be empty.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <title>Hidalgo's Apartment</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
      <!-- BOX ICONS -->
      <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <!-- GOOGLE FONTS POPPINS  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    

    <style>
        body {
            background-color: #e0e7ff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 900px;
            width: 550px;
            display: flex;
            flex-direction: column;
        }
        .left-panel, .right-panel {
            padding: 40px;
            flex: 1;
        }
        .left-panel {
            border-radius: 14px;
            margin-top: 4px;
            background-color: #3b82f6;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .left-panel h2 {
            margin-bottom: 20px;
            text-align: center;
            font-family: 'Poppins', 'sans-serif';
            font-size: 36px;
            font-weight: 600;
        }
        h3 {
            text-align: center;
            font-family: 'Poppins', 'sans-serif';
            font-size: 26px;
            font-weight: 500;
        }
        .right-panel {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #3b82f6;
            border: none;
        }
        .btn-primary:hover {
            background-color: #2563eb;
        }
        .input-box {
            position: relative;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .input-box input::placeholder {
            color: #888;
            font-weight: 400;
        }

        .input-box i {
            position: absolute;   
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #888;
        }

        .btn {
            width: 100%;
            height: 48px;
            background-color: rgb(102, 153, 255) !important;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
        }
        span{
            display: block;
        }
        .password-container {
            position: relative;
        }
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        .notify {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <h2>Let's Now Set your <span>New Password!</span></h2>
        </div>
        <div class="right-panel">
            <h3>Kindly Enter your New Password</h3>
            <form method="post" action="">
                <div class="input-box">
                    <input type="password" id="password" name="new_password" placeholder="Password" required>
                    <i class="toggle-password bi bi-eye-slash"></i>
                </div>    
              
                <div class="notify">
                    <!-- ERROR AND SUCCESS HANDLING -->
                    <?php if (isset($_GET['error'])) { ?>
                        <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                    <?php } ?>
                    <?php if (isset($_GET['success'])) { ?>
                        <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                    <?php } ?>
                </div>

                <button class="btn" name="reset">Reset Password</button>
            </form>
        </div>
    </div>

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        // PASSWORD TOGGLE 
        const togglePassword = document.querySelector('.toggle-password');
        const passwordField = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>
