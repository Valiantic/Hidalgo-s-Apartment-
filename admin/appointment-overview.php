<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$tenant_id = $_GET['tenant_id'] ?? null;

if ($tenant_id) {
    $appointment_query = "SELECT * FROM appointments WHERE tenant_id = ? AND appointment_status = 'pending'";
    $stmt = $conn->prepare($appointment_query);
    if ($stmt === false) {
        die('Prepare failed: ' . htmlspecialchars($conn->error));
    }
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $appointment_result = $stmt->get_result();

    if ($appointment_result->num_rows > 0) {
        $appointment_info = $appointment_result->fetch_assoc();

        // Fetch tenant details
        $tenant_query = "SELECT fullname, work, phone_number FROM tenant WHERE tenant_id = ?";
        $tenant_stmt = $conn->prepare($tenant_query);
        if ($tenant_stmt === false) {
            die('Prepare failed: ' . htmlspecialchars($conn->error));
        }
        $tenant_stmt->bind_param("i", $tenant_id);
        $tenant_stmt->execute();
        $tenant_result = $tenant_stmt->get_result();

        if ($tenant_result->num_rows > 0) {
            $tenant_info = $tenant_result->fetch_assoc();
        } else {
            echo "Tenant details not found.";
            exit;
        }
    } else {
        echo "No pending appointments for this tenant.";
        exit;
    }
} else {
    echo "Invalid tenant ID.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment Overview - Tenant <?php echo htmlspecialchars($tenant_id); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Appointment Overview - Tenant <?php echo htmlspecialchars($tenant_id); ?></h1>
        <table class="table table-bordered">
            <tr>
                <th>Tenant Name</th>
                <td><?php echo htmlspecialchars($tenant_info['fullname']); ?></td>
            </tr>
            <tr>
                <th>Work</th>
                <td><?php echo htmlspecialchars($tenant_info['work']); ?></td>
            </tr>
            <tr>
                <th>Phone Number</th>
                <td><?php echo htmlspecialchars($tenant_info['phone_number']); ?></td>
            </tr>
            <tr>
                <th>Unit</th>
                <td><?php echo htmlspecialchars($appointment_info['units']); ?></td>
            </tr>
            <tr>
                <th>Appointment Date</th>
                <td><?php echo htmlspecialchars($appointment_info['appointment_date']); ?></td>
            </tr>
            <tr>
                <th>Valid ID Path</th>
                <td><img src="../tenant/req/uploads/<?php echo htmlspecialchars($appointment_info['valid_id_path']); ?>" alt="Valid ID" style="max-width: 100%; height: auto;"></td>
            </tr>
            <tr>
                <th>Appointment Status</th>
                <td><?php echo htmlspecialchars($appointment_info['appointment_status']); ?></td>
            </tr>
            <tr>
                <th>Date Created</th>
                <td><?php echo htmlspecialchars($appointment_info['date_created']); ?></td>
            </tr>
        </table>
        <a href="units.php" class="btn btn-primary">Back to Units</a>
    </div>
</body>
</html>
<?php
$conn->close();
?>
