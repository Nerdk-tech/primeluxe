<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) exit("Access Denied");

$uid = $_SESSION['user_id'];
$vip_id = (int)$_POST['vip_id'];

// Define Prices
$prices = [1 => 3000, 2 => 23000, 3 => 53000, 4 => 83000, 5 => 113000];
$cost = $prices[$vip_id];

// 1. Check User Balance
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM users WHERE id = '$uid'"));

if($user['balance'] < $cost) {
    header("Location: ../dashboard.php?error=Insufficient funds. Please recharge.");
    exit();
}

// 2. Check if user already has an active investment
$check = mysqli_query($conn, "SELECT id FROM investments WHERE user_id = '$uid' AND status = 'active'");
if(mysqli_num_rows($check) > 0) {
    header("Location: ../dashboard.php?error=You already have an active plan.");
    exit();
}

// 3. Deduct Money and Start Investment
mysqli_query($conn, "UPDATE users SET balance = balance - $cost WHERE id = '$uid'");

// Start at Step 0. The growth script will handle the rest every 48 hours.
$sql = "INSERT INTO investments (user_id, vip_level, amount_invested, current_step, status) 
        VALUES ('$uid', '$vip_id', '$cost', 0, 'active')";

if(mysqli_query($conn, $sql)) {
    header("Location: ../dashboard.php?success=Investment activated! Growth starts in 48 hours.");
} else {
    header("Location: ../dashboard.php?error=System error. Contact support.");
}
?>
