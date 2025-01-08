<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include "../connections.php";

// Fetch occupied units
$query = "SELECT units FROM tenant";
$result = $conn->query($query);

if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$occupiedUnits = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $occupiedUnits[] = $row['units'];
    }
}

// Fetch units with pending appointments
$pendingQuery = "SELECT units FROM appointments WHERE appointment_status = 'pending'";
$pendingResult = $conn->query($pendingQuery);

if ($pendingResult === false) {
    die("Error executing query: " . $conn->error);
}

$pendingUnits = [];
if ($pendingResult->num_rows > 0) {
    while ($row = $pendingResult->fetch_assoc()) {
        $pendingUnits[] = $row['units'];
    }
}

$current_page = basename($_SERVER['PHP_SELF']); 

if (isset($_GET['tenant_id'])) {
    $tenant_id = $_GET['tenant_id'];

    $stmt = $conn->prepare("SELECT fullname, phone_number, work, downpayment, advance, electricity, water, units, residents, move_in_date FROM tenant WHERE tenant_id = ?");
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $tenant = $result->fetch_assoc();
        $currentUnit = $tenant['units']; 
    } else {
        die("Tenant not found!");
    }

    $stmt->close();
} else {
    die("No tenant ID provided!");
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


    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>

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
    h4, h5 {
        font-family: 'Poppins', 'sans-serif';
        font-size: 20px;
        font-weight: 500;
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
    .custom-btn-font {
    font-size: 1.35rem; 
    }

    .form-w{
        max-width:600px;
        width: 100%;
        background-color: #f8f9fa; 
        background-color: #f8f9fa; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        
    }
    .form-label {
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 300;
    }
    h3{
        font-family: 'Poppins', 'sans-serif';
        font-size: 40px;
        font-weight: 500;
    }
    .btn{
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 300;
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
        color: #000000CC;
        margin-left: 0px;
        margin-right: 10px;
        padding: 10px 0px;
        font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 500;
    }
    }
    /* Hide the spinner for Chrome, Safari, and Edge */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .form-w{
      margin-left: 10px;
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

        <li class="items">
            <a href="dashboard.php"><i class="fa-solid fa-chart-simple"></i></a>
            <p class="para"><a href="dashboard.php">Dashboard</a></p>
        </li>

        <li class="items">
            <a href="units.php"><i class="fa-solid fa-home"></i></i></a>
            <p class="para"><a href="units.php">Units</a></p>
        </li>

        <li class="items <?php echo $current_page == 'edit-tenant.php' ? 'active-menu' : ''; ?>">
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

    <!-- MAIN CONTENT -->
<div class="content">
    <div class="container-fluid ">
        <div class="row justify-content-center">
            <form class="shadow p-4  mb-3 bg-light rounded" method="post" action="req/edit-tenant.php" style="max-width: 600px; width: 100%;">
                <hr>
                <h3 class="text-center">Edit Tenant Information</h3>
                <hr>

                <!-- ERROR HANDLING -->
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $_GET['error'] ?>
                    </div>
                <?php } ?>

                <!-- SUCCESS HANDLING -->
                <?php if (isset($_GET['success'])) { ?>
                    <div class="alert alert-success" role="alert">
                        <?= $_GET['success'] ?>
                    </div>
                <?php } ?>

                <!-- Tenant ID To Pass -->
                <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($tenant_id); ?>">


                <!-- Full Name -->
                <div class="mb-3">
                    <label class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($tenant['fullname']); ?>" required>
                </div>

                <!-- Phone Number -->
                <div class="mb-3">
                    <label class="form-label">Phone Number</label>
                    <input type="number" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($tenant['phone_number']); ?>" required>
                </div>

                <!-- Work -->
                <div class="mb-3">
                    <label class="form-label">Work</label>
                    <input type="text" class="form-control" id="work" name="work" value="<?php echo htmlspecialchars($tenant['work']); ?>" required>
                </div>


                  <!-- Downpayment -->
                  <div class="mb-3">
                    <label class="form-label">Downpayment</label>
                    <input type="number" class="form-control" id="downpayment" name="downpayment" value="<?php echo htmlspecialchars($tenant['downpayment']); ?>" min="0" required>
                </div>


                  <!-- Downpayment -->
                  <div class="mb-3">
                    <label class="form-label">Advance</label>
                    <input type="number" class="form-control" id="advance" name="advance" value="<?php echo htmlspecialchars($tenant['advance']); ?>" min="0" required>
                </div>

                <sub>* Utilities Downpayment</sub>


                  <!-- Downpayment -->
                  <div class="mb-3">
                    <label class="form-label">Electricity</label>
                    <input type="number" class="form-control" id="electricity" name="electricity" value="<?php echo htmlspecialchars($tenant['electricity']); ?>" min="0" required>
                </div>


                  <!-- Downpayment -->
                  <div class="mb-3">
                    <label class="form-label">Water</label>
                    <input type="number" class="form-control" id="water" name="water" value="<?php echo htmlspecialchars($tenant['water']); ?>" min="0" required>
                </div>



                <!-- Units -->
                <div class="mb-3">
                <label class="form-label">Unit:</label>

                <div class="d-flex flex-wrap gap-3">
                    <?php
                    $allUnits = ['Unit 1', 'Unit 2', 'Unit 3', 'Unit 4', 'Unit 5'];
                    $disableUpdate = false;
                    foreach ($allUnits as $unit) {
                        // Determine if the unit is occupied or has a pending appointment and not the tenant's current unit
                        $isOccupied = in_array($unit, $occupiedUnits) && $unit !== $currentUnit;
                        $hasPending = in_array($unit, $pendingUnits);

                        // Determine if the unit should be preselected
                        $isSelected = $unit === $currentUnit;

                        if ($isSelected && $hasPending) {
                            $disableUpdate = true;
                        }

                        echo '<div class="form-check">';
                        echo '<input class="form-check-input" type="radio" name="units" value="' . $unit . '"'
                            . ($isSelected ? ' checked' : '') . ($isOccupied ? ' disabled' : '') . '>'; // Disable if occupied
                        echo '<label class="form-check-label' . ($isOccupied || $hasPending ? ' text-muted' : '') . '">' . $unit . '</label>';
                        echo '</div>';
                    }
                    ?>
                </div>
                <sub>* Unit radio buttons are unclickable if occupied or has a pending appointment</sub>
            </div>


               <!-- Number of Residents -->
               <div class="mb-3">
                    <label class="form-label">Number of Residents</label>
                    <input type="number" class="form-control" id="residents" name="residents" value="<?php echo htmlspecialchars($tenant['residents']); ?>" min="0" required>
                </div>



                    <!-- ERROR AND SUCCESS HANDLING -->
                    <?php if (isset($_GET['error'])) { ?>
                    <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                    <?php } ?>
                    <?php if (isset($_GET['success'])) { ?>
                        <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                    <?php } ?>


                <!-- Submit Button -->
                <button type="submit" class="btn btn-ocean w-100" <?php echo $disableUpdate ? 'disabled' : ''; ?>>Update</button>
                <?php if ($disableUpdate) { ?>
                    <p class="text-danger text-center">* Pending appointment</p>
                <?php } ?>
            </form>

            <!-- Separate form for updating move-in date -->
            <form class="shadow p-4  mb-3 bg-light rounded" method="post" action="req/edit-move-date.php" style="max-width: 600px; width: 100%;">
                <hr>
                <h3 class="text-center">Update Move-in Date</h3>
                <hr>

                <!-- Tenant ID To Pass -->
                <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($tenant_id); ?>">

                <!-- Calendar for Move-in Date -->
                <div class="form-group mb-3">
                    <label for="move_in_date" class="form-label">Move-in Date:</label>
                    <input type="date" class="form-control" id="move_in_date" name="move_in_date" value="<?php echo htmlspecialchars($tenant['move_in_date']); ?>" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-ocean w-100">Update Move-in Date</button>
            </form>
        </div>
    </div>
</div>


</div>



<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

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

      // RANDOM PASSWORD GENERATOR 
      function makePass(length) {
            var result           = '';
            var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
            var charactersLength = characters.length;
            for ( var i = 0; i < length; i++ ) {
              result += characters.charAt(Math.floor(Math.random() * 
         charactersLength));

           }
           var passInput = document.getElementById('password');
           passInput.value = result;
        }

        var gBtn = document.getElementById('gBtn');
        gBtn.addEventListener('click', function(e){
          e.preventDefault();
          makePass(7); // just adjust the number to increase the character length of the password generator
        });
</script>

</body>
</html>