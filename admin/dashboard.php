<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']); 

// Fetch total number of tenants
$query = "SELECT COUNT(*) AS total_tenants FROM tenant";
$result = $conn->query($query);
$total_tenants = $result->fetch_assoc()['total_tenants'];

// Fetch total monthly earnings for the current month
$query = "
    SELECT SUM(downpayment + advance) AS total_earnings 
    FROM tenant 
    WHERE MONTH(move_in_date) = MONTH(CURDATE()) 
    AND YEAR(move_in_date) = YEAR(CURDATE())
";
$result = $conn->query($query);
$total_earnings = $result->fetch_assoc()['total_earnings'];

// Fetch total paid rent for the current month
$query = "
    SELECT t.unit, t.monthly_rent_status, t.transaction_date
    FROM transaction_info t
    WHERE t.monthly_rent_status = 'Paid'
    AND MONTH(t.transaction_date) = MONTH(CURDATE())
    AND YEAR(t.transaction_date) = YEAR(CURDATE())
";
$result = $conn->query($query);
$total_paid_rent = 0;
while ($row = $result->fetch_assoc()) {
    $unit_number = (int) filter_var($row['unit'], FILTER_SANITIZE_NUMBER_INT);
    if ($unit_number === 1 || $unit_number === 2) {
        $total_paid_rent += 3500;
    } elseif (in_array($unit_number, [3, 4, 5])) {
        $total_paid_rent += 6500;
    }
}

// Add total paid rent to total earnings
$total_earnings += $total_paid_rent;

// Fetch all transaction dates and advance them by 1 month
$query = "
    SELECT 
        t.unit,
        DATE_ADD(t.transaction_date, INTERVAL 1 MONTH) as due_date
    FROM transaction_info t
";
$result = $conn->query($query);
$todays_due_dates = 0;
while ($row = $result->fetch_assoc()) {
    if (date('Y-m-d') == date('Y-m-d', strtotime($row['due_date']))) {
        $todays_due_dates++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Hidalgo's Apartment</title>
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


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
        background: #C6E7FF;
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
        color: #252525;
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
        color: #ffffff;    
    }
    
    .logout-btn{
        margin-top: 30px;
        color: #B70202;
    }
    
    .logout-btn:hover{
        background-color: #B70202;
        color: #ffffff;    
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
     .btn-ocean {
        background-color: #4DA1A9;
        color: #ffffff;
    }

    .btn-ocean:hover {
        background-color:rgb(125, 187, 205);
        color: #ffffff;
    }
     
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
    font-size: 1.35rem; /* Adjust the size as needed */
    }

     /* MEDIA QUERIES */

    /* // FOR TABLET AND MOBILE VIEW */
    @media (max-width: 768px) {
        .sidebar{
        height: 100vh;
        width: 70px;
        background: #C6E7FF;
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
        color: #252525;
        margin-left: 0px;
        margin-right: 10px;
        padding: 10px 0px;
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 500;
    }
    }

    .sidebar .active-menu {
        background-color: #4DA1A9;
        color: white;
    }
    .sidebar .active-menu a {
        color: white;
    }




</style>

</head>
<body class="bg-light">
    
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

        <li class="items  <?php echo $current_page == 'dashboard.php' ? 'active-menu' : ''; ?>">
            <a href="dashboard.php"><i class="fa-solid fa-chart-simple"></i></a>
            <p class="para"><a href="dashboard.php">Dashboard</a></p>
        </li>

        <li class="items <?php echo $current_page == 'units.php' ? 'active-menu' : ''; ?>">
            <a href="units.php"><i class="fa-solid fa-home"></i></i></a>
            <p class="para"><a href="units.php">Units</a></p>
        </li>

        <li class="items <?php echo $current_page == 'tenants.php' ? 'active-menu' : ''; ?>">
            <a href="tenants.php"> <i class="fa-solid fa-user"></i></a>
            <p class="para"><a href="tenants.php">Tenants</a></p>
        </li>
        <li class="items <?php echo $current_page == 'message-admin.php' ? 'active-menu' : ''; ?>">
            <a href="message-admin.php"> <i class="fa-solid fa-message"></i></a>
            <p class="para"><a href="message-admin.php">Message</a></p>
        </li>

        <li class="items <?php echo $current_page == 'mail.php' ? 'active-menu' : ''; ?>">
            <a href="mail.php"> <i class="fa-solid fa-envelope"></i></a>
            <p class="para"><a href="mail.php">Mails</a></p>
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
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-lg">
                        <img class="card-img-top img-fluid height-img"  src="../assets/images/icons/all-tenants.png" alt="Card image cap">
                        <div class="card-body">
                            <h1 class="card-title"><?php echo $total_tenants; ?></h1>
                            <p class="card-text">All Tenants</p>
                            <div class="d-flex justify-content-center">
                            <a href="tenants.php" class="btn btn-ocean w-100 custom-btn-font">See more Info</a>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-lg">
                        <img class="card-img-top img-fluid height-img"  src="../assets/images/icons/house-income.png" alt="Card image cap">
                        <div class="card-body">
                            <h1 class="card-title">₱ <?php echo number_format($total_earnings, 2); ?></h1>
                            <p class="card-text">Monthly Earnings</p>
                            <div class="d-flex justify-content-center">
                            <a href="monthly-earnings.php" class="btn btn-ocean w-100 custom-btn-font">See more Info</a>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 mb-3">
                    <div class="card shadow-lg">
                        <img class="card-img-top img-fluid height-img"  src="../assets/images/icons/deadline.png" alt="Card image cap">
                        <div class="card-body">
                            <!-- MAKE THE VALUE OF THIS DYNAMIC -->
                            <h1 class="card-title"><?php echo $todays_due_dates; ?></h1>
                            <p class="card-text">Today's Due Date</p>
                            <div class="d-flex justify-content-center">
                            <a href="due-date.php" class="btn btn-ocean w-100 custom-btn-font">See more Info</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

<script>
    const toggler = document.querySelector('.toggler')
    const sidebar = document.querySelector('.sidebar')

    const showFull = () => {
        toggler.addEventListener('click', ()=> {
            toggler.classList.toggle('active')
            sidebar.classList.toggle('active')
        })
    }


    showFull()
</script>

</body>
</html>
<?php
$conn->close();
?>
