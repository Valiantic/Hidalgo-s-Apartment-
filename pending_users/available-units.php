<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); 
    exit;
}

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']); 

$query = "SELECT units, COUNT(*) AS count FROM tenant GROUP BY units";
$result = $conn->query($query);

$units_status = [];
while ($row = $result->fetch_assoc()) {
    $units_status[$row['units']] = $row['count'] > 0 ? 'Occupied' : 'Available';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservation - Hidalgo's Apartment</title>
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



    .active.toggler {
        left: 190px;
    }

    @media (max-width: 768px) {
        .active.toggler {
            left: 105px;
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


        <li class="items <?php echo $current_page == 'available-units.php' ? 'active-menu' : ''; ?>">
            <a href="available-units.php.php"><i class="fa-solid fa-home"></i></i></a>
            <p class="para"><a href="available-units.php.php">Units</a></p>
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
            <?php
            for ($i = 1; $i <= 5; $i++) {
                $status = isset($units_status["Unit $i"]) ? $units_status["Unit $i"] : 'Available';
                if ($status == 'Available') {
                    $max_occupancy = $i <= 2 ? '2-4 persons' : '3-5 persons';
                    $building_type = $i <= 2 ? 'Single-Storey Building' : '2-Storey Building';
                    $unitDetails = $i >= 3 ? '
                        <li>
                            <ul class="card-subtitle fs-4">2 Bedrooms (located on the upper floor)</ul>
                            <ul class="card-subtitle fs-4">1 Living Room</ul>
                            <ul class="card-subtitle fs-4">1 Bathroom</ul>
                            <ul class="card-subtitle fs-4">1 Kitchen/Dining Area</ul>
                        </li>' : '
                        <li>
                            <ul class="card-subtitle fs-4">1 Bedroom</ul>
                            <ul class="card-subtitle fs-4">1 Living Room</ul>
                            <ul class="card-subtitle fs-4">1 Bathroom</ul>
                            <ul class="card-subtitle fs-4">1 Kitchen/ Dining Area</ul>
                        </li>';
                    $rentCost = $i >= 3 ? '₱6,500.00' : '₱3,500.00';
                    $img_src = $i >= 3 ? '../assets/images/icons/rent-house2.png' : '../assets/images/icons/rent-house1.png';

                    echo "
                    <div class='col-sm-12 col-md-6 col-lg-4 mb-3'>
                        <div class='card shadow-lg'>
                            <img class='card-img-top img-fluid height-img' src='$img_src' alt='Card image cap'>
                            <div class='card-body'>
                                <div class='d-flex justify-content-center'>
                                    <div class='d-block mb-2'>
                                        <h1 class='card-title text-center'>Unit $i</h1>
                                        <p class='card-text text-warning text-center'>$status</p>
                                    </div>
                                </div>
                                <p class='card-text fs-3 text-primary text-center'>Unit Details</p>
                                <p class='card-subtitle fs-4 text-center'>$building_type</p>
                                <p class='card-subtitle fs-4 text-center'>Max Occupancy: $max_occupancy</p>
                                <p class='card-subtitle text-left'>$unitDetails</p>
                                <p class='card-text mt-2 fs-3 text-success text-center'>Security Deposit</p>
                                <p class='card-subtitle fs-4 text-left'>1 Month Deposit: $rentCost</p>
                                <p class='card-subtitle fs-4 text-left'>1 Month Advance: $rentCost</p>
                                <p class='card-subtitle fs-4 text-left'>Electricity Deposit: ₱1000.00</p>
                                <p class='card-subtitle fs-4 text-left'>Water Deposit: ₱500.00</p>
                                <div class='d-flex justify-content-center mt-2 mb-4'>
                                    <button 
                                        class='btn btn-ocean w-100 custom-btn-font' 
                                        data-bs-toggle='modal' 
                                        data-bs-target='#uploadModal' 
                                        data-unit='$i'>Rent This Unit</button>
                                </div>
                            </div>
                        </div>
                    </div>";
                }
            }
            ?>
        </div>
    </div>
</div>



<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadModalLabel">Upload Valid ID</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="./req/upload-id.php" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="validId" class="form-label">Valid ID</label>
                        <input type="file" class="form-control" id="validId" name="validId" required>
                    </div>
                    <input type="hidden" id="unitInput" name="unit">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>


</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    


<script>
document.addEventListener('DOMContentLoaded', function () {
    const uploadModal = document.getElementById('uploadModal');
    uploadModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const unit = button.getAttribute('data-unit'); // Extract info from data-* attributes
        const unitInput = document.getElementById('unitInput');
        unitInput.value = unit; // Update hidden input value
    });
});
</script>


<!-- UNIT RENT STATUS -->
<script>
    $(document).on('click', '.rent-unit', function () {
        const unitId = $(this).data('unit');
        $.ajax({
            url: 'req/update-unit-status.php',
            type: 'POST',
            data: { unit: unitId },
            success: function (response) {
                if (response.success) {
                    $(`#status-${unitId}`).text('Rented');
                    alert('Unit ' + unitId + ' has been rented successfully.');
                } else {
                    alert('Failed to rent the unit. Please try again.');
                }
            },
            error: function () {
                alert('An error occurred. Please try again later.');
            }
        });
    });
</script>


<!-- HANDLES VALID ID UPLOAD -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const buttons = document.querySelectorAll('.rent-button');
    const modal = new bootstrap.Modal(document.getElementById('uploadModal'));
    const form = document.getElementById('uploadForm');

    buttons.forEach(button => {
        button.addEventListener('click', (e) => {
            const unit = button.dataset.unit;
            document.getElementById('selectedUnit').value = unit;
            modal.show();
        });
    });

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const formData = new FormData(form);

        fetch('req/upload-id.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert(data.message);
                    modal.hide();
                    location.reload(); // Refresh to update card state
                } else {
                    alert(data.message);
                }
            })
            .catch(error => console.error('Error:', error));
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
</script>

</body>
</html>
<?php
$conn->close();
?>
