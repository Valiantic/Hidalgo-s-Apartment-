<?php
session_start();
include '.././connections.php';

if (!isset($_SESSION['email']) || !isset($_SESSION['verification_code'])) {
    echo "
    <script>
        alert('Session expired or verification code not found. Please try again.');
        window.location.href = 'forgot-password.php';
    </script>
    ";
    exit;
}

$email = $_SESSION['email'];


if (isset($_POST['verify'])) {
    $inputCode = trim($_POST['verification_code']); 



    if ((string)$inputCode === (string)$_SESSION['verification_code']) {
        echo "
        <script>
            window.location.href = 'reset-password.php';
        </script>
        ";
        exit;
    } else {
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>;
        <script>
            Swal.fire({
                title: 'Error!',
                text: 'Incorrect Verification Code!" . mysqli_error($conn) . "',
                icon: 'error'
            });
        </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logov3.png">
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
            font-size: 25px;
            font-weight: 600;
        }
        h3{
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
            <h2>We're Almost there! A verification code has been sent to your email: <strong><?php echo htmlspecialchars($email); ?></strong> </h2>
            
        </div>
        <div class="right-panel">
            <h3>Enter Verification Code</h3>
            <form method="post" action="">
                <div class="input-box">
                    <input type="text" name="verification_code" id="verification_code" required>
                    <i class='bx bxs-user'></i> 
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

                <button class="btn" name="verify">Verify Code</button>
            </form>
        </div>
    </div>
</body>
</html>