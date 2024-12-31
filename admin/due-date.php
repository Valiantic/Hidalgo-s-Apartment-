<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$current_page = basename($_SERVER['PHP_SELF']); 


// Fetch only the latest transaction date for each unit
function fetchTransactionDates() {
    global $pdo;
    
    $query = "
        SELECT 
            t.unit,
            MAX(t.transaction_date) as transaction_date
        FROM transaction_info t
        GROUP BY t.unit
        ORDER BY t.transaction_date DESC
    ";
    
    try {
        $stmt = $pdo->query($query);
        return json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    } catch(PDOException $e) {
        return json_encode(['error' => $e->getMessage()]);
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
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
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
            background-color: #4DA1A9;
            color: white;
        }
        .sidebar .active-menu a {
            color: white;
        }

        .calendar-day {
            min-height: 120px;
            background: #fff;
            border: 1px solid #dee2e6;
            padding: 10px;
        }
        .date-number {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .unit-deadline {
            background: #fff;
            border: 2px solid #dc3545;
            border-radius: 4px;
            padding: 5px;
            margin: 2px;
            display: inline-block;
            font-size: 0.8em;
            color: #dc3545;
        }
        .today {
            background: #e8f5e9;
        }
        @media (max-width: 768px) {
            .calendar-header .col {
                margin-bottom: 10px;
            }
            .calendar-header .col.text-center {
                text-align: center;
            }
            .calendar-header .col.text-end {
                text-align: center;
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

        <li class="items  <?php echo $current_page == 'due-date.php' ? 'active-menu' : ''; ?>">
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
    <div class="container-fluid py-4">
    <div class="row mb-4 calendar-header">
       
        <div class="col text-center">
            <h2>Unit Deadlines</h2>
        </div>
        <div class="col text-center">
            <button id="prevMonth" class="btn btn-outline-primary">
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <button id="todayBtn" class="btn btn-primary mx-2">Today</button>
            <button id="nextMonth" class="btn btn-outline-primary">
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        <div class="col text-end">
            <h3 id="currentMonth" class="mb-0"></h3>
        </div>
    </div>

    <div class="row mb-2">
        <div class="col">
            <div class="row">
                <div class="col text-center fw-bold">Sun</div>
                <div class="col text-center fw-bold">Mon</div>
                <div class="col text-center fw-bold">Tue</div>
                <div class="col text-center fw-bold">Wed</div>
                <div class="col text-center fw-bold">Thu</div>
                <div class="col text-center fw-bold">Fri</div>
                <div class="col text-center fw-bold">Sat</div>
            </div>
        </div>
    </div>

    <div id="calendar"></div>
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
</script>

<script>
const transactionData = <?php echo fetchTransactionDates(); ?>;

class Calendar {
    constructor() {
        this.today = new Date();
        this.currentDate = new Date();
        this.deadlines = this.calculateDeadlines(transactionData);
        
        this.init();
        this.setupListeners();
    }

    calculateDeadlines(transactions) {
        return transactions.map(trans => {
            const transDate = new Date(trans.transaction_date);
            return {
                unit: trans.unit,
                deadline: new Date(transDate.setMonth(transDate.getMonth() + 1))
            };
        });
    }

    init() {
        this.updateMonthDisplay();
        this.renderCalendar();
    }

    setupListeners() {
        document.getElementById('prevMonth').onclick = () => {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.init();
        };

        document.getElementById('nextMonth').onclick = () => {
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.init();
        };

        document.getElementById('todayBtn').onclick = () => {
            this.currentDate = new Date();
            this.init();
        };
    }

    updateMonthDisplay() {
        document.getElementById('currentMonth').textContent = 
            this.currentDate.toLocaleString('default', {
                month: 'long',
                year: 'numeric'
            });
    }

    renderCalendar() {
        const calendar = document.getElementById('calendar');
        calendar.innerHTML = '';

        const firstDay = new Date(
            this.currentDate.getFullYear(),
            this.currentDate.getMonth(),
            1
        ).getDay();

        const lastDate = new Date(
            this.currentDate.getFullYear(),
            this.currentDate.getMonth() + 1,
            0
        ).getDate();

        let html = '<div class="row">';

        // Empty cells before first day
        for (let i = 0; i < firstDay; i++) {
            html += '<div class="col"><div class="calendar-day"></div></div>';
        }

        // Calendar days
        for (let day = 1; day <= lastDate; day++) {
            if ((day + firstDay - 1) % 7 === 0 && day !== 1) {
                html += '</div><div class="row">';
            }

            const date = new Date(
                this.currentDate.getFullYear(),
                this.currentDate.getMonth(),
                day
            );

            const isToday = this.isToday(date) ? 'today' : '';
            const deadlinesForDay = this.getDeadlinesForDay(date);
            
            html += `
                <div class="col">
                    <div class="calendar-day ${isToday}">
                        <div class="date-number">${day}</div>
                        <div>
                            ${deadlinesForDay.map(deadline => 
                                `<div class="unit-deadline" title="Deadline for Unit ${deadline.unit}">
                                    Unit ${deadline.unit}
                                </div>`
                            ).join('')}
                        </div>
                    </div>
                </div>
            `;
        }

        // Empty cells after last date
        const remainingCells = 7 - ((lastDate + firstDay) % 7);
        if (remainingCells < 7) {
            for (let i = 0; i < remainingCells; i++) {
                html += '<div class="col"><div class="calendar-day"></div></div>';
            }
        }

        html += '</div>';
        calendar.innerHTML = html;
    }

    isToday(date) {
        return date.toDateString() === this.today.toDateString();
    }

    getDeadlinesForDay(date) {
        return this.deadlines.filter(deadline => 
            deadline.deadline.toDateString() === date.toDateString()
        );
    }
}

document.addEventListener('DOMContentLoaded', () => new Calendar());
</script>
</body>
</html>