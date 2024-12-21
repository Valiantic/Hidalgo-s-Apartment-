<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

$fullname = $_SESSION['fullname'];
$phone_number = $_SESSION['phone_number'];


include '../connections.php';


$current_page = basename($_SERVER['PHP_SELF']);


$user_id = $_SESSION['user_id'];


// Fetch user email
$stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
$email = $user['email'] ?? ''; 


$stmt = $pdo->prepare("SELECT tenant_id, move_in_date, units FROM tenant WHERE user_id = ?");
$stmt->execute([$user_id]);
$tenant = $stmt->fetch();
$tenant_id = $tenant['tenant_id'];
$start_date = $tenant['move_in_date'];
$unit = $tenant['units'];
$unit_number = (int) filter_var($unit, FILTER_SANITIZE_NUMBER_INT);

function getMonthlyRent($unit) {
    $rent_prices = [
        'Unit 1' => 3500,
        'Unit 2' => 3500,
        'Unit 3' => 6500,
        'Unit 4' => 6500,
        'Unit 5' => 6500,
        
    ];
    return isset($rent_prices[$unit]) ? $rent_prices[$unit] : 'N/A';
}

$monthly_rent = getMonthlyRent($unit);


$stmt = $pdo->prepare("SELECT monthly_rent_status, electricity_status, water_status 
                       FROM transaction_info 
                       WHERE tenant_id = ? 
                       ORDER BY transaction_id DESC 
                       LIMIT 1");
$stmt->execute([$tenant_id]);
$billing_info = $stmt->fetch();

function displayStatus($status) {
    switch ($status) {
        case 'Paid':
            return '<span style="color: green;">●</span> Paid';
        case 'Not Paid':
            return '<span style="color: red;">●</span> Not Paid';
        default:
            return '<span style="color: gray;">●</span> No Bill Yet';
    }
}

$maintenance_query = "SELECT unit, status FROM maintenance_request WHERE tenant_id = ? ORDER BY request_id DESC LIMIT 1";
$maintenance_stmt = $pdo->prepare($maintenance_query);
$maintenance_stmt->execute([$tenant_id]);
$maintenance_status = $maintenance_stmt->fetch();

$maintenance_color = 'gray';
$maintenance_text = 'No Issues';

if ($maintenance_status) {
    switch ($maintenance_status['status']) {
        case 'Pending':
            $maintenance_color = 'red';
            $maintenance_text = 'Pending';
            break;
        case 'In Progress':
            $maintenance_color = 'yellow';
            $maintenance_text = 'In Progress';
            break;
        case 'Resolved':
            $maintenance_color = 'green';
            $maintenance_text = 'Resolved';
            break;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidalgo's Apartment</title>
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

 <!-- Bootstrap CSS -->
 <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

     <!-- ANIMATE ON SCROLL -->
     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

     <!-- GOOGLE FONTS POPPINS  -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

<style>
    *{
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: sans-serif;
    }
    
    body{
        height: 100vh;
        width: 100vw;
        background-color: rgb(102, 153, 255) !important;
        display: flex;
        flex-direction: column;
    }
    
    .menu{
        display: flex;
        flex-grow: 1;
        overflow: hidden;
    }
    
    .sidebar{
        height: 100vh;
        width: 60px;
        background: aliceblue;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        transition: all 0.5s ease;
        
    }
    
    .mainHead{
        margin-left: 15px;
        
    }
    
    img{
        height: 40px;
        width: 40px;
        border-radius: 50%;
        margin-left: 10px;
    }
    
    .items{
        display: flex;
        align-items: center;
        font-size: 1.3rem;
        color: #000000CC;
        margin-left: 0px;
        padding: 10px 0px;
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 500;
    }
    
    .sidebar li{
        margin-left: 10px;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }
    
    .items i{
        margin: 0 10px;
    }
    
    .para{
        opacity: 0;
        transition: opacity 0.5s ease;
    }
    
    .sidebar li:not(.logout-btn):hover {
        background: #000;
        color: aliceblue;
    }
    
    .logout-btn{
        margin-top: 30px;
        color: #B70202;
    }
    
    .logout-btn:hover{
        background-color: #B70202;
        color: aliceblue;
    }
    
    .toggler{
        position: absolute;
        top: 0;
        left: 0px;
        padding: 10px 1px;
        font-size: 1.4rem;
        transition: all 0.5s ease;
    }
    
    .toggler #toggle-cross {
        display: none;
    }
    
    .active.toggler #toggle-cross {
        display: block;
    }
    
    .active.toggler #toggle-bars {
        display: none;
    }
    
    .active.toggler {
        left: 190px;
    }
    
    .active.sidebar {
        width: 220px;
    }
    
    .active.sidebar .para{
        opacity: 1;
    }
    
    .content {
        flex-grow: 1;
        padding: 20px;
        overflow-y: auto;
    }
    
    a {
        text-decoration: none;
        color: inherit;
    }

     /* CARD STYLING */
     .height-img {
    max-height: 220px; 
    width: auto;
    }
    .card-img-top {
    margin-top: 10px;
    width: 100%;
    height: auto;
    object-fit: contain; 
    border-radius: 0; 
    }

    .card-text{
        font-family: 'Poppins', 'sans-serif';
        font-size: 20px;
        font-weight: 700;
    }
    .card-title{
        font-family: 'Poppins', 'sans-serif';
        font-size: 30px;
        font-weight: 500;
    }
    a{
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 300;
    }
    h4, h5 {
        font-family: 'Poppins', 'sans-serif';
        font-size: 20px;
        font-weight: 500;
    }
    .custom-btn-font {
    font-size: 1.35rem; 
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

     /* MEDIA QUERIES */

    /* // FOR TABLET AND MOBILE VIEW */
    @media (max-width: 768px) {
        .sidebar{
        height: 100vh;
        width: 70px;
        background: aliceblue;
        display: flex;
        flex-direction: column;
        justify-content: space-evenly;
        transition: all 0.5s ease;
    }

    .active.toggler {
        left: 123px;
    }
    
    .active.sidebar {
        width: 225px;
    }

    .items{
        display: flex;
        align-items: center;
        font-size: 1.3rem;
        color: #000000CC;
        margin-left: 0px;
        margin-right: 10px;
        padding: 10px 0px;
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 500;
    }
    }

     .sidebar .active-menu {
        background: black;
        color: white;
    }
    .sidebar .active-menu a {
        color: white;
    }

    @media (max-width: 768px) {
        .col-md-6 {
            max-width: 100%;
            flex: 0 0 100%;
        }
    }

</style>

</head>
<body>
    
    <!-- ADMIN SIDEBAR COMPONENT -->
    <?php

    // include "../components/admin_sidebar.php";

    ?> 

<div class="menu">
    <div class="sidebar">
        <div class="logo items">
            <span class="mainHead para">
                <h5>Hidalgo's</h5>
                <h4>Apartment</h4>
            </span>
        </div>

        <li class="items  <?php echo $current_page == 'home.php' ? 'active-menu' : ''; ?>">
            <a href="home.php"><i class="fa-solid fa-person"></i></a>
            <p class="para"><a href="home.php">Dashboard</a></p>
        </li>


        <li class="items <?php echo $current_page == 'message.php' ? 'active-menu' : ''; ?>">
            <a href="message.php"> <i class="fa-solid fa-message"></i></a>
            <p class="para"><a href="message.php">Message</a></p>
        </li>

        <li class="items <?php echo $current_page == 'settings.php' ? 'active-menu' : ''; ?>">
            <a href="settings.php"> <i class="fa-solid fa-gear"></i></a>
            <p class="para"><a href="settings.php">Settings</a></p>
        </li>


        <li class="items logout-btn">
            <!-- ENCLOSED THE ANCHOR TAG WITHIN THE LIST ITEM -->
            <a href="logout.php"> <i class="fa-solid fa-right-from-bracket"></i></a>
            <p class="para"><a href="logout.php">Log-out</a></p>
        </li>
    </div>

    <div class="toggler">
        <i id="toggle-bars">
            <img src="../assets/images/logov3.png" alt="">
        </i>
        <i class="fa-solid fa-xmark" id="toggle-cross"></i>
    </div>

    <div class="content">
        <div class="container-fluid mt-4">
            <div class="d-flex justify-content-center flex-wrap">

               <!-- Right Side Login Form -->
               <div class="col-md-6">
                    <div class="login-form">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-black">Tenant Information</h3>
                        </div>

                        <!-- Login Form -->
                        <form action="./req/update_info.php" method="POST">
                            <div class="input-box">
                                <input type="text" name="fullname" placeholder="Fullname" value="<?php echo htmlspecialchars($fullname); ?>" required>
                                <i class='bx bxs-user-account'></i> 
                            </div>    
                            <div class="input-box">
                                <input type="text" name="phone_number" placeholder="Phonenumber" value="<?php echo htmlspecialchars($phone_number); ?>" required>
                                <i class='bx bxs-phone'></i> 
                            </div>    

                            <div class="d-flex justify-content-center mb-1">
                                <!-- ERROR AND SUCCESS HANDLING -->
                                <?php if (isset($_GET['error'])) { ?>
                                    <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                                <?php } ?>
                                <?php if (isset($_GET['success'])) { ?>
                                    <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                                <?php } ?>
                            </div>

                            <button type="submit" class="btn">Update Information</button>
                        </form>
                    </div>
                </div>

                <!-- New Form for Email and Password -->
                <div class="col-md-6">
                    <div class="login-form">
                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-black">Account Settings</h3>
                        </div>

                        <!-- Login Form -->
                        <form action="./req/update_account.php" method="POST">
                            <div class="input-box">
                                <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                                <i class='bx bxs-envelope'></i> 
                            </div>    
                            <div class="input-box">
                                <input type="password" name="old_password" placeholder="Old Password" required>
                                <i class='bx bxs-lock'></i> 
                            </div>    
                            <div class="input-box">
                                <input type="password" name="new_password" placeholder="New Password" required>
                                <i class='bx bxs-lock'></i> 
                            </div>    

                            <div class="d-flex justify-content-center mb-1">
                                <!-- ERROR AND SUCCESS HANDLING -->
                                <?php if (isset($_GET['error'])) { ?>
                                    <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                                <?php } ?>
                                <?php if (isset($_GET['success'])) { ?>
                                    <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                                <?php } ?>
                            </div>

                            <button type="submit" class="btn">Update Account</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

<!-- ANIMATE ON SCROLL -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

 
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">



<script>

    AOS.init();

    const toggler = document.querySelector('.toggler')
    const sidebar = document.querySelector('.sidebar')

    const showFull = () => {
        toggler.addEventListener('click', ()=> {
            toggler.classList.toggle('active')
            sidebar.classList.toggle('active')
        })
    }


    showFull()



    
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
<?php
$conn->close();
?>
