<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

$fullname = $_SESSION['fullname'];
$phone_number = $_SESSION['phone_number'];
$first_name = explode(' ', $fullname)[0]; 

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']);


$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT tenant_id, move_in_date, units FROM tenant WHERE user_id = ?");
$stmt->execute([$user_id]);
$tenant = $stmt->fetch();
$tenant_id = $tenant['tenant_id'];
$start_date = $tenant['move_in_date'] ? date('m/d/y', strtotime($tenant['move_in_date'])) : 'N/A';
$unit = $tenant['units'];
$unit_number = (int) filter_var($unit, FILTER_SANITIZE_NUMBER_INT);

function getMonthlyRent($unit) {
    $rent_prices = [
        'Unit 1' => 3500,
        'Unit 2' => 3500,
        'Unit 3' => 6500,
        'Unit 4' => 6500,
        'Unit 5' => 6500,
        
    ];
    return isset($rent_prices[$unit]) ? $rent_prices[$unit] : 'N/A';
}

$monthly_rent = getMonthlyRent($unit);


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

$maintenance_query = "SELECT unit, status FROM maintenance_request WHERE tenant_id = ? ORDER BY request_id DESC LIMIT 1";
$maintenance_stmt = $pdo->prepare($maintenance_query);
$maintenance_stmt->execute([$tenant_id]);
$maintenance_status = $maintenance_stmt->fetch();

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

// Fetch the latest transaction date by the tenant to determine the due date
$transaction_query = "
    SELECT MAX(transaction_date) as latest_transaction_date
    FROM transaction_info
    WHERE tenant_id = ?
";
$transaction_stmt = $conn->prepare($transaction_query);
$transaction_stmt->bind_param("i", $tenant_id);
$transaction_stmt->execute();
$transaction_result = $transaction_stmt->get_result();
$latest_transaction = $transaction_result->fetch_assoc();
$latest_transaction_date = $latest_transaction['latest_transaction_date'] ?? null;
$due_date = $latest_transaction_date ? date('m/d/y', strtotime($latest_transaction_date . ' +1 month')) : 'N/A';

// Check if the tenant has a pending appointment
$appointment_query = "SELECT * FROM appointments WHERE tenant_id = ? AND appointment_status = 'pending'";
$appointment_stmt = $pdo->prepare($appointment_query);
$appointment_stmt->execute([$tenant_id]);
$pending_appointment = $appointment_stmt->fetch();

// Check if the tenant has a confirmed appointment
$confirmed_appointment_query = "SELECT * FROM appointments WHERE tenant_id = ? AND appointment_status = 'confirmed'";
$confirmed_appointment_stmt = $pdo->prepare($confirmed_appointment_query);
$confirmed_appointment_stmt->execute([$tenant_id]);
$confirmed_appointment = $confirmed_appointment_stmt->fetch();

// Check if the tenant has paid the monthly rent, electricity, and water bills
$billing_paid = $billing_info && $billing_info['monthly_rent_status'] === 'Paid' && $billing_info['electricity_status'] === 'Paid' && $billing_info['water_status'] === 'Paid';

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
     <!-- SweetAlert2 -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/sweetalert2.all.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.0/sweetalert2.min.css" rel="stylesheet">

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
        <div class="container-fluid mt-4">
            <div class="row justify-content-center gap-4">

            <h1 data-aos="fade-right" class="display- text-black fw-bold">Welcome, <?php echo htmlspecialchars($first_name); ?>!</h1>
               

            <div class="card text-dark shadow bg-light mb-3 p-3" style="max-width: 18rem;">
            <div class="card-title fs-4 text-center">Tenant Information</div>
            <div class="card-body">
                <label>Tenant Fullname</label>
                <h5 class="card-text"><?php echo htmlspecialchars($fullname)?></h5>
                <label>Tenant Phone number</label>
                <h5 class="card-text"><?php echo htmlspecialchars($phone_number)?></h5>
                <h5>Unit: <?php echo $unit_number; ?></h5>
                <label>Move in Date</label>
                <h5 class="card-text"><?php echo $start_date; ?></h5>
                <label>Due Date</label>
                <h5 class="card-text"><?php echo $due_date; ?></h5>
            </div>
            </div>

            <div class="card text-left text-dark shadow bg-light mb-3 p-3" style="max-width: 18rem;">
            <div class="card-title fs-4 text-center">Your Billings</div>
            <div class="card-body">
                <!-- YOUR BILLINGS DISPLAY IF THE TENANT HAS PAID ALL BILLS -->
                <?php if ($billing_paid): ?>
                    <label>Monthly Rent <span style="color: blue;">₱<?php echo htmlspecialchars($monthly_rent); ?></span></label>
                    <p class="card-text"><?php echo displayStatus($billing_info['monthly_rent_status']); ?></p>
                    <label>Electricity Bill</label>
                    <p class="card-text"><?php echo displayStatus($billing_info['electricity_status']); ?></p>
                    <label>Water Bill</label>
                    <p class="card-text"><?php echo displayStatus($billing_info['water_status']); ?></p>
                <!-- YOUR BILLINGS DISPLAY IF THE TENANT HAS NOT PAID ALL BILLS -->
                <?php else: ?>
                    <label>Downpayment <span style="color: blue;">₱<?php echo $unit_number <= 2 ? '3500' : '6500'; ?></span></label>
                    <p class="card-text"><span style="color: gray;">●</span> Not Paid</p>
                    <label>Advance <span style="color: blue;">₱<?php echo $unit_number <= 2 ? '3500' : '6500'; ?></span></label>
                    <p class="card-text"><span style="color: gray;">●</span> Not Paid</p>
                    <label>Electricity Bill <span style="color: blue;">₱1000</span></label>
                    <p class="card-text"><span style="color: gray;">●</span> Not Paid</p>
                    <label>Water Bill <span style="color: blue;">₱500</span></label>
                    <p class="card-text"><span style="color: gray;">●</span> Not Paid</p>
                <?php endif; ?>
            </div>
            </div>

            <div class="card text-left text-dark shadow bg-light mb-3 p-3" style="max-width: 18rem;">
            <div class="card-title fs-4 text-center">Actions</div>
            <div class="card-body d-flex flex-column gap-2">
                <?php if ($billing_paid && $start_date === 'N/A'): ?>
                    <h6>Your payment has been received. you now rent <strong class="text-primary">Unit <?php echo $unit_number; ?>!</strong></h6>
                    <h6>Please wait for the contract it will soon generate once you moved in to the unit!</h6>
                    <button class="btn btn-success btn-lg text-white fs-6">Transaction Complete!</button>
                <?php elseif ($start_date !== 'N/A'): ?>
                    <?php if ($maintenance_status): ?>
                        <div class="alert alert-info text-center">
                            Maintenance Status: <span style="color: <?php echo $maintenance_color; ?>;">●</span> <?php echo $maintenance_text; ?>
                            <?php if ($maintenance_status['status'] == 'Resolved'): ?>
                            <form action="req/confirm-maintenance.php" method="POST">
                                <input type="hidden" name="tenant_id" value="<?php echo $tenant_id; ?>">
                                <button type="submit" class="btn btn-success btn-lg">Confirm</button>
                            </form>
                        <?php endif; ?>
                        
                        </div>
                        
                    <?php endif; ?>
                    <br/>
                    <br/>
                    <a href="report-issue.php" class="btn btn-warning btn-lg text-white">Report Issue</a>
                    <a href="contract-page.php?unit=<?php echo $unit_number; ?>" class="btn btn-ocean btn-lg">View Contract</a>
                <?php else: ?>
                    <?php if ($confirmed_appointment): ?>
                        <h6>Your <strong class="text-success">Appointment Request</strong> has been confirmed by the Admin.</h6>
                        <h6>You can now go to the compound for transaction deposit located at <strong class="text-primary">Alunos Subdivision, Barangay Sto. Domingo in Biñan City.</strong></h6>
                        <a href="https://www.google.com/maps/place/Bi%C3%B1an,+Laguna/@14.3377245,121.0812316,646m/data=!3m1!1e3!4m15!1m8!3m7!1s0x3397d9e2cd1290f7:0xddb7903b387fc1b5!2zQmnDsWFuLCBMYWd1bmE!3b1!8m2!3d14.3377788!4d121.0812494!16s%2Fg%2F11g5_vj73m!3m5!1s0x3397d9e2cd1290f7:0xddb7903b387fc1b5!8m2!3d14.3377788!4d121.0812494!16s%2Fg%2F11g5_vj73m?entry=ttu&g_ep=EgoyMDI0MTIxMS4wIKXMDSoASAFQAw%3D%3D" target="_blank" class="btn btn-success btn-lg text-white fs-6">Show me the way</a>
                    <?php elseif ($pending_appointment): ?>
                        <h6>Your <strong class="text-danger">Appointment Request</strong> is now pending to the Admin, We will notify you later for confirmation.</h6>
                        <button class="btn btn-primary btn-lg text-white fs-6">Pending Appointment ...</button>
                        <button class="btn btn-warning btn-lg text-white fs-6" id="pending-appointment-btn">Cancel Appointment</button>
                    <?php else: ?>
                        <h6>Hello <strong><?php echo htmlspecialchars($fullname)?>!</strong> Thank you for Choosing Hidalgo's Apartment</h6>
                        <h6>Schedule an Appointment to Rent this Unit.</h6>
                        <a href="make-appointment.php?unit=<?php echo $unit_number; ?>" class="btn btn-primary btn-lg">Make Appointment</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            </div>

            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    

<!-- ANIMATE ON SCROLL -->
<script src="https://unpkg.com/aos@next/dist/aos.js"></script>

  <!-- SweetAlert2 -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.all.min.js"></script>

<script>

    AOS.init();

    const toggler = document.querySelector('.toggler')
    const sidebar = document.querySelector('.sidebar')

    const showFull = () => {
        toggler.addEventListener('click', ()=> {
            toggler.classList.toggle('active')
            sidebar.classList.toggle('active')
        })
    }


    showFull()

    // JS END DATE CALCULATION
    // const startDate = new Date("<?php echo $start_date; ?>");
    // const endDate = new Date(startDate);
    // endDate.setMonth(startDate.getMonth() + 1);
    // const formattedEndDate = endDate.toLocaleDateString('en-US');
    // document.getElementById('end-date').textContent = formattedEndDate;

    // SweetAlert2 for canceling appointment
    document.getElementById('pending-appointment-btn').addEventListener('click', function() {
        Swal.fire({
            title: 'Appointment is Pending',
            text: "Would you want to cancel your appointment?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                // Make an AJAX request to cancel the appointment
                fetch('./req/cancel-appointment.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tenant_id: <?php echo $tenant_id; ?> })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire(
                            'Cancelled!',
                            'Your appointment has been cancelled.',
                            'success'
                        ).then(() => {
                            location.reload(); // Reload the page to reflect changes
                        });
                    } else {
                        Swal.fire(
                            'Error!',
                            'There was an error cancelling your appointment.',
                            'error'
                        );
                    }
                })
                .catch(error => {
                    Swal.fire(
                        'Error!',
                        'There was an error cancelling your appointment.',
                        'error'
                    );
                });
            }
        });
    });
</script>

</body>
</html>
<?php
$conn->close();
?>
