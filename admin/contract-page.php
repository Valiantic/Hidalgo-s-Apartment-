<?php
session_start();

if (!isset($_SESSION['tenant_id'])) {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$unit_number = isset($_GET['unit']) ? (int)$_GET['unit'] : null;
if (!$unit_number) {
    die("Unit number not specified.");
}

$unit_name = "Unit $unit_number";

$sql = "SELECT fullname, phone_number, move_in_date FROM tenant WHERE units = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $unit_name);
$stmt->execute();
$tenant = $stmt->get_result()->fetch_assoc();

if (!$tenant) {
    die("No tenant found for the specified unit.");
}

if ($unit_number === 1 || $unit_number === 2) {
    $rent_amount = '3,500';
    $description = 'Single-floor units rent is considered overdue if it is unpaid 3 days past the due date.';
} elseif (in_array($unit_number, [3, 4, 5])) {
    $rent_amount = '6,500';
    $description = 'Two-floor units, rent is considered overdue if it is unpaid 4 days past the due date.';
} else {
    die("Invalid unit number.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Hidalgo's Apartment</title>
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <script>
        function calculateEndOfMonth(moveInDate) {
            const moveIn = new Date(moveInDate);
            const endOfMonth = new Date(moveIn.getFullYear(), moveIn.getMonth() + 1, moveIn.getDate()); // Same day next month
            return endOfMonth.toISOString().split('T')[0]; // Format as YYYY-MM-DD
        }

        function updateEndDate() {
            const moveInDate = document.getElementById("move-in-date").textContent.trim();
            const endDate = calculateEndOfMonth(moveInDate);
            document.getElementById("end-of-month").textContent = endDate;
        }

        window.onload = updateEndDate;
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .contract {
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 8px;
            max-width: 800px;
            margin: 0 auto;
        }

        .btn-print {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn-print:hover {
            background-color: #0056b3;
        }
        .text-center{
            text-align: center;
        }

        p, li {
            font-size: 0.9rem;
        }

        @media print {
            .btn-print {
                display: none;
            }
            body {
                margin: 0;
                padding: 0;
                width: 100%;
            }
            .contract {
                border: none;
                padding: 0;
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="contract">
        <h1 class="text-center">Lease Agreement</h1>
        <p>This Lease Agreement ("Agreement") is entered into on this <span id="move-in-date"><?php echo $tenant['move_in_date']; ?></span> by and between the landlord and tenant:</p>
        <p><strong>Tenant's Fullname:</strong> <?php echo htmlspecialchars($tenant['fullname']); ?></p>
        <p><strong>Phone number:</strong> <?php echo htmlspecialchars($tenant['phone_number']); ?></p>

        <h2 class="fs-6 fw-bold">1. Property Description</h2>
        <p>The Landlord hereby leases to the Tenant the property located at:</p>
        <p><strong>Address:</strong> 127 Alunos Subdivision, Brgy Santo Domingo, Binan City, Laguna</p>
        <p><strong>Type of Property:</strong> Apartment</p>

        <h2  class="fs-6 fw-bold">2. Lease Term</h2>
        <p>The term of this lease shall begin on <span id="move-in-date"><?php echo $tenant['move_in_date']; ?></span> until <span id="end-of-month"></span>.</p>

        <h2  class="fs-6 fw-bold">3. Rent</h2>
        <p>The Tenant agrees to pay rent in the amount of PHP <?php echo $rent_amount; ?> per month.</p>
        <p><?php echo $description; ?></p>

        <h2  class="fs-6 fw-bold">4. Security Deposit</h2>
        <p>The Tenant agrees to pay a security deposit of an advanced payment for one (1) month upon agreement.</p>

        <h2  class="fs-6 fw-bold">5. Maintenance and Repairs</h2>
        <ul>
            <li>The Tenant agrees to maintain the property in good condition and promptly report any needed repairs to the Landlord.</li>
            <li>The Landlord is responsible for major repairs not caused by the Tenant’s negligence.</li>
        </ul>

        <h2  class="fs-6 fw-bold">6. Rules and Restrictions</h2>
        <ul>
            <li><strong>Pets:</strong> Pets are allowed as long as they are kept indoors at all times.</li>
            <li>The Tenant shall not make any alterations to the property without prior written consent from the Landlord.</li>
        </ul>

        <h2 class="fs-6 fw-bold">7. Termination and Renewal</h2>
        <ul>
            <li>The Tenant must provide 3 days’ written notice prior to vacating the property.</li>
        </ul>
    </div>
    <button class="btn-print" onclick="window.print()">Print to PDF</button>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.0/dist/js/bootstrap.bundle.min.js"></script>    
</body>
</html>
