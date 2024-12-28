<?php


include 'connections.php';


$unit_number = isset($_GET['unit']) ? (int)$_GET['unit'] : null;

if ($unit_number < 1 || $unit_number > 5) {
    // die("Invalid unit number. Please select a unit between 1 and 5.");
    header("Location: login.php"); 
}

$unit_name = "Unit $unit_number";

$sql = "SELECT units FROM tenant WHERE units = '$unit_name'";
$result = $conn->query($sql);

$status = ($result->num_rows > 0) ? '<p class="fs-4 text-muted text-center">Occupied</p>' : '<p class="fs-4 fw-bold text-center text-warning">Available</p>';

function getUnitType($unitNumber) {
    return $unitNumber >= 3 ? '2-Storey Building' : 'Single-Storey Building';
}

$type = getUnitType($unit_number);

function maxOccupancy($unitNumber) {
    return $unitNumber >= 3 ? '3-5 Persons' : '2-4 Persons';
}

$occupancy = maxOccupancy($unit_number);

function getUnitImage($unitNumber, $status) {
    if ($unitNumber >= 3) {
        return $status == '<p class="fs-4 text-muted text-center">Occupied</p>' ? './assets/images/icons/house2.png' : './assets/images/icons/rent-house2.png';
    } else {
        return $status == '<p class="fs-4 text-muted text-center">Occupied</p>' ? './assets/images/icons/house1.png' : './assets/images/icons/rent-house1.png';
    }
}

$img_src = getUnitImage($unit_number, $status);

function rentButton($status, $unit_number) {
    if ($status == '<p class="fs-4 fw-bold text-center text-warning">Available</p>') {
        return "<a href='./authentication/signup_handler.php?unit=$unit_number' class='btn btn-primary w-100 custom-btn-font'>Rent this Unit</a>";
    } else {
        return "";
    }
}

$rent = rentButton($status, $unit_number);

function buildingType($unitNumber) {
    return $unitNumber >= 3 ? ' <li>
                                <ul class="card-subtitle fs-4">2 Bedrooms (located on the upper floor)</ul>
                                <ul class="card-subtitle fs-4">1 Living Room</ul>
                                <ul class="card-subtitle fs-4">1 Bathroom</ul>
                                <ul class="card-subtitle fs-4">1 Kitchen/Dining Area</ul>
                                </li>' : '
                                <li>
                                <ul class="card-subtitle fs-4">1 Bedroom</ul>
                                <ul class="card-subtitle fs-4">1 Living Room</ul>
                                <ul class="card-subtitle fs-4">1 Bathroom</ul>
                                <ul class="card-subtitle fs-4">1 Kitchen/ Dining Area</ul>
                                </li>
                                ';
}

$building = buildingType($unit_number);

?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidalgo's Apartment</title>
    <link rel="shortcut icon" href="./assets/images/logov5.png">
    <link rel="shortcut icon" href="./assets/images/logov5.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

    <!-- FONT AWESOME -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">


    <!-- ANIMATE ON SCROLL -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- BOOTSTRAP -->

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
         * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        }

        body{
        background-color: rgb(102, 153, 255) !important;
        display: flex;
        flex-direction: column;

        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./assets/images/home_bg.png'); /* Dark overlay and image */  
         background-size: cover; 
        background-position: center; 
        background-attachment: fixed;   

        }

        body, html {
        font-family: Arial, sans-serif;
        overflow-x: hidden;
        }

         /* Hide scrollbar for Chrome, Safari, and Opera */
         body::-webkit-scrollbar { 
            display: none;
        }

        /* NAVBAR */

        #homeNav {
            background-color: rgb(102, 153, 255) !important;
            border-radius: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            transition: background-color 0.3s ease-in-out;
       
            
        }

        #homeNav a, #homeNav a:visited, #homeNav a:hover, #homeNav a:active {
            color: white !important;
            text-decoration: black;

        }

        #homeNav a:hover {
            color: black !important;
            text-decoration: none;
        }

        #homeNav .nav-link {
            color: white !important;
            text-decoration: none;
        }

        #homeNav {
            background-color: rgb(102, 153, 255) !important;
            border-radius: 20px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            transition: background-color 0.3s ease-in-out;
           
            
        }

        #homeNav a, #homeNav a:visited, #homeNav a:hover, #homeNav a:active {
            color: white !important;
            text-decoration: black;

        }

        #homeNav a:hover {
            color: black !important;
            text-decoration: none;
        }

        #homeNav .nav-link {
            color: white !important;
            text-decoration: none;
        }

        
        .nav-link.active {
        text-decoration: underline;
        color: white !important;
        }
        a{
            font-family: 'Poppins', 'sans-serif';
        font-size: 17px;
        font-weight: 500;
        text-decoration: none;
        }


  

        /* Back to Top Button */
        #backToTopBtn {
            display: none; /* Hidden by default */
            position: fixed; /* Fixed/sticky position */
            bottom: 50px; /* Place the button at the bottom of the page */
            right: 50px; /* Place the button 20px from the right */
            z-index: 99; /* Make sure it does not overlap */
            border: none; /* Remove borders */
            outline: none; /* Remove outline */
            background-color: rgb(102, 153, 255) !important;
            color: white; /* White text (arrow) color */
            cursor: pointer; /* Add a mouse pointer on hover */
            padding: 10px; /* Some padding */
            border-radius: 50%; /* Rounded corners */
            font-size: 16px; /* Font size for the arrow */
            width: 40px; /* Set width */
            height: 40px; /* Set height */
        }

        #backToTopBtn:hover {
            background-color: darkblue; /* Change background on hover */
        }

        /* Arrow Icon */
        .fas.fa-arrow-up {
            font-size: 20px; /* Adjust size of the arrow */
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

    </style>
</head>
<body>

    <?php

    include './components/navbar.php';

    ?> 


    <div class="container-fluid mt-4 mb-4">

    <div class="container mt-5">
        <br/>
        <div class="row justify-content-center">
            <div class="col-12 col-md-6 mb-3">
                <div class="card" data-aos="fade-up">
                    <div class="card-body">
                        <a href="tenant_view.php">Back</a>
                        <img src="<?php echo $img_src; ?>" class="card-img-top height-img" alt="Unit Image">
                        <h1 class="card-title text-center"><?php echo $unit_name; ?></h1>
                        <p class="card-text text-center"> <?php echo $status; ?></p>
                        <?php echo $rent; ?>
                        <h2 class="card-text fs-3 mt-4 mb-2 text-primary text-center">Unit Details</h2>
                        <h2 class="card-subtitle mt-4 mb-2 text-center"><?php echo $type; ?></h2>
                        <h2 class="card-subtitle mb-2 text-center">Maximum Occupancy: <?php echo $occupancy; ?></h2>
                        <p class="card-subtitle text-left"><?php echo $building; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>

    <br/>
    


    <?php

    include "components/footer.php";

    ?> 

    <!-- ANIMATE ON SCROLL -->
    <script src="https://unpkg.com/aos@next/dist/aos.js"></script>
    <script>
         // Initialize AOS FOR SCREEN ANIMATION
         AOS.init();

    </script>
</body>
</html>

<?php $conn->close(); ?>
