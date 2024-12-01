<?php
   include "connections.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidalgo's Apartment</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="shortcut icon" href="./assets/images/logov3.png">
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


    <!-- OWN CSS IS HERE! -->
    <style>
      
        * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        }

        body, html {
        font-family: Arial, sans-serif;
        overflow-x: hidden;
        }

         /* Hide scrollbar for Chrome, Safari, and Opera */
         body::-webkit-scrollbar { 
            display: none;
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
  
        
                
                
        .section {
          height: 100vh;
          display: flex;
          justify-content: center;
          align-items: center;
          color: #fff;
          text-align: center;
          background-size: cover;
          background-position: center;
          background-repeat: no-repeat;
        }


        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 2; /* Ensure content is above the overlay */
            text-align: center;
            color: black;
        }
        h4{
            color: #eee;
            font-size: 30px;
            font-family: "Bebas Neue", sans-serif;
        
            color:  white;
        }

        .content img {
            display: block; /* Remove extra space caused by inline-block behavior */
            margin: 0; /* Ensure no default margin */
        }

        .content h4 {
            margin: 0; /* Remove any margin on the heading */
        }
        p{
            text-align: justify;
        }

        .home {
        background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('./assets/images/home_bg.png'); /* Dark overlay and image */        }

        .highlight {
            background-color: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5));
        }

        .why-us {
          background-image: url('./assets/images/skyshot.jpg');
        }

        #hightlight {
            min-height: 100vh;
        }
        .carousel-inner {
            max-width: 800px; /* Set the max width */
            margin: 0 auto; /* Center the carousel */
        }
        .carousel-img {
            border-radius: 10px; /* Add border-radius to images */
             max-height: 90vh; /* Set max-height for larger screens */ 
            /* object-fit: contain; Ensure the entire image is visible without cropping */
            object-fit: cover; /* Cover the entire area */
        }
        .carousel-caption h5 {
            font-family: "Bebas Neue", sans-serif;
            font-size: 25px;
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            padding: 10px; /* Padding around text */
            border-radius: 10px; /* Rounded corners */
        }
        .carousel-control-prev, .carousel-control-next 
        { 
        width: 45px; /* Adjust width */ 
        height: 45px; /* Adjust height */ 
        background-color: rgb(102, 153, 255) !important;
        border-radius: 50%; /* Rounded corners */ 
        padding: 10px; /* Padding for a more modern look */ 
        top: 50%; /* Center vertically */ 
        transform: translateY(-50%); } 

        .carousel-control-prev i, .carousel-control-next i 
        { 
            font-size: 4px; /* Smaller size for the arrow */ 
            color: white; /* Arrow color */
        }


        #why-us {
        min-height: 100vh;
        }
        #why-us .card-1{
        max-width: 600px;
        width:90%;
        /* MAKE THE BACKGROUND TRANSPARENT */
        /* background: rgba(255,255,255, 0.7); */
        background: white;
        padding: 20px;
        border-radius: 20px;
        }
        #why-us .card-1 h5{
         font-family: "Bebas Neue", sans-serif;
        font-size: 25px;
        
        }


        #contact {
        min-height: 100vh;
        background-image: url('./assets/images/side_windows.jpg');
        }
        #contact form {
        max-width: 600px;
        width: 90%;
        /* TRANSPARENCY */
        /* background: rgba(255,255,255, 0.7); */ 
        background: white;
        padding: 20px;
        border-radius: 20px;
        }
        #contact form h3{
        text-align:center;
        font-family: "Bebas Neue", sans-serif;
        color: black;
        }
        #contact form label {
            display: flex;
            justify-content: start;
            color: #252525;
        }

        .form-text{
            display: flex;
            justify-content: start;
        }
        textarea{
        resize:none;
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


         /* MEDIA QUERIES */

        /* CARD TEXT SIZE */
        @media (max-width: 768px) {
            .card-text {
                font-size: 14px; /* Smaller font size on mobile */
            }
        }
        @media (min-width: 769px) {
            .card-text{
                font-size: 15px; /* Larger font size on tablets and above */
            }
        }
        @media (min-width: 1200px) {
            .card-text {
                font-size: 17px; /* Even larger font size on larger screens */
            }
        }

         /* Custom responsive image control */
         .img-fluid {
            max-width: 100%; /* Ensures the image resizes to the parent width */
        }

        /* IMAGE SIZE  */
        @media (max-width: 768px) {
            .responsive-image {
                max-height: 280px; /* Set a max height for smaller screens */
                object-fit: cover; /* Make sure the image covers the area without stretching */
            }
        }

        /* For larger screens */
        @media (min-width: 769px) {
            .responsive-image {
                max-height: 500px; /* Set a larger max-height for larger screens */
                object-fit: contain; /* Keep the entire image visible within the container */
            }
        }

     

    </style>
</head>
<body>


        <?php

        include "components/navbar.php";

        ?> 


    
        <!-- MAIN SECTION -->

    <main>

        <!-- LOGO AND TEXT -->
        <section id="home" class="section home d-flex justify-content-start flex-column">
            <div class="content" data-aos="zoom-in">
            <img src="./assets/images/logov4.png" class="img-fluid">

            </div>
        </section>

        <!-- HIGHLIGHT CAROUSEL -->
        <section id="hightlight" class="section hightlight">
        <div class="container" data-aos="fade-up">
        <div id="carouselExampleControls" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- Carousel Item 1 -->
                <div class="carousel-item active">
                    <img src="./assets/images/rooms/door.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 1">
                    <div class="carousel-caption d-block">
                        <h5>Main Door</h5>
                    </div>
                </div>
                <!-- Carousel Item 2 -->
                <div class="carousel-item">
                    <img src="./assets/images/rooms/frontwindow.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 2">
                    <div class="carousel-caption d-block">
                        <h5>Lounge Window</h5>
                    </div>
                </div>
                <!-- Carousel Item 3 -->
                <div class="carousel-item">
                    <img src="./assets/images/rooms/kitchen.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 3">
                    <div class="carousel-caption d-block">
                        <h5>Kitchen</h5>
                    </div>
                </div>
                <!-- Carousel Item 4 -->
                <div class="carousel-item">
                    <img src="./assets/images/rooms/stairs.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 4">
                    <div class="carousel-caption d-block">
                        <h5>Stairs</h5>
                    </div>
                </div>

                 <!-- Carousel Item 5 -->
                 <div class="carousel-item">
                    <img src="./assets/images/rooms/windows.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 4">
                    <div class="carousel-caption d-block">
                        <h5>Windows</h5>
                    </div>
                </div>

                 <!-- Carousel Item 6 -->
                 <div class="carousel-item">
                    <img src="./assets/images/rooms/side_windows.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 4">
                    <div class="carousel-caption d-block">
                        <h5>Side Windows</h5>
                    </div>
                </div>

                 <!-- Carousel Item 7 -->
                 <div class="carousel-item">
                    <img src="./assets/images/rooms/cabinetone.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 4">
                    <div class="carousel-caption d-block">
                        <h5>Cabinet One</h5>
                    </div>
                </div>

                 <!-- Carousel Item 8 -->
                 <div class="carousel-item">
                    <img src="./assets/images/rooms/cabinettwo.jpg" class="d-block w-100 img-fluid carousel-img" alt="Image 4">
                    <div class="carousel-caption d-block">
                        <h5>Cabinet Two</h5>
                    </div>
                </div>

            </div>
            <!-- Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleControls" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
        </div>
        </section>


        <!-- WHY US CARD -->
        <section id="why-us" class="section why-us">
            <div class="content d-flex justify-content-center align-items-center" data-aos="fade-up">

                <div class="card mb-3 card-1">
            <div class="row g-0">
                <div class="col-md-4">
                <img src="./assets/images/logov3.png" class="img-fluid responsive-image rounded-start" alt="...">
                </div>
                <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">Why Hidalgo's Apartment? </h5>
                    <p class="card-text">

                    Hidalgo's apartment offers comfort and convenience. Situated in a perfect location that is at reach from the plaza of Biñan City. That means you could get to the palengke quickly for your grocery and essential needs. 

The location is also an ideal choice as it is located near bus and jeep terminals. In just a walk away, you already have access to get around the city. 

The convenience that our apartment offers makes Hidalgo's apartment the ideal choice when it comes to calling your space the perfect home.
                    </p>
                </div>
                </div>
            </div>
            </div>

            </div>
        </section>


    </main>


    <?php

    include "components/footer.php";

    ?> 


<!-- BOOTSTRAP -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- ANIMATE ON SCROLL -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script> 

        // Initialize AOS FOR SCREEN ANIMATION
         AOS.init();


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
