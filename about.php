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

        .about {
          background-image: url('./assets/images/about_bg.jpg');
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


       .title {
            font-size: 3rem;
            margin: 2rem 0rem;
        }
        .faq-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .faq {
            width: 80%; /* Adjust the width as needed */
            margin-bottom: 20px; /* Space between FAQs */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .question {
            display: flex;
            justify-content: space-between;
            align-items: center;
            min-height: 80px; /* Ensures a consistent height */
            width: 100%;
            text-align: center; /* Center text within the question */
            cursor: pointer; /* Add cursor pointer to indicate clickable element */
            padding: 10px; /* Add padding for better alignment */
            background-color: #f8f9fa; /* Light background color for contrast */
            border-radius: 5px; /* Rounded corners */
        }

        .answer {
            text-align: center;
            width: 100%;
            display: none; /* Hide answers initially */
            padding: 10px; /* Add padding for better alignment */
            background-color: #ffffff; /* White background for the answer */
            border-radius: 5px; /* Rounded corners */
            border-top: 1px solid #dee2e6; /* Border between question and answer */
        }



        .answer p {
            padding-top: 1rem;
            line-height: 1.6;
            font-size: 1.4rem;
        }

        .faq.active .answer {
            max-height: 300px;
            animation: fade 1s ease-in-out;
        }


        .faq.active svg {
            transform: rotate(180deg);
        }

        svg {
            transition: transform  5s ease-in;
        }


        @keyframes fade {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0px);
            }
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

    <section id="about" class="section about">
            <div class="content d-flex justify-content-center align-items-center" data-aos="fade-up">

            <div class="card mb-3 card-1">
            <div class="row g-0">
                <div class="col-md-4">
                <img src="./assets/images/logov3.png" class="img-fluid rounded-start" alt="...">
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


      <!-- FAQS SECTION -->
<section>
    <h2 class="title d-flex justify-content-center">FAQs</h2>

    <div class="faq-container d-flex justify-content-center flex-column align-items-center">
        <div class="faq">
            <div class="question">
                <h3>What is included in the rental cost?</h3>
                <svg class="toggle-icon" width="15" height="10" viewBox="0 0 42 25">
                    <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="answer">
                <p>
                Utilities (e.g. water and electricity) is paid separately from the rent        
                </p>
            </div>
        </div>

        <div class="faq">
            <div class="question">
                <h3>Are there rules about the number of occupants?</h3>
                <svg class="toggle-icon" width="15" height="10" viewBox="0 0 42 25">
                    <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="answer">
                <p>
                3 - 4 occupants in the single-floor apartments and 
                4 - 5 occupants in the up-and-down apartments
                </p>
            </div>
        </div>

        <div class="faq">
            <div class="question">
                <h3>Are pets allowed?</h3>
                <svg class="toggle-icon" width="15" height="10" viewBox="0 0 42 25">
                    <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="answer">
                <p>
                Yes, but only 1 pet is allowed per unit. They are required to stay indoors at all times.
                </p>
            </div>
        </div>

        <div class="faq">
            <div class="question">
                <h3>What documents do I need to rent an apartment?</h3>
                <svg class="toggle-icon" width="15" height="10" viewBox="0 0 42 25">
                    <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="answer">
                <p>
                - A valid government-issued ID
                - Proof of income 
                </p>
            </div>
        </div>

        <div class="faq">
            <div class="question">
                <h3>Is public transportation accessible from the apartment?</h3>
                <svg class="toggle-icon" width="15" height="10" viewBox="0 0 42 25">
                    <path d="M3 3L21 21L39 3" stroke="white" stroke-width="7" stroke-linecap="round"/>
                </svg>
            </div>
            <div class="answer">
                <p>
                The apartment is located near the local jeepney and bus terminal in Binan City 
                </p>
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

          // FAQS 
          document.addEventListener("DOMContentLoaded", function() {
            var questions = document.querySelectorAll(".question");
            questions.forEach(function(question) {
                question.addEventListener("click", function() {
                    var answer = this.nextElementSibling;
                    if (answer.style.display === "none" || answer.style.display === "") {
                        answer.style.display = "block";
                    } else {
                        answer.style.display = "none";
                    }
                });
            });
        });




</script>
</body>

</html>
