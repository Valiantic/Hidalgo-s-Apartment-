<?php
session_start();
include "../connections.php";

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$current_page = basename($_SERVER['PHP_SELF']); 


// Get list of all tenants
$tenant_query = "SELECT * FROM tenant_users WHERE role = 'user'";
$tenant_result = mysqli_query($conn, $tenant_query);

// Get selected tenant's messages if any
$selected_tenant = isset($_GET['tenant_id']) ? $_GET['tenant_id'] : null;


// Get count of new messages for each tenant
$new_messages_query = "
    SELECT sender_id, COUNT(*) AS new_messages 
    FROM messages 
    WHERE receiver_id = ? AND is_read = 0 
    GROUP BY sender_id";
$new_messages_stmt = $conn->prepare($new_messages_query);
$new_messages_stmt->bind_param("i", $_SESSION['user_id']);
$new_messages_stmt->execute();
$new_messages_result = $new_messages_stmt->get_result();
$new_messages_count = [];
while ($row = $new_messages_result->fetch_assoc()) {
    $new_messages_count[$row['sender_id']] = $row['new_messages'];
}
// Mark messages as read when a tenant is selected
if ($selected_tenant) {
    $mark_as_read_query = "UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ?";
    $mark_as_read_stmt = $conn->prepare($mark_as_read_query);
    $mark_as_read_stmt->bind_param("ii", $selected_tenant, $_SESSION['user_id']);
    $mark_as_read_stmt->execute();
    $mark_as_read_stmt->close();
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


     <!-- GOOGLE FONTS POPPINS  -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
   
   <style>
        .chat-container {
            height: 60vh;
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

        <li class="items  <?php echo $current_page == 'dashboard.php' ? 'active-menu' : ''; ?>">
            <a href="dashboard.php"><i class="fa-solid fa-chart-simple"></i></a>
            <p class="para"><a href="dashboard.php">Dashboard</a></p>
        </li>

        <li class="items <?php echo $current_page == 'units.php' ? 'active-menu' : ''; ?>">
            <a href="units.php"><i class="fa-solid fa-home"></i></i></a>
            <p class="para"><a href="units.php">Units</a></p>
        </li>

        <li class="items <?php echo $current_page == 'tenants.php' ? 'active-menu' : ''; ?>">
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
            <div class="row justify-content-center">
                
            <div class="container mt-4">
        <div class="row">
            <!-- Tenant List -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5>Tenants</h5>
                    </div>
                    <div class="card-body shadow-lg">
                        <div class="list-group">
                            <?php while($tenant = mysqli_fetch_assoc($tenant_result)): ?>
                                <a href="?tenant_id=<?php echo $tenant['user_id']; ?>" 
                                   class="list-group-item list-group-item-action <?php echo ($selected_tenant == $tenant['user_id']) ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($tenant['fullname']); ?>
                                    <?php if (isset($new_messages_count[$tenant['user_id']])): ?>
                                        <span class="badge bg-danger rounded-pill ms-2"><?php echo $new_messages_count[$tenant['user_id']]; ?></span>
                                    <?php endif; ?>
                                    <br>
                                    <small>Unit: <?php echo htmlspecialchars($tenant['units']); ?></small>
                                </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
                                
            <!-- Chat Area -->
            <div class="col-md-8">
                <?php if($selected_tenant): ?>
                    <?php
                    $tenant_query = "SELECT fullname FROM users WHERE id = ?";
                    $stmt = mysqli_prepare($conn, $tenant_query);
                    mysqli_stmt_bind_param($stmt, "i", $selected_tenant);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $tenant_name = mysqli_fetch_assoc($result)['fullname'];
                    ?>
                    <div class="card">
                        <div class="card-header">
                            <h5>Chat with <?php echo htmlspecialchars($tenant_name); ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="chat-container" id="chatContainer">
                                <?php
                                $message_query = "SELECT * FROM messages WHERE 
                                    (sender_id = ? AND receiver_id = ?) OR
                                    (sender_id = ? AND receiver_id = ?)
                                    ORDER BY timestamp ASC";
                                $stmt = mysqli_prepare($conn, $message_query);
                                mysqli_stmt_bind_param($stmt, "iiii", $_SESSION['user_id'], $selected_tenant, $selected_tenant, $_SESSION['user_id']);
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
                                <input type="hidden" name="receiver_id" value="<?php echo $selected_tenant; ?>">
                                <div class="input-group">
                                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                                    <button type="submit" class="btn btn-primary">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Select a tenant to start messaging</div>
                <?php endif; ?>
            </div>
        </div>
    </div>


            </div>
        </div>
    </div>


   

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-scroll to bottom of chat
        const chatContainer = document.getElementById('chatContainer');
        if (chatContainer) {
            chatContainer.scrollTop = chatContainer.scrollHeight;
        }

        // Refresh chat every 5 seconds
        // setInterval(function() {
        //     if (window.location.search.includes('tenant_id')) {
        //         location.reload();
        //     }
        // }, 5000);


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