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
    <link rel="shortcut icon" href="./assets/images/logo.png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lobster&display=swap" rel="stylesheet">

    <!-- ANIMATE ON SCROLL -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

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
            text-decoration: none;
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
            background-color: rgba(255, 255, 255, 0.1); /* White with 50% transparency */
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
        p{
            text-align: justify;
        }

        .home {
          background-image: url('./assets/images/home_bg.png');
        }

        .about {
          background-image: url('./assets/images/about_bg.jpg');
        }

        .why-us {
          background-image: url('./assets/images/windows.jpg');
        }

        #about {
        min-height: 100vh;
        }
        #about .card-1{
        max-width: 600px;
        width:90%;
        /* MAKE THE BACKGROUND TRANSPARENT */
        /* background: rgba(255,255,255, 0.7); */
        background: white;
            padding: 20px;
            border-radius: 20px;
        }
        #about .card-1 h5{
         font-family: "Bebas Neue", sans-serif;
        font-size: 25px;
        
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

         /* MEDIA QUERIES */

         /* Custom responsive typography */
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

        /* Control the height on small screens */
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


                                <!-- NAVBAR -->
<nav class="navbar navbar-expand-lg bg-body-tertiary" id="homeNav">
            <div class="container-fluid">
                <a class="navbar-brand" href="#"><img src="./assets/images/logo.png" width="95" height="55"></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#about">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#contact">Contact</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav me-right mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link" href="login.php" id="logintxt">Login</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>


    


    <main>
        <section id="home" class="section home d-flex justify-content-center align-items-center flex-column">
            <div class="content" data-aos="zoom-in">
            <img src="./assets/images/logo.png" class="img-fluid">
            <h4>Since 1980's</h4>

            </div>
        </section>
        <section id="about" class="section about">
            <div class="content d-flex justify-content-center align-items-center" data-aos="fade-up">

            <div class="card mb-3 card-1">
            <div class="row g-0">
                <div class="col-md-4">
                <img src="./assets/images/logo.png" class="img-fluid rounded-start" alt="...">
                </div>
                <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">About</h5>
                    <p class="card-text text-justify">

                    A well-preserved property that was built from around the 1980s. Located in the neighborhood of 
                    Alunos Subdivision, Barangay Sto. Domingo in Biñan City. The property has 2 single-floor and 3 up-and-down space 
                    apartments with a single space garage to park your motorcycle or any small vehicle. 
                    </p>
                </div>
                </div>
            </div>
            </div>

            </div>
        </section>
        
        <section id="why-us" class="section why-us">
            <div class="content d-flex justify-content-center align-items-center" data-aos="fade-up">

                <div class="card mb-3 card-1">
            <div class="row g-0">
                <div class="col-md-4">
                <img src="./assets/images/stairs.jpg" class="img-fluid responsive-image rounded-start" alt="...">
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


        <section id="contact" class="section contact">
           <!-- CONTACTS FORM -->
        <form method="post" action="req/contact.php">
        <h3>Reach us!</h3>

        <!-- ERROR HANDLING  -->
        <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                    <?=$_GET['error']?>
                    </div>
            <?php } ?>

            <!-- SUCCESS HANDLING FOR TEACHER-DELETE -->
            <?php if (isset($_GET['success'])) { ?>
                        <div class="alert alert-info mt-3 n-table" role="alert">
                        <?=$_GET['success']?>
                    </div>
                    <?php } ?>

        <div class="mb-1">
            <label for="exampleInputEmail1" class="form-label">Email address</label>
            <input type="email" name="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">We'll never share your email with anyone else.</div>
        </div>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="full_name" class="form-control" id="exampleInputPassword1">
        </div>
        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea class="form-control"name="message" rows="4"></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary d-flex justify-content-start">Send</button>
        </form>
        
        </section>


    </main>

      <!-- Footer -->
      <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
              

                <!-- Column 2: Links -->
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#home" class="text-white">Home</a></li>
                        <li><a href="#about" class="text-white">About</a></li>
                        <li><a href="#contact" class="text-white">Contact</a></li>
                    </ul>
                </div>

                <!-- Column 3: Social Media -->
                <div class="col-md-4">
                    <h5>Follow Us</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Facebook</a></li>
                        <li><a href="#" class="text-white">Twitter</a></li>
                        <li><a href="#" class="text-white">Instagram</a></li>
                    </ul>
                </div>
            </div>

            <!-- Copyright Section -->
            <div class="row">
                <div class="col text-center mt-4">
                    <p>&copy; 2024 Hidalgo Apartment's</p>
                </div>
            </div>
        </div>
    </footer>


      
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- ANIMATE ON SCROLL -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init();
</script>
</body>
</html>
