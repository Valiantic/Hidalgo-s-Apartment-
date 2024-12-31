<?php
session_start();
include '../connections.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

$current_page = basename($_SERVER['PHP_SELF']); 

$searchKey = isset($_GET['searchKey']) ? $_GET['searchKey'] : '';
$filterUnitOrder = isset($_GET['filterUnitOrder']) ? $_GET['filterUnitOrder'] : 'ASC';

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

       <!-- Bootstrap CSS -->
       <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap JavaScript -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.0/js/bootstrap.bundle.min.js"></script>


     <!-- GOOGLE FONTS POPPINS  -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

    <!-- SWEET ALERT MODAL -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
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
        color: inherit;
        text-decoration: none;
    }
    .tenant-fullname {
        text-decoration: underline;
    }
    h4, h5 {
        font-family: 'Poppins', 'sans-serif';
        font-size: 20px;
        font-weight: 500;
    }

    .w-450{
        width:450px;
        border-radius:20px;
    }
    .n-table{
        max-width: 800px;
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
    font-size: 1.35rem; /* Adjust the size as needed */
    }
    .btn {
        font-family: 'Poppins', 'sans-serif';
        font-size: 15px;
        font-weight: 500;
    }
    /* Custom styling for table */
    .table-rounded {
            border-radius: 14px;
            overflow: hidden; /* Prevent content from overflowing the border radius */
    }

        /* Make table responsive */
    .table-wrapper {
            overflow-x: auto; /* Enable horizontal scrolling */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on touch devices */
    }
    
    .btn-custom {
        min-width: 100px; /* Adjust as needed */
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
        background-color: #4DA1A9;
        color: white;
    }
    .sidebar .active-menu a {
        color: white;
    }



</style>

</head>
<body class="bg-light">
    
    <!-- ADMIN SIDEBAR COMPONENT -->
    <?php

    // include "../components/admin_sidebar.php";

    ?> 

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
          

        <a href="add-tenant.php"
        class="btn btn-ocean mb-3">Add New Tenant</a>

        <a href="tenant-history.php"
        class="btn btn-dark mb-3">View Tenant History</a>

          <!-- SEARCH AND FILTER FORM -->
          <form action="tenants.php" class="smt-3 n-table" method="get">

        <div class="input-group mb-3">
        <input type="text" class="form-control shadow" name="searchKey" placeholder="Search..." value="<?php echo htmlspecialchars($searchKey); ?>">
       
        <button class="btn btn-primary shadow" id="gBtn">
        Search
        <!-- Search button svg icon -->
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
        </svg> 

        </button>
        <select class="form-select shadow ms-2" name="filterUnitOrder" onchange="this.form.submit();" style="max-width: 200px;">
            <option value="ASC" <?php echo $filterUnitOrder == 'ASC' ? 'selected' : ''; ?>>View Unit 1-5</option>
            <option value="DESC" <?php echo $filterUnitOrder == 'DESC' ? 'selected' : ''; ?>>View Unit 5-1</option>
        </select>
        
        </div>

        </form>

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

        <!-- TABLE TO READ DATA FROM DATABASE -->
        <div class="table-wrapper shadow-lg">
        <table class="table table-bordered table-striped table-rounded">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">Tenant_ID</th>
                    <th class="text-center">Full Name</th>
                    <th class="text-center">Phone Number</th>
                    <th class="text-center">Work</th>
                    <th class="text-center">Downpayment</th>
                    <th class="text-center">Advance</th>
                    <th class="text-center">Electricity</th>
                    <th class="text-center">Water</th>
                    <th class="text-center">Unit No.</th>
                    <th class="text-center">Move in Date</th>
                    <th class="text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch tenant data with filters and sorting
                $sql = "SELECT t.*, a.appointment_status 
                       FROM tenant t 
                       LEFT JOIN appointments a ON t.tenant_id = a.tenant_id 
                       WHERE (t.fullname LIKE '%$searchKey%' 
                       OR t.phone_number LIKE '%$searchKey%' 
                       OR t.work LIKE '%$searchKey%' 
                       OR t.units LIKE '%$searchKey%')";
                $sql .= " ORDER BY t.units $filterUnitOrder";
                $result = $conn->query($sql);

                if (!$result) {
                    die("Error executing query: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $unit_number = str_replace('Unit ', '', $row['units']);
                        $formatted_move_in_date = $row['move_in_date'] ? date('m/d/Y', strtotime($row['move_in_date'])) : 'N/A';

                        // Determine link and status color
                        if ($row['appointment_status'] == 'pending' || $row['appointment_status'] == 'confirmed') {
                            $link = "appointment-overview.php?tenant_id={$row['tenant_id']}";
                            $status_color = $row['appointment_status'] == 'confirmed' ? 'text-success' : 'text-warning';
                        } else {
                            $link = "tenant-information.php?unit={$unit_number}";
                            $status_color = '';
                        }

                        echo "<tr>
                            <td>{$row['tenant_id']}</td>
                            <td><a class='text-primary tenant-fullname {$status_color}' href='{$link}'>{$row['fullname']}</a></td>
                            <td>{$row['phone_number']}</td>
                            <td>{$row['work']}</td>
                            <td>{$row['downpayment']}</td>
                            <td>{$row['advance']}</td>
                            <td>{$row['electricity']}</td>  
                            <td>{$row['water']}</td>
                            <td>{$row['units']}</td>
                            <td>{$formatted_move_in_date}</td>
                            <td>
                                 <div class='d-flex gap-2'>
                                    <a href='edit-tenant.php?tenant_id={$row['tenant_id']}' class='btn btn-ocean w-100'>Edit</a>
                                    <button class='btn btn-danger btn-sm' onclick='confirmDelete({$row['tenant_id']})'>Delete</button>
                                </div>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='11' class='text-center'>No tenants found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
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

    // DELETE CONFIRMATION

    function confirmDelete(tenantId) {
    Swal.fire({
        title: 'Remove Tenant?',
        text: "You won't be able to undo this action.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Remove Tenant'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect to the delete URL
            window.location.href = `./req/tenant-history.php?tenant_id=${tenantId}`;
        }
    });
    }
</script>

</body>
</html>
<?php
$conn->close();
?>
