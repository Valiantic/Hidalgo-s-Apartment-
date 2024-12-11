<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatibel" content="'IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <title>Hidalgo's Apartment</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/login.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- GOOGLE FONTS POPPINS  -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

<style>

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

</style>

</head>

<body>
    
    <div class="container">
        <div class="form-box login">
            <form action="./req/login.php" method="POST">
                <div class="logo">
                    <a href="../index.php"> <img src="../assets/images/logov3.png" alt="logo"></a>
                </div>
                <h1>Login</h1>
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

                   <!-- ERROR AND SUCCESS HANDLING -->
                   <?php if (isset($_GET['error'])) { ?>
                    <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                <?php } ?>

                <button type="submit" class="btn">Login</button>
            </form>
        </div>
        
        <div class="form-box register">
            <!-- NOTE: METHOD POST MUST REMEMBER TO PASS DATA TO BACKEND -->
            <form action="./req/signup.php" method="POST">
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <i class='bx bxs-user'></i> 
                </div>
                <div class="input-box">
                    <input type="number" name="phone_number" placeholder="Phone Number" required>
                    <i class='bx bxs-phone' ></i> 
                </div> 
                <div class="input-box">
                    <input type="text" name="work" placeholder="Work" required>
                    <i class='bx bx-current-location' ></i>
                </div>        
                <div class="input-box">
                    <input type="email" name="email"  placeholder="Email" required>
                    <i class='bx bxs-envelope' ></i>
                </div>   
                <div class="input-box">
                    <input type="password" id="password" name="password"  placeholder="Password" required onkeyup="checkPasswordStrength()">
                    <i class='bx bxs-lock-alt'></i>
                </div>   
                
                  <!-- ERROR AND SUCCESS HANDLING -->
                  <?php if (isset($_GET['error'])) { ?>
                    <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                <?php } ?>
                <?php if (isset($_GET['success'])) { ?>
                    <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                <?php } ?>

             
                <button type="submit" class="btn">Register</button>
            </form>
        </div>

        <div class="toggle-box">
            <div class="toggle-panel toggle-left">
                <h1>Welcome Back!</h1>
              
                <p>Don't have an account?</p>
                <button class="btn register-btn">Register</button>
            </div>
            <div class="toggle-panel toggle-right">
                <h1>Hello, Welcome!</h1>
                <p>Already have an account?</p>
                <button class="btn login-btn">Login</button>
            </div>
        </div>    
    </div>


     <!-- Bootstrap Icons -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script src="../assets/js/login.js"></script>

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