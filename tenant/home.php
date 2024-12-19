<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

$fullname = $_SESSION['fullname'];
$phone_number = $_SESSION['phone_number'];
$first_name = explode(' ', $fullname)[0]; 

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']);


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT tenant_id, move_in_date, units FROM tenant WHERE user_id = ?");
$stmt->execute([$user_id]);
$tenant = $stmt->fetch();
$tenant_id = $tenant['tenant_id'];
$start_date = $tenant['move_in_date'];
$unit = $tenant['units'];

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


     <!-- GOOGLE FONTS POPPINS  -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">


     <!-- ANIMATE ON SCROLL -->
     <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

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
        left: 170px;
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
            <a href="dashboard.php"><i class="fa-solid fa-chart-simple"></i></a>
            <p class="para"><a href="dashboard.php">Dashboard</a></p>
        </li>


        <li class="items <?php echo $current_page == 'message.php' ? 'active-menu' : ''; ?>">
            <a href="message.php"> <i class="fa-solid fa-message"></i></a>
            <p class="para"><a href="message.php">Message</a></p>
        </li>

        <li class="items <?php echo $current_page == 'message.php' ? 'active-menu' : ''; ?>">
            <a href="message.php"> <i class="fa-solid fa-gear"></i></a>
            <p class="para"><a href="message.php">Settings</a></p>
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
            <div class="row justify-content-center gap-4">

            <h1 data-aos="fade-right" class="display- text-white fw-bold">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
               

            <div class="card text-dark bg-light mb-3 p-3" style="max-width: 18rem;">
            <div class="card-title fs-4 text-center">Tenant Information</div>
            <div class="card-body">
                <label>Tenant Fullname</label>
                <h5 class="card-text"><?php echo htmlspecialchars($fullname)?></h5>
                <label>Tenant Phone number</label>
                <h5 class="card-text"><?php echo htmlspecialchars($phone_number)?></h5>
                <label>Start Date</label>
                <h5 class="card-text"><?php echo date('m/d/Y', strtotime($start_date)); ?></h5>
                <label>End Date</label>
                <h5 class="card-text" id="end-date"></h5>
            </div>
            </div>

            <div class="card text-left text-dark bg-light mb-3 p-3" style="max-width: 18rem;">
            <div class="card-title fs-4 text-center">Your Billings</div>
            <div class="card-body">
                <label>Monthly Rent <span style="color: blue;">₱<?php echo htmlspecialchars($monthly_rent); ?></span></label>
                <p class="card-text"><?php echo $billing_info ? displayStatus($billing_info['monthly_rent_status']) : displayStatus(null); ?></p>
                <label>Electricity Bill</label>
                <p class="card-text"><?php echo $billing_info ? displayStatus($billing_info['electricity_status']) : displayStatus(null); ?></p>
                <label>Water Bill</label>
                <p class="card-text"><?php echo $billing_info ? displayStatus($billing_info['water_status']) : displayStatus(null); ?></p>
                </div>
            </div>

            <div class="card text-left text-dark bg-light mb-3 p-3" style="max-width: 18rem;">
            <div class="card-title fs-4 text-center">Actions</div>
            <div class="card-body d-flex flex-column gap-2">
                <br/>
                <a href="report-issue.php" class="btn btn-warning btn-lg text-white">Report Issue</a>
                <a href="#" class="btn btn-primary btn-lg">View Contract</a>
                <a href="#" class="btn btn-danger btn-lg">End Contract</a>
            </div>
            </div>

            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

<!-- ANIMATE ON SCROLL -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

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

    const startDate = new Date("<?php echo $start_date; ?>");
    const endDate = new Date(startDate);
    endDate.setMonth(startDate.getMonth() + 1);
    const formattedEndDate = endDate.toLocaleDateString('en-US');
    document.getElementById('end-date').textContent = formattedEndDate;
</script>

</body>
</html>
<?php
$conn->close();
?>
