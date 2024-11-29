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
        body {
            margin: 0;
            padding: 0;
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

        #homeNav .nav-link {
            color: white !important;
            text-decoration: none;
        }
        
        .welcome-text {
            min-height: 80vh;
        }
        .welcome-text img {
            height: 90px;
            width: 135px;
        }
        .welcome-text h4 {
            color: #eee;
            font-size: 51px;
            font-family: "Lobster", sans-serif;
        }
        .welcome-text p {
            color: white;
            background-color: rgb(102, 153, 255);
            padding: 5px;
            border-radius: 4px;
        }
        #about {
            min-height: 100vh;
        }
        #about .card-1 {
            max-width: 600px;
            width: 90%;
            background: white;
            padding: 20px;
            border-radius: 20px;
        }
        #about .card-1 h5 {
            font-family: "Lobster", sans-serif;
            font-size: 25px;
        }
        #contacts {
            min-height: 100vh;
        }
        #contacts form {
            max-width: 600px;
            width: 90%;
            background: white;
            padding: 20px;
            border-radius: 20px;
        }
        #contacts form h3 {
            text-align: center;
            font-family: "Lobster", sans-serif;
        }
        textarea {
            resize: none;
        }
        #logintxt {
            transition: background-color 0.3s ease-in-out;
        }
        .welcome-div {
          width: 100%;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('./assets/images/home_bg.png');
            background-size: cover;
            background-position: center;
            text-align: center;
        }
        @media (max-width: 768px) {
            #welcome {
                height: auto;
                padding: 20px;
            }
        }
    </style>
</head>
<body class="body-home">
<div class="black-fill">
    <br>
    <div class="container">
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
                            <a class="nav-link" href="#contacts">Contact</a>
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
        <div data-aos="zoom-in" class="welcome-div">
            <section id="welcome" class="welcome-text d-flex justify-content-center align-items-center flex-column">
                <img src="./assets/images/logo.png">
                <h4>Welcome to Hidalgo's Apartment</h4>
                <p>Since 1980's</p>
            </section>
        </div>
        <section id="about" class="d-flex justify-content-center align-items-center flex-column">
            <div data-aos="fade-up">
                <div class="card mb-3 card-1">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="./assets/images/logo.png" class="img-fluid rounded-start" alt="...">
                        </div>
                        <div class="col-md-8">
                            <div class="card-body">
                                <h5 class="card-title">About</h5>
                                <p class="card-text">A well-preserved property that was built from around the 1980s. Located in the neighborhood of Alunos Subdivision, Barangay Sto. Domingo in Biñan City. The property has 2 single-floor and 3 up-and-down space apartments with a single space garage to park your motorcycle or any small vehicle. </p>
                                <p class="card-text"><small class="text-body-secondary"></small></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section id="contacts" class="d-flex justify-content-center align-items-center flex-column">
            <form method="post" action="req/contact.php">
                <h3>Reach us!</h3>
                <?php if (isset($_GET['error'])) { ?>
                    <div class="alert alert-danger" role="alert">
                        <?=$_GET['error']?>
                    </div>
                <?php } ?>
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
                    <textarea class="form-control" name="message" rows="4"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send</button>
            </form>
        </section>
        <div class="text-center text-light">
            Copyright &copy; 2024. All rights reserved.
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- ANIMATE ON SCROLL -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>
<script>
    AOS.init();
</script>
</body>
</html>
