<?php
    session_start();
    include '../connections.php';
    
    // Check if user is logged in and is a tenant
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
        header('Location: login.php');
        exit();
    }


    $tenant_id = $_SESSION['tenant_id'];
    $stmt = $conn->prepare("SELECT * FROM tenant WHERE tenant_id = ?");
    $stmt->bind_param("i", $tenant_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $tenant = $result->fetch_assoc();

    // Check if tenant has a move-in date or a pending appointment
    if ($tenant['move_in_date']) {
        header('Location: home.php');
        exit();
    }

    $pending_query = "SELECT * FROM appointments WHERE tenant_id = ? AND appointment_status = 'pending'";
    $pending_stmt = $conn->prepare($pending_query);
    $pending_stmt->bind_param("i", $tenant_id);
    $pending_stmt->execute();
    $pending_result = $pending_stmt->get_result();
    if ($pending_result->num_rows > 0) {
        header('Location: home.php');
        exit();
    }

    // Fetch all booked dates
    $booked_query = "SELECT DATE_FORMAT(appointment_date, '%Y-%m-%d') as booked_date 
                     FROM appointments 
                     WHERE appointment_status != 'cancelled'";
    $booked_result = $conn->query($booked_query);
    $booked_dates = array();
    while($row = $booked_result->fetch_assoc()) {
        $booked_dates[] = $row['booked_date'];
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hidalgo's Apartment</title>
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>
<body>


    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">Book an Appointment</h3>
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <h5>Tenant Information</h5>
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($tenant['fullname']); ?></p>
                            <p><strong>Unit:</strong> <?php echo htmlspecialchars($tenant['units']); ?></p>
                            <p><strong>Phone number:</strong> <?php echo htmlspecialchars($tenant['phone_number']); ?></p>
                        </div>

                        <form action="./req/process-appointment.php" method="POST" enctype="multipart/form-data" id="appointmentForm">
                            <input type="hidden" name="tenant_id" value="<?php echo $tenant_id; ?>">
                            <input type="hidden" name="units" value="<?php echo htmlspecialchars($tenant['units']); ?>">
                            
                            <div class="mb-4">
                                <label for="appointment_date" class="form-label">Select Appointment Date</label>
                                <div id="appointment-status" class="form-text mt-2"></div>
                                <input type="text" class="form-control" id="appointment_date" name="appointment_date" required>
                            </div>
                            
                            <div class="mb-4">
                                <label for="valid_id" class="form-label">Upload Valid ID</label>
                                <input type="file" class="form-control" id="valid_id" name="valid_id" accept="image/*" required>
                                <div class="form-text">Please upload a clear image of your valid ID</div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>Submit Appointment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        const bookedDates = <?php echo json_encode($booked_dates); ?>;
        const statusDiv = document.getElementById('appointment-status');
        const submitBtn = document.getElementById('submitBtn');

        flatpickr("#appointment_date", {
            enableTime: true,
            dateFormat: "Y-m-d H:i",
            minDate: "today",
            maxDate: new Date().fp_incr(30), // Allow booking up to 30 days ahead
            minTime: "09:00",
            maxTime: "17:00",
            disable: [
                function(date) {
                    // Disable weekends
                    if (date.getDay() === 0 || date.getDay() === 6) {
                        return true;
                    }
                    // Disable dates with existing appointments
                    const formattedDate = date.toISOString().split('T')[0];
                    return bookedDates.includes(formattedDate);
                }
            ],
            onChange: function(selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    const selected = selectedDates[0];
                    const dateStr = selected.toISOString().split('T')[0];
                    
                    if (bookedDates.includes(dateStr)) {
                        statusDiv.innerHTML = '<span class="text-danger">This date is already booked. Please select another.</span>';
                        this.clear();
                        submitBtn.disabled = true;
                    } else {
                        statusDiv.innerHTML = '<span class="text-success">Date available!</span>';
                        submitBtn.disabled = false;
                    }
                } else {
                    submitBtn.disabled = true;
                }
            },
            onOpen: function() {
                statusDiv.innerHTML = '';
            },
            onClose: function(selectedDates, dateStr) {
                if (selectedDates.length > 0) {
                    const selected = selectedDates[0];
                    const endDate = new Date(selected.getTime() + 8 * 60 * 60 * 1000); // Add 8 hours
                    if (endDate.getDate() !== selected.getDate()) {
                        statusDiv.innerHTML = '<span class="text-danger">Appointment duration cannot exceed 8 hours. Please select another time.</span>';
                        this.clear();
                        submitBtn.disabled = true;
                    }
                }
            }
        });

        // Add form validation
        document.getElementById('appointmentForm').addEventListener('submit', function(e) {
            const appointmentDate = document.getElementById('appointment_date').value;
            if (!appointmentDate) {
                e.preventDefault();
                statusDiv.innerHTML = '<span class="text-danger">Please select an appointment date.</span>';
            }
        });
    </script>
</body>
</html>