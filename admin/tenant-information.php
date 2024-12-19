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

function rentButton($status, $tenant_id, $unit_name) {
    if ($status == '<p class="fs-4 fw-bold text-center text-warning">Available</p>') {
        return ""; 
    } else {
        return "
        <div class='button-group'>
            <form method='POST' action='update_billing_status.php' style='display: inline;'>
                <input type='hidden' name='tenant_id' value='" . htmlspecialchars($tenant_id) . "'>
                <input type='hidden' name='unit' value='" . htmlspecialchars($unit_name) . "'>
                <button type='submit' class='btn btn-primary custom-btn-font text-white text'>Update Details</button>
            </form>
            <button type='submit' class='btn btn-success custom-btn-font text-white text'>View Contract</button>
            <button type='submit' class='btn btn-danger custom-btn-font text-white text'>Terminate Lease</button>
        </div>";
    }
}

$rent = rentButton($status, $tenant_id, $unit_name);


$maintenance_query = "SELECT unit, COUNT(*) AS count FROM maintenance_request GROUP BY unit";
$maintenance_result = $conn->query($maintenance_query);

$maintenance_status = [];
while ($row = $maintenance_result->fetch_assoc()) {
    $maintenance_status[$row['unit']] = $row['count'] > 0 ? 'Pending' : 'No Issues';
}

$maintenance_color = isset($maintenance_status[$unit_name]) && $maintenance_status[$unit_name] == 'Pending' ? 'red' : 'green';
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
        background: aliceblue;
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
        color: aliceblue;
    }
    
    .logout-btn{
        margin-top: 30px;
        color: #B70202;
    }
    
    .logout-btn:hover{
        background-color: #B70202;
        color: aliceblue;
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
        left: 150px;
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
<body>
    
   

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
        <li class="items <?php echo $current_page == 'message.php' ? 'active-menu' : ''; ?>">
            <a href="message.php"> <i class="fa-solid fa-message"></i></a>
            <p class="para"><a href="message.php">Message</a></p>
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

            <div class="col-12 col-md-6 mb-3">
                <div class="card" data-aos="fade-up">
                    <!-- TENANT INFORMATION CARD -->
                    <div class="card-body">
                        <a href="units.php">Back</a>
                        <h1 class="text-center">Unit Information</h1>

                        <!-- DATA TO PASS FOR BILLING INFORMATION -->
                        <input type="hidden" name="tenant_id" value="<?php echo htmlspecialchars($user_id); ?>">
                        <input type="hidden" name="unit" value="<?php echo htmlspecialchars($unit); ?>">

                                        <!-- ERROR HANDLING  -->
                        <?php if (isset($_GET['error'])) { ?>
                                <div class="alert alert-danger mt-3 n-table" role="alert">
                                <?=$_GET['error']?>
                            </div>
                            <?php } ?>

                                        <!-- SUCCESS HANDLING FOR TEACHER-DELETE -->
                            <?php if (isset($_GET['success'])) { ?>
                                <div class="alert alert-info mt-3 n-table" role="alert">
                                <?=$_GET['success']?>
                            </div>
                        <?php } ?>


                        <img src="<?php echo $img_src; ?>" class="card-img-top height-img" alt="Unit Image">
                        <h1 class="card-title text-center"><?php echo $unit_name; ?></h1>
                        <p class="card-text text-center">Status: <?php echo $status; ?></p>
                        <h2 class="card-subtitle mb-2 text-center"><?php echo $type; ?></h2>
                        <?php if ($status != '<p class="fs-4 fw-bold text-center text-warning">Available</p>'): ?>
                            <p class="card-text text-center">Maintenance Status: <span style="color: <?php echo $maintenance_color; ?>;">●</span></p>
                            <hr>
                            <h3 class="text-center">Tenant Information</h3>
                            <form action="./req/update_billing_status.php" method="post">
                                <input type="hidden" name="unit_number" value="<?php echo $unit_number; ?>">
                                <p class="text-left fs-4">Full Name: <?php echo $tenant_fullname; ?></p>
                                <p class="text-left fs-4">Phone Number: <?php echo $tenant_phone; ?></p>
                                <p class="text-left fs-4">Start Date: <?php echo $start_date; ?></p>
                                <p class="text-left fs-4">Due Date: <?php echo $due_date; ?></p>
                                <hr>
                                <h3 class="text-center">Billing Information</h3>
                                <p class="fs-4">Monthly Bill:<br>
                                    <div class="radio-group">
                                        <input class="card-text fs-6" type="radio" name="monthly_rent_status" value="Paid"> paid
                                        <input class="card-text" type="radio" name="monthly_rent_status" value="Not Paid"> not paid
                                        <input type="radio" name="monthly_rent_status" value="No Bill Yet" checked> no bill yet
                                    </div>
                                </p>
                                <p class="fs-4">Electricity Bill:<br>
                                    <div class="radio-group">
                                        <input class="card-text" type="radio" name="electricity_status" value="Paid"> paid
                                        <input class="card-text" type="radio" name="electricity_status" value="Not Paid"> not paid
                                        <input type="radio" name="electricity_status" value="No Bill Yet" checked> no bill yet
                                    </div>
                                </p>
                                <p class="fs-4">Water Bill:<br>
                                    <div class="radio-group">
                                        <input class="card-text" type="radio" name="water_status" value="Paid"> paid
                                        <input class="card-text" type="radio" name="water_status" value="Not Paid"> not paid
                                        <input type="radio" name="water_status" value="No Bill Yet" checked> no bill yet
                                    </div>
                                </p>
                                <hr/>
                               
                                <?php echo $rent; ?>
                                
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
                
            </div>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

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
