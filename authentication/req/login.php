<?php
session_start();
include '../../connections.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['phone_number'] = $user['phone_number'];

        if ($user['role'] == 'user') {
            $stmt = $pdo->prepare("SELECT tenant_id, move_in_date, units FROM tenant WHERE user_id = ?");
            $stmt->execute([$user['id']]);
            $tenant = $stmt->fetch();

            
            if ($tenant) {
                $_SESSION['tenant_id'] = $tenant['tenant_id'];
                $_SESSION['move_in_date'] = date('m/d/Y', strtotime($tenant['move_in_date']));
                $_SESSION['unit'] = $tenant['units'];
            }

            header('Location: ../../tenant/home.php');
            exit;
        } elseif ($user['role'] == 'admin') {
            // For admin, fetch and set the tenant_id for administrative purposes
            $stmt = $pdo->prepare("SELECT tenant_id FROM tenant LIMIT 1"); // Adjust query as needed
            $stmt->execute();
            $adminTenant = $stmt->fetch();

            if ($adminTenant) {
                $_SESSION['tenant_id'] = $adminTenant['tenant_id'];
            }

            header('Location: ../../admin/dashboard.php');
            exit;
        }
    } else {
        // Check if the email is registered in the pending_users table
        $stmt = $pdo->prepare("SELECT * FROM pending_users WHERE email = ?");
        $stmt->execute([$email]);
        $pending_user = $stmt->fetch();

        if ($pending_user && password_verify($password, $pending_user['password'])) {
            $_SESSION['user_id'] = $pending_user['pending_user_id'];
            $_SESSION['role'] = $pending_user['role'];
            $_SESSION['fullname'] = $pending_user['fullname'];
            $_SESSION['phone_number'] = $pending_user['phone_number'];

            header('Location: ../../pending_users/available-units.php');
            exit;
        } else {
            header('Location: ../login.php?error=' . urlencode('Incorrect email or password.'));
            exit;
        }
    }
}
?>
