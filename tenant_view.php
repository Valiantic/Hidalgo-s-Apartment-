<?php


include 'connections.php';


// Fetch unit data
$query = "SELECT units, COUNT(*) AS count FROM tenant GROUP BY units";
$result = $conn->query($query);

$units_status = [];
while ($row = $result->fetch_assoc()) {
    $units_status[$row['units']] = $row['count'] > 0 ? 'Occupied' : 'Available';
}


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
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

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
    
    <!-- ADMIN SIDEBAR COMPONENT -->
    
    <?php

    include './components/navbar.php';

    ?> 


    <div class="content">

 

        <div class="container-fluid mt-4 mb-4">

        
       
            <br/>
            <br/>
            <br/>
            <br/>
            <div class="row justify-content-center">
            <?php
                for ($i = 1; $i <= 5; $i++) {
                    $status = isset($units_status["Unit $i"]) ? $units_status["Unit $i"] : 'Available';
                    if ($i <= 3) {
                        $img_src = $status == 'Occupied' ? './assets/images/icons/house2.png' : './assets/images/icons/rent-house2.png';
                    } else {
                        $img_src = $status == 'Occupied' ? './assets/images/icons/house1.png' : './assets/images/icons/rent-house1.png';
                    }
                    echo "
                    <div class='col-sm-12 col-md-6 col-lg-4 mb-3'>
                        <div class='card'>
                            <img class='card-img-top img-fluid height-img' src='$img_src' alt='Card image cap'>
                            <div class='card-body'>
                                <div class='d-flex justify-content-center'>
                                    <div class='d-block mb-2'>
                                        <h1 class='card-title'>Unit $i</h1>
                                        <p class='card-text'>$status</p>
                                    </div>
                                </div>
                                <div class='d-flex justify-content-center'>
                                    <a href='#' class='btn btn-primary w-100 custom-btn-font'>Info</a>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
                ?>

            </div>
        </div>
    </div>

    <?php

    include "components/footer.php";

    ?> 

<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


<script>

        // Get the button
        let mybutton = document.getElementById("backToTopBtn");

        // When the user scrolls down 20px from the top of the document, show the button
        window.onscroll = function() {
        scrollFunction();
        };

        function scrollFunction() {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
        }

        // When the user clicks on the button, scroll to the top of the document
        mybutton.addEventListener("click", function() {
        document.body.scrollTop = 0;
        document.documentElement.scrollTop = 0;
        });

   
</script>

</body>
</html>
