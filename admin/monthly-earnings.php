<?php

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']); 


// BAR GRAPH DATA FETCH 
function getMonthlyEarnings($conn) {
    $monthlyData = array();
    
    // Get data for the last 12 months
    for ($i = 0; $i < 12; $i++) {
        $month = date('Y-m', strtotime("-$i months"));
        $monthStart = $month . '-01';
        $monthEnd = date('Y-m-t', strtotime($monthStart));
        
        // Get downpayment and advance for the month
        $query1 = "SELECT COALESCE(SUM(downpayment), 0) as total_downpayment, 
                          COALESCE(SUM(advance), 0) as total_advance
                   FROM tenant 
                   WHERE DATE(move_in_date) BETWEEN '$monthStart' AND '$monthEnd'";
        
        $result1 = $conn->query($query1);
        $row1 = $result1->fetch_assoc();
        
        // Get monthly rent payments
        $query2 = "SELECT unit, COUNT(*) as unit_count 
                   FROM transaction_info 
                   WHERE monthly_rent_status = 'Paid' 
                   AND DATE(transaction_date) BETWEEN '$monthStart' AND '$monthEnd'
                   GROUP BY unit";
        
        $result2 = $conn->query($query2);
        
        $monthlyRent = 0;
        while ($row2 = $result2->fetch_assoc()) {
            $unit = (int)$row2['unit'];
            if ($unit >= 1 && $unit <= 2) {
                $monthlyRent += (3500 * $row2['unit_count']);
            } else if ($unit >= 3 && $unit <= 5) {
                $monthlyRent += (6500 * $row2['unit_count']);
            }
        }
        
        // Calculate total earnings for the month
        $totalEarnings = $row1['total_downpayment'] + $row1['total_advance'] + $monthlyRent;
        
        $monthlyData[] = array(
            'month' => date('M Y', strtotime($monthStart)),
            'earnings' => $totalEarnings
        );
    }
    
    return array_reverse($monthlyData);
}


// TRANSACTION INFO TABLE DATA FETCH
$earningsData = getMonthlyEarnings($conn);
$months = array_column($earningsData, 'month');
$earnings = array_column($earningsData, 'earnings');

$query = "SELECT t.transaction_id, 
          tn.fullname, 
          t.unit, 
          t.monthly_rent_status,
          t.electricity_status,
          t.water_status,
          t.transaction_date 
          FROM transaction_info t
          JOIN tenant tn ON t.tenant_id = tn.tenant_id
          ORDER BY t.transaction_date DESC";

$result = $conn->query($query);
$transactions = [];
while ($row = $result->fetch_assoc()) {
    // Calculate monthly rent based on unit
    $unit = (int)$row['unit'];
    $monthlyRent = ($unit >= 1 && $unit <= 2) ? 3500 : 
                   (($unit >= 3 && $unit <= 5) ? 6500 : 0);
    
    $row['monthly_rent'] = $row['monthly_rent_status'] === 'Paid' ? $monthlyRent : 'Not Paid';
    $transactions[] = $row;
}

// Clear Transaction History 
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'clearTransactions') {
    $conn = new mysqli('localhost', 'username', 'password', 'database_name');
    
    if ($conn->connect_error) {
        echo json_encode(['success' => false, 'message' => 'Connection failed']);
        exit;
    }
    
    $query = "DELETE FROM transaction_info";
    if ($conn->query($query) === TRUE) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error deleting records']);
    }
    
    $conn->close();
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
     <!-- GOOGLE FONTS POPPINS  -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Hidalgo's Apartment</title>
    <link rel="shortcut icon" href="../assets/images/logov5.png">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/datatables/1.10.21/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    
    <!-- SweetAlert2 -->
     <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.all.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert2/11.7.0/sweetalert2.min.css" rel="stylesheet">
    
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
            color: #252525;
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
            color: #252525;
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



        /* BAR GRAPH CSS */
        .table-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .chart-container {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px; /* Add gap between the tables */
        }
        .btn-export {
            background: #3498db;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-export:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        /* CLEAR TRANSACTION INFO CSS */
        .btn-danger-soft {
            background: #ff7675;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 5px;
            transition: all 0.3s;
        }
        .btn-danger-soft:hover {
            background: #ff6b6b;
            transform: translateY(-2px);
        }
        .action-buttons {
            gap: 10px;
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

        <li class="items  <?php echo $current_page == 'monthly-earnings.php' ? 'active-menu' : ''; ?>">
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
        <div class="container-fluid">
            <div class="row justify-content-center">
                
          
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10">

              <!-- MONTHLY EARNINGS TABLE  -->
                <div class="card shadow-sm chart-container">
                    <div class="card-header bg-white border-0 py-3">
                        <h2 class="card-title text-center mb-0">Monthly Earnings Overview</h2>
                    </div>
                    <div class="card-body">
                        <canvas id="earningsChart" height="300"></canvas>
                    </div>

                

                </div>

                 <!-- TRANSACTION INFO TABLE  -->
                 <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Transaction History</h3>
                <div class="d-flex action-buttons">
                    <button class="btn btn-danger-soft" onclick="confirmClearTransactions()">
                        Clear History
                    </button>
                    <button class="btn btn-export" onclick="exportToPDF()">
                        Export to PDF
                    </button>
                </div>
            </div>
            
            <table id="transactionTable" class="table table-hover">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Tenant Name</th>
                        <th>Unit</th>
                        <th>Monthly Rent</th>
                        <th>Electricity</th>
                        <th>Water</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $transaction): ?>
                    <tr>
                        <td><?php echo $transaction['transaction_id']; ?></td>
                        <td><?php echo htmlspecialchars($transaction['fullname']); ?></td>
                        <td><?php echo $transaction['unit']; ?></td>
                        <td><?php echo $transaction['monthly_rent'] === 'Not Paid' ? 
                                    '<span class="text-danger">Not Paid</span>' : 
                                    '₱' . number_format($transaction['monthly_rent'], 2); ?></td>
                        <td><?php echo $transaction['electricity_status']; ?></td>
                        <td><?php echo $transaction['water_status']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($transaction['transaction_date'])); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

            </div>

            

            

        </div>
    </div>


            </div>
        </div>
    </div>

</div>
    
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    


    <!-- SIDEBAR JS  -->
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


    <!-- MONTHLY EARNINGS CHART JS -->
    <script>
        const ctx = document.getElementById('earningsChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($months); ?>,
                datasets: [{
                    label: 'Total Earnings (PHP)',
                    data: <?php echo json_encode($earnings); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.8)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1,
                    borderRadius: 5,
                    maxBarThickness: 50
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
                    

    <!-- TRANSACTION INFO TABLE JS -->
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            $('#transactionTable').DataTable({
                order: [[0, 'desc']],
                pageLength: 10,
                language: {
                    search: "Search transactions:"
                }
            });
        });

        // PDF Export Function using jsPDF with AutoTable
        function exportToPDF() {
            // Initialize jsPDF
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'pt', 'a4');

            // Get data from table
            const data = [];
            const table = document.getElementById('transactionTable');
            const rows = Array.from(table.getElementsByTagName('tr'));
            
            // Get headers
            const headers = [];
            const headerCells = rows[0].getElementsByTagName('th');
            for (let cell of headerCells) {
                headers.push(cell.textContent);
            }

            // Get data rows
            const bodyRows = Array.from(table.getElementsByTagName('tbody')[0].getElementsByTagName('tr'));
            for (let row of bodyRows) {
                const cells = row.getElementsByTagName('td');
                const rowData = [];
                for (let cell of cells) {
                    // Get text content, removing any HTML
                    rowData.push(cell.textContent.trim());
                }
                data.push(rowData);
            }

            // Generate PDF with AutoTable
            doc.autoTable({
                head: [headers],
                body: data,
                startY: 20,
                theme: 'grid',
                styles: {
                    fontSize: 10,
                    cellPadding: 5,
                },
                headStyles: {
                    fillColor: [52, 152, 219],
                    textColor: 255,
                    fontSize: 11,
                    fontStyle: 'bold',
                },
                columnStyles: {
                    0: { cellWidth: 'auto' },
                    1: { cellWidth: 'auto' },
                    2: { cellWidth: 'auto' },
                    3: { cellWidth: 'auto' },
                    4: { cellWidth: 'auto' },
                    5: { cellWidth: 'auto' },
                    6: { cellWidth: 'auto' }
                },
                margin: { top: 20 }
            });

            // Save PDF
            doc.save('transaction_history.pdf');
        }

        // Previous chart initialization code remains the same...
    </script>

    <!-- CLEAR HISTORY CONFIRMATION SWEET ALERT JS -->
    <script>

    // Clear Transactions Function
    function confirmClearTransactions() {
        Swal.fire({
            title: 'Clear Transaction History?',
            text: "This will permanently delete all transaction records. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete all!',
            cancelButtonText: 'Cancel',
            reverseButtons: false
        }).then((result) => {
            if (result.isConfirmed) {
                clearTransactions();
            }
        });
    }

    function clearTransactions() {
        // Show loading state
        Swal.fire({
            title: 'Deleting...',
            text: 'Please wait while we clear the transaction history.',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Send delete request
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=clearTransactions'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    title: 'Deleted!',
                    text: 'All transaction records have been cleared.',
                    icon: 'success'
                }).then(() => {
                    // Reload the page to refresh the table
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: data.message || 'Failed to clear transaction history.',
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            Swal.fire({
                title: 'Error!',
                text: 'An unexpected error occurred.',
                icon: 'error'
            });
        });
    }

    // Previous chart initialization code remains the same...
    </script>

</body>
</html>