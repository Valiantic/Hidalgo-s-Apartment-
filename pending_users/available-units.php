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

// Get pending requests
$pending_requests = [];
$pending_query = "SELECT unit_number FROM verification_documents WHERE user_id = ? AND status = 'pending'";
if ($stmt = $conn->prepare($pending_query)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $pending_requests[] = $row['unit_number'];
    }
    $stmt->close();
}

// In your existing unit card loop, replace the button part with this:
for ($i = 1; $i <= 5; $i++) {
    $status = isset($units_status["Unit $i"]) ? $units_status["Unit $i"] : 'Available';
    if ($status == 'Available') {
        // Your existing unit card code here...
        
        // Replace the existing button with this code:
        $buttonHtml = in_array($i, $pending_requests) ? 
            "<button class='btn w-100 custom-btn-font pending-request' style='background-color: #FFA500; color: white;' data-bs-toggle='modal' data-bs-target='#pendingModal' data-unit='$i'>Pending Request</button>" :
            "<a href='#' class='btn btn-ocean w-100 custom-btn-font rent-button' data-bs-toggle='modal' data-bs-target='#verificationModal' data-unit='$i'>Rent This Unit</a>";

    
    }
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
    .upload-area:hover {
    background-color: #f8f9fa;
    border-color: #3d8a91 !important;
    }
    .modal-header {
        border-radius: 6px 6px 0 0;
    }
    #submit-btn:disabled {
        opacity: 0.7;
        cursor: not-allowed;
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
            <p class="para"><a href="available-units.php.php">Available Units</a></p>
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
                        $unitDetails = $i >= 3 ? ' <li>
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
                                </li>
                                ';
                        $rentCost = $i >= 3 ? '₱6,500.00' : '₱3,500.00';
                        
                        if ($i >= 3) {
                            $img_src = '../assets/images/icons/rent-house2.png';
                        } else {
                            $img_src = '../assets/images/icons/rent-house1.png';
                        }
                        
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
                                      <p class='card-subtitle fs-4  text-center'>Max Occupancy: $max_occupancy</p>
                                      <p class='card-subtitle text-left'>$unitDetails</p>

                                      <p class='card-text mt-2 fs-3 text-success text-center'>Security Deposit</p>
                                      <p class='card-subtitle fs-4 text-left'>1 Month Deposit: $rentCost</p>
                                      <p class='card-subtitle fs-4 text-left'>1 Month Advance: $rentCost</p>
                                      <p class='card-subtitle fs-4 text-left'>Electricity Deposit: ₱1000.00 </p>
                                      <p class='card-subtitle fs-4 text-left'>Water Deposit: ₱500.00 </p>

                                       <div class='d-flex justify-content-center mt-2 mb-4'>
                                         $buttonHtml
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

</div>

<div class="modal fade" id="verificationModal" tabindex="-1" aria-labelledby="verificationModalLabel" aria-hidden="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-ocean text-white" style="background-color: #4DA1A9;">
                <h5 class="modal-title text-center" id="verificationModalLabel">ID Verification</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="verificationForm" action="req/upload-verification.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="selectedUnit" name="unit_number">
                    
                    <div class="mb-4">
                        <p class="text-center mb-3">Please upload a valid government-issued ID for verification</p>
                        <div class="upload-area border rounded p-4 text-center" id="upload-area" 
                             style="border: 2px dashed #4DA1A9; cursor: pointer;">
                            <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                            <p class="mb-2">Click to upload or drag and drop</p>
                            <p class="text-muted small mb-0">Supported formats: JPG, JPEG, PNG, PDF</p>
                            <input type="file" name="id_image" id="id_image" class="d-none" accept=".jpg,.jpeg,.png,.pdf">
                        </div>
                        <div id="previewContainer" class="mt-3 text-center" style="display: none;">
                            <img id="preview-image" class="mx-auto" style="max-width: 100%; max-height: 200px;" alt="ID Preview">
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn w-100" id="submit-btn" disabled
                                style="background-color: #4DA1A9; color: white;">
                            Submit for Verification
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add this success modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Success!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-center">Your ID has been uploaded successfully! Please wait for admin verification.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<!-- Pending Status Modal -->
<div class="modal fade" id="pendingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title">Pending Request</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Your request is currently pending admin approval.</p>
                <button type="button" class="btn btn-danger mt-3" id="cancelRequestBtn">Cancel Request</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmCancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Cancellation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Are you sure you want to cancel your request? This action cannot be undone.</p>
                <input type="hidden" id="cancelUnitNumber">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">No, Keep Request</button>
                <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Request</button>
            </div>
        </div>
    </div>
</div>

<!-- Add this JavaScript before the closing </body> tag -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('upload-area');
    const fileInput = document.getElementById('id_image');
    const previewImage = document.getElementById('preview-image');
    const previewContainer = document.getElementById('previewContainer');
    const submitBtn = document.getElementById('submit-btn');
    const verificationForm = document.getElementById('verificationForm');
    const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));
    const successModal = new bootstrap.Modal(document.getElementById('successModal'));

    // Update the form action when modal is shown
    document.getElementById('verificationModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const unitNumber = button.getAttribute('data-unit');
        document.getElementById('selectedUnit').value = unitNumber;
        
        // Reset form and preview when modal is opened
        verificationForm.reset();
        previewContainer.style.display = 'none';
        submitBtn.disabled = true;
    });

    uploadArea.addEventListener('click', () => fileInput.click());

    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#3d8a91';
        uploadArea.style.backgroundColor = '#f8f9fa';
    });

    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = '#4DA1A9';
        uploadArea.style.backgroundColor = 'transparent';
    });

    uploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        fileInput.files = e.dataTransfer.files;
        handleFileSelect(e.dataTransfer.files[0]);
    });

    fileInput.addEventListener('change', (e) => {
        handleFileSelect(e.target.files[0]);
    });

    function handleFileSelect(file) {
        if (file) {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
            submitBtn.disabled = false;
        }
    }

    verificationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('req/upload-verification.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            verificationModal.hide();
            if (data.success) {
                successModal.show();
            } else {
                alert(data.message || 'An error occurred during upload.');
            }
        })
        .catch(error => {
            alert('An error occurred during upload.');
            console.error('Error:', error);
        });
    });
});

// Add this to your existing JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // ... (keep your existing JavaScript) ...

    // Update the form submission success handler
    verificationForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const unitNumber = document.getElementById('selectedUnit').value;
        
        fetch('req/upload_verification.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            verificationModal.hide();
            if (data.success) {
                successModal.show();
                // Update the button for this unit
                const rentButton = document.querySelector(`.rent-button[data-unit="${unitNumber}"]`);
                if (rentButton) {
                    const newButton = document.createElement('button');
                    newButton.className = 'btn w-100 custom-btn-font pending-request';
                    newButton.style.backgroundColor = '#FFA500';
                    newButton.style.color = 'white';
                    newButton.setAttribute('data-bs-toggle', 'modal');
                    newButton.setAttribute('data-bs-target', '#pendingModal');
                    newButton.setAttribute('data-unit', unitNumber);
                    newButton.textContent = 'Pending Request';
                    rentButton.replaceWith(newButton);
                }
            } else {
                alert(data.message || 'An error occurred during upload.');
            }
        })
        .catch(error => {
            alert('An error occurred during upload.');
            console.error('Error:', error);
        });
    });

    // Handle pending modal and cancel request
    const pendingModal = new bootstrap.Modal(document.getElementById('pendingModal'));
    const confirmCancelModal = new bootstrap.Modal(document.getElementById('confirmCancelModal'));
    
    document.getElementById('cancelRequestBtn').addEventListener('click', function() {
        const unitNumber = document.querySelector('#pendingModal').getAttribute('data-unit');
        document.getElementById('cancelUnitNumber').value = unitNumber;
        pendingModal.hide();
        confirmCancelModal.show();
    });

    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        const unitNumber = document.getElementById('cancelUnitNumber').value;
        const formData = new FormData();
        formData.append('unit_number', unitNumber);

        fetch('req/cancel-request.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                confirmCancelModal.hide();
                // Update the button back to "Rent This Unit"
                const pendingButton = document.querySelector(`.pending-request[data-unit="${unitNumber}"]`);
                if (pendingButton) {
                    const newLink = document.createElement('a');
                    newLink.href = '#';
                    newLink.className = 'btn btn-ocean w-100 custom-btn-font rent-button';
                    newLink.setAttribute('data-bs-toggle', 'modal');
                    newLink.setAttribute('data-bs-target', '#verificationModal');
                    newLink.setAttribute('data-unit', unitNumber);
                    newLink.textContent = 'Rent This Unit';
                    pendingButton.replaceWith(newLink);
                }
            } else {
                alert(data.message || 'An error occurred while canceling the request.');
            }
        })
        .catch(error => {
            alert('An error occurred while canceling the request.');
            console.error('Error:', error);
        });
    });

    // Update pending modal when it's opened
    document.getElementById('pendingModal').addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const unitNumber = button.getAttribute('data-unit');
        this.setAttribute('data-unit', unitNumber);
    });
});
</script>

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
</script>

</body>
</html>
<?php
$conn->close();
?>
