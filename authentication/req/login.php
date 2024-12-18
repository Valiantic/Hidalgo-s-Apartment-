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
        $_SESSION['fullname'] = $user['fullname'];
        $_SESSION['phone_number'] = $user['phone_number'];
        
        // Fetch tenant start date
        $stmt = $pdo->prepare("SELECT move_in_date FROM tenant WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $tenant = $stmt->fetch();
        $_SESSION['move_in_date'] = date('m/d/Y', strtotime($tenant['move_in_date']));

        if ($user['role'] == 'admin') {
            header('Location: ../../admin/dashboard.php');
            exit;
        } else {
            header('Location: ../../tenant/home.php');            
            exit;
        }
    } else {
        
        header('Location: ../login.php?error=' . urlencode('Incorrect email or password.'));
        exit;
    }
}
?>
