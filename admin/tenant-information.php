<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']);

$unit_number = isset($_GET['unit']) ? (int)$_GET['unit'] : null;

if ($unit_number < 1 || $unit_number > 5) {
    die("Invalid unit number. Please select a unit between 1 and 5.");
}

$unit_name = "Unit $unit_number";

$sql = "SELECT * FROM tenant WHERE units = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $unit_name);
$stmt->execute();
$result = $stmt->get_result();
$tenant_info = $result->fetch_assoc();

$tenant_id = $tenant_info['tenant_id'] ?? null;
$tenant_fullname = $tenant_info['fullname'] ?? 'N/A';
$tenant_phone = $tenant_info['phone_number'] ?? 'N/A';
$start_date = $tenant_info['move_in_date'] ?? 'N/A';
$due_date = $start_date !== 'N/A' ? date('Y-m-d', strtotime($start_date . ' +1 month')) : 'N/A';

$status = ($result->num_rows > 0) ? '<p class="fs-4 text-muted text-center">Occupied</p>' : '<p class="fs-4 fw-bold text-center text-warning">Available</p>';

// Function to determine unit type
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
        return $status == '<p class="fs-4 text-muted text-center">Occupied</p>' ? '../assets/images/icons/house2.png' : '../assets/images/icons/rent-house2.png';
    } else {
        return $status == '<p class="fs-4 text-muted text-center">Occupied</p>' ? '../assets/images/icons/house1.png' : '../assets/images/icons/rent-house1.png';
    }
}

$img_src = getUnitImage($unit_number, $status);

function rentButton($status, $tenant_id, $unit_number) {
    if ($status == '<p class="fs-4 fw-bold text-center text-warning">Available</p>') {
        return ""; 
    } else {
        return "
        <div class='button-group d-flex justify-content-center'>
            <form method='POST' action='update_billing_status.php' style='display: inline;'>
                <input type='hidden' name='tenant_id' value='" . htmlspecialchars($tenant_id) . "'>
                <input type='hidden' name='unit' value='" . htmlspecialchars($unit_number) . "'>
                <button type='submit' class='btn btn-ocean custom-btn-font text-white text'>Update Details</button>
            </form>
            <a href='contract-page.php?unit=" . htmlspecialchars($unit_number) . "' class='btn btn-success custom-btn-font text-white text'>View Contract</a>
            <a href='#' class='btn btn-danger custom-btn-font text-white text terminate-lease' data-tenant-id='" . htmlspecialchars($tenant_id) . "'>Terminate Lease</a>
        </div>";
    }
}

$rent = rentButton($status, $tenant_id, $unit_number);


$stmt = $pdo->prepare("SELECT monthly_rent_status, electricity_status, water_status 
                       FROM transaction_info 
                       WHERE tenant_id = ? 
                       ORDER BY transaction_id DESC 
                       LIMIT 1");
$stmt->execute([$tenant_id]);
$billing_info = $stmt->fetch();

function displayStatus($status) {
    switch ($status) {
        case 'Paid':
            return '<span style="color: green;">●</span> Paid';
        case 'Not Paid':
            return '<span style="color: red;">●</span> Not Paid';
        default:
            return '<span style="color: gray;">●</span> No Bill Yet';
    }
}


$maintenance_query = "SELECT unit, status FROM maintenance_request WHERE unit = ? ORDER BY request_id DESC LIMIT 1";
$maintenance_stmt = $conn->prepare($maintenance_query);
$maintenance_stmt->bind_param("s", $unit_name);
$maintenance_stmt->execute();
$maintenance_result = $maintenance_stmt->get_result();
$maintenance_status = $maintenance_result->fetch_assoc();

$maintenance_color = 'gray';
$maintenance_text = 'No Issues';

if ($maintenance_status) {
    switch ($maintenance_status['status']) {
        case 'Pending':
            $maintenance_color = 'red';
            $maintenance_text = 'Pending';
            break;
        case 'In Progress':
            $maintenance_color = 'yellow';
            $maintenance_text = 'In Progress';
            break;
        case 'Resolved':
            $maintenance_color = 'green';
            $maintenance_text = 'Resolved';
            break;
    }
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
    h4, h5 {
        font-family: 'Poppins', 'sans-serif';
        font-size: 20px;
        font-weight: 500;
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

    .radio-group {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-size: 1.7rem;
    }
    .radio-group input[type="radio"] {
        display: inline-block;
        margin-right: 2px;
        transform: scale(1.5); 
    }

    .button-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }

    @media (min-width: 768px) {
        .button-group {
            flex-direction: row;
        }
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

        <li class="items <?php echo $current_page == 'dashboard.php' ? 'active-menu' : ''; ?>">
            <a href="dashboard.php"><i class="fa-solid fa-chart-simple"></i></a>
            <p class="para"><a href="dashboard.php">Dashboard</a></p>
        </li>

        <li class="items <?php echo $current_page == 'tenant-information.php' ? 'active-menu' : ''; ?>">
            <a href="units.php"><i class="fa-solid fa-home"></i></i></a>
            <p class="para"><a href="units.php">Units</a></p>
        </li>

        <li class="items <?php echo $current_page == 'tenant.php' ? 'active-menu' : ''; ?>">
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
                
                <div class="col-12 mb-3">
                    <div class="card shadow-lg" data-aos="fade-up">
                        <?php if ($status == '<p class="fs-4 fw-bold text-center text-warning">Available</p>'): ?>
                            <div class="text-center p-3">


                            <img src="<?php echo $img_src; ?>" class="card-img-top img-fluid height-img mb-3" alt="Unit Image">
                                <h1 class="card-title"><?php echo $unit_name; ?></h1>
                                <p class="card-text">Status: <?php echo $status; ?></p>
                                <h2 class="card-subtitle mb-2"><?php echo $type; ?></h2>
                                <a href="add-tenant.php?unit=<?php echo $unit_number; ?>" class="btn btn-primary">Add Tenant</a>
                            </div>
                        <?php else: ?>
                            <div class="row g-0">
                                <div class="col-md-4 d-flex flex-column align-items-center p-3">
                                    <img src="<?php echo $img_src; ?>" class="card-img-top img-fluid height-img mb-3" alt="Unit Image">
                                    <h1 class="card-title"><?php echo $unit_name; ?></h1>
                                    <p class="card-text">Status: <?php echo $status; ?></p>
                                    <h2 class="card-subtitle mb-2"><?php echo $type; ?></h2>
                                    <p class="card-text"> <a class='text-primary text-decoration-underline' href='unit-maintenance.php?unit=<?php echo $unit_number; ?>'>Maintenance Status: </a><span style="color: <?php echo $maintenance_color; ?>;">●</span> <?php echo $maintenance_text; ?></p>
                                   

                                </div>
                                <div class="col-md-8">
                                    <div class="card-body">
                                        <hr>
                                        <h3 class="text-center">Tenant Information</h3>
                                        <form action="./req/update_billing_status.php" method="post">
                                            <input type="hidden" name="unit_number" value="<?php echo $unit_number; ?>">
                                            <p class="fs-4">Full Name: <?php echo $tenant_fullname; ?></p>
                                            <p class="fs-4">Phone Number: <?php echo $tenant_phone; ?></p>
                                            <p class="fs-4">Start Date: <?php echo $start_date; ?></p>
                                            <p class="fs-4">Due Date: <?php echo $due_date; ?></p>
                                            <hr>
                                            <h3 class="text-center">Billing Information</h3>
                                            <p class="fs-4">Monthly Bill: <?php echo $billing_info ? displayStatus($billing_info['monthly_rent_status']) : displayStatus(null); ?><br>
                                                <div class="radio-group">
                                                    <input class="card-text fs-6" type="radio" name="monthly_rent_status" value="Paid"> paid
                                                    <input class="card-text" type="radio" name="monthly_rent_status" value="Not Paid"> not paid
                                                    <input type="radio" name="monthly_rent_status" value="No Bill Yet" checked> no bill yet
                                                </div>
                                            </p>
                                            <p class="fs-4">Electricity Bill: <?php echo $billing_info ? displayStatus($billing_info['electricity_status']) : displayStatus(null); ?><br>
                                                <div class="radio-group">
                                                    <input class="card-text" type="radio" name="electricity_status" value="Paid"> paid
                                                    <input class="card-text" type="radio" name="electricity_status" value="Not Paid"> not paid
                                                    <input type="radio" name="electricity_status" value="No Bill Yet" checked> no bill yet
                                                </div>
                                            </p>
                                            <p class="fs-4">Water Bill: <?php echo $billing_info ? displayStatus($billing_info['water_status']) : displayStatus(null); ?><br>
                                                <div class="radio-group">
                                                    <input class="card-text" type="radio" name="water_status" value="Paid"> paid
                                                    <input class="card-text" type="radio" name="water_status" value="Not Paid"> not paid
                                                    <input type="radio" name="water_status" value="No Bill Yet" checked> no bill yet
                                                </div>
                                            </p>
                                            <hr/>
                                            <?php echo $rent; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>  

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.terminate-lease').forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                const tenantId = this.getAttribute('data-tenant-id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You are about to terminate this lease.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, terminate it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = `req/terminate-lease.php?tenant_id=${tenantId}`;
                    }
                });
            });
        });
    });
</script>


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


    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
      radio.addEventListener('click', function() {
        if (this.wasChecked) {
          this.checked = false;
        }
        document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
          radio.wasChecked = radio.checked;
        });
      });
    });

    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.value !== 'No Bill Yet') {
                document.querySelector('input[type="radio"][name="' + this.name + '"][value="No Bill Yet"]').checked = false;
            }
        });
    });
  
</script>

</body>
</html>
<?php
$conn->close();
?>