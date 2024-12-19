<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']);

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT tenant_id, units FROM tenant WHERE user_id = ?");
$stmt->execute([$user_id]);
$tenant = $stmt->fetch();
$tenant_id = $tenant['tenant_id'];
$unit = $tenant['units'];

$_SESSION['tenant_id'] = $tenant_id;
$_SESSION['unit'] = $unit;

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


        <!-- BOOTSTRAP 5.3.0 CSS -->
     <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">

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
    font-size: 1.35rem; /* Adjust the size as needed */
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
            .form-control, .form-select {
                font-size: 14px;
    }
            .btn {
                font-size: 14px;
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

        <li class="items  <?php echo $current_page == 'report-issue.php' ? 'active-menu' : ''; ?>">
            <a href="home.php"><i class="fa-solid fa-person"></i></a>
            <p class="para"><a href="home.php">Home</a></p>
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

            <h1 data-aos="fade-right" class="display- text-white fw-bold">Let's Raise your Concern to the Admin!</h1>
               

            <!-- INPUT FORM HERE -->
            <div class="card text-dark bg-light mb-3 p-3" style="max-width: 30rem;">
                <div class="card-title fs-4 text-center">Maintenance Report</div>
                <div class="card-body">

                <form action="./req/report-issue.php" method="POST" class="p-4 border rounded">
                    <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($tenant_id); ?>">
                    <input type="hidden" name="unit" value="<?php echo htmlspecialchars($unit); ?>">
                    <div class="mb-3">
                        <label for="report-description" class="form-label">Report Issue</label>
                        <textarea class="form-control" id="report-description" name="report_description" rows="4" placeholder="Tell us what happened?" required></textarea>
                    </div>

                    <?php if (isset($error_message)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>

                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success mt-3">
                            <?= htmlspecialchars($_SESSION['success_message']) ?>
                            <?php unset($_SESSION['success_message']); ?>
                        </div>
                    <?php endif; ?>

                    <button type="submit" class="btn btn-primary w-100">Submit Report</button>
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


</script>

</body>
</html>
<?php
$conn->close();
?>
