<?php
session_start();

// If coming from unit selection, set the access flag
if (isset($_GET['unit'])) {
    $_SESSION['rent_unit_access'] = true;
}

// Check if the user has access
if (!isset($_SESSION['rent_unit_access'])) {
    header("Location: login.php"); 
    exit();
}

// Fetch the selected unit from the URL
$selected_unit = isset($_GET['unit']) ? (int)$_GET['unit'] : '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../assets/images/logov5.png">
    <title>Hidalgo's Apartment</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons CSS -->
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

     <!-- GOOGLE FONTS POPPINS -->
     <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Karla:ital,wght@0,200..800;1,200..800&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&family=Roboto+Mono:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Poppins', 'sans-serif';
            background-image: url('../assets/images/skyshot.jpg');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center;
            background-attachment: fixed;
        }

        .login-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-form {
            flex: 1;
            max-width: 450px;
            background: #ffffff;
            padding: 30px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            border-radius: 14px;
            margin-left: 40px;
        }

        .form-floating {
            margin-bottom: 25px; /* Adds gaps between input fields */
        }

        .form-floating i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #6c757d;
        }

        .form-floating input {
            padding-left: 2.5rem; /* Offset to make space for icons */
        }

        h3 {
            color: rgb(102, 153, 255);
        }

        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            font-size: 1.2rem;
            color: #6c757d;
        }

        .logo {
            height: 100px;
            margin-bottom: 10px;
        }

        .input-box {
            position: relative;
            margin: 30px 0;
        }

        .input-box input {
            width: 100%;
            padding: 13px 50px 13px 20px;
            background: #eee;
            border-radius: 8px;
            border: none;
            outline: none;
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .input-box input::placeholder {
            color: #888;
            font-weight: 400;
        }

        .input-box i {
            position: absolute;   
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #888;
        }

        .forgot-link {
            margin: -15px 0 15px;
            text-align: center;
        }

        .forgot-link a {
            font-size: 14.5px;
            color: #333;
            text-decoration: none;
        }

        .btn {
            width: 100%;
            height: 48px;
            background-color: rgb(102, 153, 255) !important;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, .1);
            border: none;
            cursor: pointer;
            font-size: 16px;
            color: #fff;
            font-weight: 600;
        }

        ./* Adjusting spacing between the checkbox and the text */
        .rules-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            margin-left: 5px;
            gap: 5px; /* Reduced gap between checkbox and label */
        }

        .rules-container label {
            font-size: 14px;
            margin-bottom: 15px;

        }

        .rules-container input {
            margin-right: 10px;
        }

        /* Hide the spinner for Chrome, Safari, and Edge */
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
            }

            .login-form {
                width: 100%;
                margin-left: 0;
            }
        }

        @media screen and (max-width: 400px) {
            .form-box {
                padding: 20px;
            }

            .toggle-panel h1 {
                font-size: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container login-container">
        <!-- Left Side Image -->
        <div class="login-image">
            
        </div>

        <!-- Right Side Login Form -->
        <div class="login-form">
            <!-- Header -->
            <div class="text-center mb-4">
                <a href="../index.php"><img class="logo" src="../assets/images/logov5.png" alt="Logo"></a>
                <h3 class="fw-bold">Let's Create your Account First!</h3>
                <?php if ($selected_unit): ?>
                    <p class="text-muted">You are about to Rent Unit <?php echo htmlspecialchars($selected_unit); ?></p>
                <?php endif; ?>
            </div>

            <!-- Login Form -->
            <form action="./req/signup.php" method="POST">
                <div class="input-box">
                    <input type="text" name="fullname" placeholder="Full Name" required>
                    <i class='bx bxs-user'></i> 
                </div>
                <div class="input-box">
                    <input type="number" name="phone_number" placeholder="Phone Number" required>
                    <i class='bx bxs-phone' ></i> 
                </div> 
                <div class="input-box">
                    <input type="text" name="work" placeholder="Work" required>
                    <i class='bx bx-current-location' ></i>
                </div>        
                <div class="input-box">
                    <input type="number" name="residents" placeholder="Number of Residents" required>
                    <i class='bx bx-child' ></i> 
                </div> 
                <div class="input-box">
                    <input type="email" name="email" placeholder="Email" required>
                    <i class='bx bxs-envelope' ></i>
                </div>   
                <div class="input-box">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="toggle-password bi bi-eye-slash"></i>
                </div>    
                <input type="hidden" name="unit" value="<?php echo htmlspecialchars($selected_unit); ?>">

                <div class="d-flex justify-content-center mb-1 text-align-center">
                    <!-- ERROR AND SUCCESS HANDLING -->
                    <?php if (isset($_GET['error'])) { ?>
                        <b style="color: #f00;"><?= htmlspecialchars($_GET['error']) ?></b><br>
                    <?php } ?>
                    <?php if (isset($_GET['success'])) { ?>
                        <b style="color: #0f0;"><?= htmlspecialchars($_GET['success']) ?></b><br>
                    <?php } ?>
                </div>
                
                <div class="rules-container">
                    <input type="checkbox" id="rulesCheck" required>
                    <label for="rulesCheck">I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#rulesModal">Rules and Regulations</a></label>
                </div>

                <button type="submit" class="btn">Sign-up</button>
                
                <div class="d-flex justify-content-center mt-4">
                    <p>Already have an account?<a class="register" href="login.php"> Click here!</a></p>
                </div>
            </form>
        </div>
    </div>

    <!-- Rules and Regulations Modal -->
    <div class="modal fade" id="rulesModal" tabindex="-1" aria-labelledby="rulesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rulesModalLabel">Rules and Regulations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                <section>
            <h2>Advance Payment and Deposit</h2>
            <ul>
                <li>At the time of signing the lease agreement, 
                  <br>all new renters must submit an initial payment of one month's advance rent plus a security deposit equal to one month's rent. 
                  <br>This deposit secures the property and serves as a guarantee against any potential damages or noncompliance with the lease conditions. 
                  <br>The advance rent is for the first month of occupation, while the deposit is held in trust and returned at the conclusion of the lease period, 
                  <br>subject to a satisfactory property inspection and completion of all lease duties.</li>
            </ul>
        </section>

        <section>
            <h2>Pets</h2>
            <ul>
                <li>Pets are absolutely forbidden from being outside under any circumstances in order to protect the safety and comfort of all inhabitants, as well as to keep the property clean and tidy. 
                <br>This limitation applies at all times, regardless of time of day or weather, and is meant to avoid disruptions, potential landscape damage, and undesired contacts with other residents or wildlife. 
                <br>Pet owners are obliged to keep their dogs indoors and oversee them within their living environment. Failure to follow this guideline may result in penalties or other action, as specified in the property's policies.</li>
            </ul>
        </section>

        <section>
            <h2> Occupancy Limits</h2>
            <ul>
                <li>Tenants are obligated to carefully adhere to the maximum number of occupants specified in the unit description and the lease agreement.
                  <br>This restriction is in place to ensure the health, safety, and comfort of all tenants, as well as to protect the apartment's infrastructure and utilities. 
                  <br>The stated occupancy limit is decided by criteria such as the apartment's size, local housing standards, and fire safety regulations; exceeding this limit may result in overcrowding, excessive pressure on facilities, and noncompliance with legal requirements. 
                  <br>Violations of this policy may result in fines, lease termination, or other legal penalties.
                  <br>Tenants are advised to clarify any issues about occupancy limitations before to moving in and must obtain prior consent for any modifications to the agreed-upon number of people.
                </li>
            </ul>
        </section>
        
        <section>
            <h2>Visitor Notification</h2>
            <ul>
                <li>If visitors are scheduled to remain in the unit for any period of time, the owner must be notified in advance. 
                  <br>This notification obligation guarantees that property management is informed of all persons dwelling or utilizing the premises, even if only briefly. 
                  <br>Advanced notice allows the owner to certify that the presence of visitors is in accordance with the provisions of the lease agreement, notably occupancy limitations and community rules. 
                  <br>It also helps to avoid possible problems like congestion, disruptions to other residents, and strain on common amenities.
                  <br>Tenants are invited to offer information on the visitor's intended stay, as well as any other pertinent facts. 
                  <br>Failure to notify the owner in advance may be regarded a breach of the lease conditions, resulting in warnings, penalties, or more action as indicated in the lease agreement.</li>
            </ul>
        </section>

        <section>
            <h2>Noise</h2>
            <ul>
                <li>Tenants are required to maintain modest noise levels at all times in order to provide a pleasant and harmonious living environment for all occupants of the property. 
                  <br>This involves maintaining noise from discussions, music, television, and other activities to a tolerable level, especially late at night and early in the morning, as specified in the community's quiet hours policy. 
                  <br>Noise should not disturb neighbors or impair their capacity to enjoy their living areas peacefully. 
                  <br>Residents are also asked to be aware of noise made by pets, home appliances, or gatherings in their units. 
                  <br>Repeated or extreme noise disruptions may result in warnings, fines, or other penalties as outlined in the lease agreement. 
                  <br>Tenants who follow this rule help to foster a courteous and thoughtful community environment.</li>
            </ul>
        </section>

        <section>
            <h2>Vehicle Parking</h2>
            <ul>
                <li>Only one compact vehicle, such as an e-bike, scooter, or motorbike, may be stored in the garage at any time. 
                  <br>This policy guarantees that garage space is used effectively and safely, avoiding congestion and possible risks. 
                  <br>Small cars should be parked in a way that doesn't block pathways, common spaces, or access to other storage locations. 
                  <br>Tenants must ensure that the specified vehicle is in good condition and does not leak fluids or pose any safety issues, such as fires or obstacles. 
                  <br>Modifications to the garage's usage, such as storing more automobiles or big objects, must be approved by the property owner or management.
                  <br>Violations of this regulation may result in warnings, penalties, or restricted parking privileges. </li>
            </ul>
        </section>

        <section>
            <h2> Late Payments</h2>
            <ul>
                <li>According to the lease agreement, all renters must pay their rent and any associated costs on time. 
                  <br>A payment is deemed late if it is not received by the property owner or management by the given date or within the authorized grace period, whichever applies. 
                  <br>If a payment is deemed late, extra costs such as late fees or penalties may be imposed, as specified in the lease agreement. 
                  <br>These fees are designed to promote prompt payments and offset any administrative or operational costs incurred as a result of delays. 
                  <br>Tenants are strongly recommended to arrange their payments ahead of time to prevent late fees. 
                  <br>In the event of unforeseen situations that may cause a delay, renters should quickly tell the property owner or management so that they may discuss the problem and seek viable solutions to avoid penalties. 
                  <br>Repeated late payments may result in not only financial penalties, but also warnings, restricted rights, or even termination of the leasing agreement.
                  <br>Making consistent and timely payments is critical for keeping a great rental history and having a smooth and polite landlord-tenant relationship.</li>
            </ul>
        </section>

        <section>
            <h2> For the two-floor apartment</h2>
            <ul>
                <li>A payment is deemed late if it is not received within four days of the set due date as stated in the lease agreement. 
                  <br>This grace period gives tenants some freedom in organizing their payment schedules while guaranteeing that the property owner receives rent on time. 
                  <br>However, once this four-day period has expired, the payment will be considered overdue, and late fees or other penalties may be levied in line with the lease's terms and conditions. 
                  <br>Tenants are advised to make payments on time to prevent these extra fees and any interruptions to their rent. 
                  <br>If circumstances happen that cause a payment delay, renters should tell the property owner or management as soon as possible so that alternate arrangements or viable solutions can be discussed.</li>
            </ul>
        </section>  

        <section>
            <h2>For the one-floor apartment</h2>
            <ul>
                <li>Payments are deemed late if they are not received within three days of the set due date as stated in the leasing agreement. 
                  <br>This three-day grace period is intended to give renters with a little buffer for unanticipated delays while still requiring prompt payments to guarantee the property's efficient administration. 
                  <br>Once this period has gone, the payment will be considered overdue, and the renter may face late fees or other penalties in line with the lease conditions. 
                  <br>Tenants are strongly advised to pay on or before the due date to prevent these fees and any potential impact on their rental history. When delays are inevitable, 
                  <br>tenants should quickly notify the property owner or management to discuss possible accommodations or payment arrangements.
                </li>
            </ul>
        </section>  

        <section>
            <h2> Gate Security</h2>
            <ul>
                <li>The gate must be kept securely closed at all times to ensure the safety and security of all inhabitants and the property as a whole. 
                  <br>Keeping the gate locked is an important precaution for preventing unwanted entrance, protecting against potential security risks, and ensuring a regulated atmosphere within the community. 
                  <br>Residents are advised to properly secure the gate behind them while entering or exiting and to avoid leaving it open for a lengthy period of time. 
                  <br>This policy helps to protect personal possessions, decreases the possibility of trespassing, and guarantees that the premises remain a safe and quiet living environment for everybody. 
                  <br>Residents are advised to promptly report any gate problems or unusual activities to property management or security staff in order to maintain the greatest level of safety.</li>
            </ul>
        </section>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
   
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        // PASSWORD TOGGLE 
        const togglePassword = document.querySelector('.toggle-password');
        const passwordField = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>