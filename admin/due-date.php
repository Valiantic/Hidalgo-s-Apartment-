<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

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
<body>

<div class="container-fluid py-4">
    <div class="row mb-4 calendar-header">
        <div class="col">
        <a href="dashboard.php"><button class="btn btn-primary mx-2">Back</button></a>
        </div>
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