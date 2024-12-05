<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatibel" content="'IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logov3.png">
    <title>Hidalgo's Apartment</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>

<body>
    
    <div class="container">
        <div class="form-box login">
            <form action="">
                <div class="logo">
                    <a href="../index.php"> <img src="../assets/images/logov3.png" alt="logo"></a>
                </div>
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" placeholder="Email" required>
                    <i class='bx bxs-user'></i> 
                </div>    
                <div class="input-box">
                    <input type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>    
                <div class="forgot-link">
                    <a href="forgot-password.php">Forgot Password?</a>
                </div>
                <button type="submit" class="btn">Login</button>
            </form>
        </div>
        
        <div class="form-box register">
            <form action="">
                <h1>Registration</h1>
                <div class="input-box">
                    <input type="text" placeholder="Full Name" required>
                    <i class='bx bxs-user'></i> 
                </div>
                <div class="input-box">
                    <input type="number" placeholder="Phone Number" required>
                    <i class='bx bxs-phone' ></i> 
                </div> 
                <div class="input-box">
                    <input type="text" placeholder="Workplace" required>
                    <i class='bx bx-current-location' ></i>
                </div>        
                <div class="input-box">
                    <input type="email" placeholder="Email" required>
                    <i class='bx bxs-envelope' ></i>
                </div>   
                <div class="input-box">
                    <input type="password" placeholder="Password" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>    
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

    <script src="../assets/js/login.js"></script>
</body>



</html>