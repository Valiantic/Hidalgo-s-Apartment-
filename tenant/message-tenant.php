<?php
session_start();
include '../connections.php';

// Check if user is logged in and is a tenant
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']);


// Get admin user
$admin_query = "SELECT id, fullname FROM users WHERE role = 'admin' LIMIT 1";
$admin_result = mysqli_query($conn, $admin_query);
$admin = mysqli_fetch_assoc($admin_result);
$admin_id = $admin['id'];
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
        .chat-container {
            height: 70vh;
            overflow-y: auto;
        }
        .message-bubble {
            max-width: 70%;
            margin: 10px;
            padding: 10px;
            border-radius: 10px;
        }
        .sender {
            background-color: #007bff;
            color: white;
            margin-left: auto;
        }
        .receiver {
            background-color: #e9ecef;
        }
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

        .sidebar .active-menu {
            background: black;
            color: white;
        }
        .sidebar .active-menu a {
            color: white;
        }


    </style>
</head>
<body class="bg-light">

<div class="menu">
    <div class="sidebar">
        <div class="logo items">
            <span class="mainHead para">
                <h5>Hidalgo's</h5>
                <h4>Apartment</h4>
            </span>
        </div>

        <li class="items  <?php echo $current_page == 'home.php' ? 'active-menu' : ''; ?>">
            <a href="home.php"><i class="fa-solid fa-person"></i></a>
            <p class="para"><a href="home.php">Dashboard</a></p>
        </li>


        <li class="items <?php echo $current_page == 'message-tenant.php' ? 'active-menu' : ''; ?>">
            <a href="message-tenant.php"> <i class="fa-solid fa-message"></i></a>
            <p class="para"><a href="message-tenant.php">Message</a></p>
        </li>

        <li class="items <?php echo $current_page == 'settings.php' ? 'active-menu' : ''; ?>">
            <a href="settings.php"> <i class="fa-solid fa-gear"></i></a>
            <p class="para"><a href="settings.php">Settings</a></p>
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
        <div class="container-fluid ">
            <div class="row justify-content-center gap-4">

            <div class="container">
        <div class="card shadow">
            <div class="card-header">
                <h5>Chat with Admin (<?php echo htmlspecialchars($admin['fullname']); ?>)</h5>
            </div>
            <div class="card-body">
                <div class="chat-container" id="chatContainer">
                    <?php
                    $message_query = "SELECT * FROM messages WHERE 
                        (sender_id = ? AND receiver_id = ?) OR
                        (sender_id = ? AND receiver_id = ?)
                        ORDER BY timestamp ASC";
                    $stmt = mysqli_prepare($conn, $message_query);
                    mysqli_stmt_bind_param($stmt, "iiii", $_SESSION['user_id'], $admin_id, $admin_id, $_SESSION['user_id']);
                    mysqli_stmt_execute($stmt);
                    $messages = mysqli_stmt_get_result($stmt);
                    
                    while($message = mysqli_fetch_assoc($messages)):
                    ?>
                        <div class="message-bubble <?php echo ($message['sender_id'] == $_SESSION['user_id']) ? 'sender' : 'receiver'; ?>">
                            <?php echo htmlspecialchars($message['message_text']); ?>
                            <br>
                            <small><?php echo date('M d, Y H:i', strtotime($message['timestamp'])); ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
                <form method="POST" action="./req/send-message.php" class="mt-3">
                    <input type="hidden" name="receiver_id" value="<?php echo $admin_id; ?>">
                    <div class="input-group">
                        <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                        <button type="submit" class="btn btn-primary">Send</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



            </div>
        </div>
    </div>

</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Auto-scroll to bottom of chat
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }


    </script>
</body>
</html>