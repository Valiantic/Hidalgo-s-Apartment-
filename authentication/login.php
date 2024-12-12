<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <title>Hidalgo's Apartment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

     <!-- ANIMATE ON SCROLL -->
     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

     <!-- GOOGLE FONTS POPPINS  -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', 'sans-serif';
            background-image: url('../assets/images/skyshot.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;

        }

        .login-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-image {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('../assets/images/welcome-cover.jpeg') no-repeat center center/cover;
            border-radius: 14px;
            min-height: 400px;
        }

        .login-form {
            flex: 1;
            max-width: 450px;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 14px;
            margin-left: 40px;
        }

        .form-floating {
            margin-bottom: 25px; /* Adds gaps between input fields */
        }

        .form-floating i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #6c757d;
        }

        .form-floating input {
            padding-left: 2.5rem; /* Offset to make space for icons */
        }
        h3{
           color: rgb(102, 153, 255);

        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2rem;
            color: #6c757d;
        }
        .logo{
            height: 100px;
            margin-bottom: 10px;
        }
        
        
        .toggle-password {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
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

        .forgot-link {
            margin: -15px 0 15px;
            text-align: center;
        }

        .forgot-link a {
            font-size: 14.5px;
            color: #333;
            text-decoration: none;
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


    
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-image {
                border-radius: 14px;
                width: 100%;
                min-height: 250px;

            }

            .login-form {
                margin-top: 20px;
                width: 100%;
                margin-left: 0;
            }
        }
      
  

        @media screen and (max-width: 400px) {
            .form-box {
                padding: 20px;
            }

            .toggle-panel h1 {
                font-size: 30xp;
            }
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <!-- Left Side Image -->
        <div class="login-image" data-aos="fade-up">
            
        </div>

        
        <!-- Right Side Login Form -->
        <div class="login-form">
            <!-- Header -->
            <div class="text-center mb-4">
                <a href="../index.php"><img class="logo" src="../assets/images/logov5.png" alt="Logo"></a>
                <h3 class="fw-bold">Login</h3>
                
            </div>
            

            <!-- Login Form -->
            <form action="./req/login.php" method="POST">
            <div class="input-box">
                    <input type="text" name="email" placeholder="Email" required>
                    <i class='bx bxs-user'></i> 
                </div>    
                <div class="input-box">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="toggle-password bi bi-eye-slash"></i>
                </div>    
                <div class="forgot-link">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>

                <div class=" d-flex justify-content-center mb-1">
               
                <!-- ERROR AND SUCCESS HANDLING -->
                <?php if (isset($_GET['error'])) { ?>
                    <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                <?php } ?>

                </div>
                   

                <button type="submit" class="btn">Login</button>
                <div class=" d-flex justify-content-center mt-4">
                <p>Don't have an account? <a class="register" href="signup.php">Click here!</a></p>

                </div>
               
            </form>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- ANIMATE ON SCROLL -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>

    <script>


          // Initialize AOS FOR SCREEN ANIMATION
          AOS.init();

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
