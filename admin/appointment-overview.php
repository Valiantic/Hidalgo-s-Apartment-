<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header('Location: ../authentication/login.php');
    exit;
}

include '../connections.php';

$tenant_id = $_GET['tenant_id'] ?? null;
$success_message = '';
$error_message = '';
$appointment_info = []; // Initialize the variable to avoid undefined variable warning

// Fetch appointment details
if ($tenant_id) {
    $appointment_query = "SELECT * FROM appointments WHERE tenant_id = ? AND (appointment_status = 'pending' OR appointment_status = 'confirmed')";
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
        echo "No pending or confirmed appointments for this tenant.";
        exit;
    }
} else {
    echo "Invalid tenant ID.";
    exit;
}

// Handle confirmation submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_appointment'])) {
    $update_query = "UPDATE appointments SET appointment_status = 'confirmed' WHERE tenant_id = ? AND appointment_status = 'pending'";
    $update_stmt = $conn->prepare($update_query);
    if ($update_stmt === false) {
        $error_message = 'Error preparing statement: ' . htmlspecialchars($conn->error);
    } else {
        $update_stmt->bind_param("i", $tenant_id);
        if ($update_stmt->execute()) {
            $success_message = 'Appointment has been confirmed successfully!';
            // Update appointment status in the current session
            $appointment_info['appointment_status'] = 'confirmed';
        } else {
            $error_message = 'Error updating appointment: ' . htmlspecialchars($update_stmt->error);
        }
        $update_stmt->close();
    }
}


// Handle complete transaction submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_transaction'])) {
    try {
        // Start transaction
        $conn->begin_transaction();
        
        if (isset($appointment_info['units'])) {
            // Get unit number to determine deposit amount
            preg_match('/\d+/', $appointment_info['units'], $matches);
            $unit_number = intval($matches[0]);
            $deposit_amount = ($unit_number == 1 || $unit_number == 2) ? 3500 : 6500;
            
            // Update tenant with deposits only (removed move_in_date)
            $update_tenant = "UPDATE tenant SET 
                downpayment = ?,
                advance = ?,
                electricity = ?,
                water = ?
                WHERE tenant_id = ?";
            
            $update_stmt = $conn->prepare($update_tenant);
            if ($update_stmt === false) {
                throw new Exception('Error preparing statement: ' . htmlspecialchars($conn->error));
            }
            
            $electricity_deposit = 1000;
            $water_deposit = 500;
            
            $update_stmt->bind_param(
                "ddiii", 
                $deposit_amount, 
                $deposit_amount, 
                $electricity_deposit,
                $water_deposit,
                $tenant_id
            );
            $update_stmt->execute();
            
            // Update transaction info table using REPLACE INTO with numeric unit number
            $update_transaction = "REPLACE INTO transaction_info 
                (tenant_id, unit, monthly_rent_status, electricity_status, water_status) 
                VALUES (?, ?, 'paid', 'paid', 'paid')";
            
            $transaction_stmt = $conn->prepare($update_transaction);
            if ($transaction_stmt === false) {
                throw new Exception('Error preparing transaction statement: ' . htmlspecialchars($conn->error));
            }
            
            $transaction_stmt->bind_param("ii", $tenant_id, $unit_number); // Changed 's' to 'i' for unit
            $transaction_stmt->execute();
            
            // Delete the appointment record instead of updating status
            $delete_appointment = "DELETE FROM appointments WHERE tenant_id = ?";
            $appointment_stmt = $conn->prepare($delete_appointment);
            if ($appointment_stmt === false) {
                throw new Exception('Error preparing delete statement: ' . htmlspecialchars($conn->error));
            }
            $appointment_stmt->bind_param("i", $tenant_id);
            $appointment_stmt->execute();
            
            // Close statements
            $update_stmt->close();
            $transaction_stmt->close();
            $appointment_stmt->close();
            
            // Commit transaction
            $conn->commit();
            
            $success_message = 'Transaction completed successfully!';
            header("Location: tenants.php"); // Redirect to tenants page since appointment is deleted
            exit;
        } else {
            throw new Exception('Unit information is missing.');
        }
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        $error_message = 'Error completing transaction: ' . htmlspecialchars($e->getMessage());
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <!-- Remove the datepicker script -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border-radius: 1rem;
        }
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid rgba(0,0,0,.125);
            border-radius: 1rem 1rem 0 0 !important;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 30%;
        }
        .btn-back {
            border-radius: 0.5rem;
            padding: 0.75rem 1.5rem;
            font-weight: 500;
        }
        .profile-section {
            background-color: #fff;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        .tenant-name {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }
        .tenant-info {
            color: #6c757d;
        }
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 2rem;
            font-weight: 500;
        }
        .img-container {
            max-width: 400px;
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .btn-confirm {
            padding: 1rem 2rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .alert {
            border-radius: 0.5rem;
            border: none;
        }
        .modal-content {
            border-radius: 1rem;
            border: none;
        }
        .modal-header {
            border-bottom: 1px solid rgba(0,0,0,.1);
        }
        .modal-footer {
            border-top: 1px solid rgba(0,0,0,.1);
        }
        .form-control {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
        }
        .deposit-info {
            background-color: #f8f9fa;
            border-radius: 0.5rem;
            padding: 1rem;
            margin-top: 1rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .profile-section {
                padding: 1rem;
            }
            
            .tenant-name {
                font-size: 1.2rem;
            }
            
            .tenant-info {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .tenant-info span {
                display: block;
                margin: 0;
            }
            
            .status-badge {
                margin-top: 1rem;
                display: inline-block;
            }
            
            .table th {
                width: 40%;
            }
            
            .img-container {
                max-width: 100%;
            }
            
            .btn-back, .btn-confirm {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .mt-4.d-flex {
                flex-direction: column;
            }
            
            .gap-3 {
                gap: 0.5rem !important;
            }
            
            .modal-dialog {
                margin: 0.5rem;
            }
            
            .deposit-info {
                padding: 0.75rem;
            }
        }

        @media (max-width: 576px) {
            .breadcrumb {
                font-size: 0.9rem;
            }
            
            .card-header h2 {
                font-size: 1.1rem;
            }
            
            .table {
                font-size: 0.9rem;
            }
            
            .modal-title {
                font-size: 1.1rem;
            }
            
            .deposit-info {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="container py-5">
        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                <?php echo htmlspecialchars($success_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                <?php echo htmlspecialchars($error_message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="tenants.php">Tenants</a></li>
                <li class="breadcrumb-item active">Appointment Overview</li>
            </ol>
        </nav>

        <div class="profile-section">
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <h1 class="tenant-name h3">
                        <i class="bi bi-person-circle me-2"></i>
                        <?php echo htmlspecialchars($tenant_info['fullname']); ?>
                    </h1>
                    <div class="tenant-info d-flex flex-wrap">
                        <span class="me-md-3">
                            <i class="bi bi-briefcase me-2"></i>
                            <?php echo htmlspecialchars($tenant_info['work']); ?>
                        </span>
                        <span>
                            <i class="bi bi-telephone me-2"></i>
                            <?php echo htmlspecialchars($tenant_info['phone_number']); ?>
                        </span>
                    </div>
                </div>
                <div class="col-12 col-md-4 text-md-end">
                    <span class="status-badge <?php echo ($appointment_info['appointment_status'] === 'confirmed') ? 'bg-success text-white' : 'bg-warning text-dark'; ?>">
                        <?php echo htmlspecialchars($appointment_info['appointment_status']); ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h2 class="h5 mb-0">Appointment Details</h2>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <tbody>
                            <tr>
                                <th>Unit</th>
                                <td>
                                    <i class="bi bi-building me-2"></i>
                                    <?php echo htmlspecialchars($appointment_info['units']); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Appointment Date</th>
                                <td>
                                    <i class="bi bi-calendar-event me-2"></i>
                                    <?php echo date('m/d/y', strtotime($appointment_info['appointment_date'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Date Created</th>
                                <td>
                                    <i class="bi bi-clock-history me-2"></i>
                                    <?php echo date('m/d/y', strtotime($appointment_info['date_created'])); ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Valid ID</th>
                                <td>
                                    <div class="img-container">
                                        <img src="../tenant/req/<?php echo htmlspecialchars($appointment_info['valid_id_path']); ?>" 
                                             alt="Valid ID" 
                                             class="img-fluid rounded">
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-4 d-flex flex-column flex-md-row gap-3">
            <a href="tenants.php" class="btn btn-secondary btn-back">
                <i class="bi bi-arrow-left me-2"></i>Back to Tenants
            </a>
            <?php if ($appointment_info['appointment_status'] === 'pending'): ?>
                <form method="POST" style="display: inline-block; width: 100%;" id="confirmForm">
                    <button type="submit" name="confirm_appointment" class="btn btn-success btn-confirm w-100">
                        <i class="bi bi-check-lg me-2"></i>Confirm Appointment
                    </button>
                </form>
            <?php elseif ($appointment_info['appointment_status'] === 'confirmed'): ?>
                <button type="button" class="btn btn-primary btn-confirm w-100" data-bs-toggle="modal" data-bs-target="#completeTransactionModal">
                    <i class="bi bi-check-circle me-2"></i>Complete Transaction
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Complete Transaction Modal -->
    <div class="modal fade" id="completeTransactionModal" tabindex="-1" aria-labelledby="completeTransactionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="completeTransactionModalLabel">Complete Transaction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="deposit-info">
                            <h6 class="mb-3">Deposit Information</h6>
                            <?php
                            // Ensure $appointment_info['units'] is set
                            if (isset($appointment_info['units'])) {
                                // Get unit number from appointment info
                                preg_match('/\d+/', $appointment_info['units'], $matches);
                                $unit_number = intval($matches[0]);
                                
                                // Validate unit number
                                if ($unit_number < 1 || $unit_number > 5) {
                                    echo '<div class="alert alert-danger">Invalid unit number detected.</div>';
                                    $deposit_amount = 0;
                                } else {
                                    // Calculate deposit based on unit number
                                    $deposit_amount = ($unit_number == 1 || $unit_number == 2) ? 3500 : 6500;
                                }
                            } else {
                                echo '<div class="alert alert-danger">Unit information is missing.</div>';
                                $deposit_amount = 0;
                            }
                            ?>
                            
                            <p>Unit: <?php echo htmlspecialchars($unit_number); ?></p>
                            <p class="mb-2">
                                <strong>Downpayment:</strong> 
                                ₱<?php echo number_format($deposit_amount, 2); ?>
                            </p>
                            <p class="mb-2">
                                <strong>Advance:</strong> 
                                ₱<?php echo number_format($deposit_amount, 2); ?>
                            </p>
                            <p class="mb-2"><strong>Electricity Deposit:</strong> ₱1,000.00</p>
                            <p class="mb-2"><strong>Water Deposit:</strong> ₱500.00</p>
                            <hr>
                            <p class="mb-0 fw-bold">
                                <strong>Total Amount:</strong> 
                                ₱<?php echo number_format(($deposit_amount * 2) + 1000 + 500, 2); ?>
                            </p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" name="complete_transaction" class="btn btn-primary">Complete Transaction</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
