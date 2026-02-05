<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) exit("Access Denied");

$uid = $_SESSION['user_id'];
$vip_id = (int)$_POST['vip_id'];

$prices = [1 => 3000, 2 => 23000, 3 => 53000, 4 => 83000, 5 => 113000];
$cost = $prices[$vip_id];

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM users WHERE id = '$uid'"));

if($user['balance'] < $cost) {
    header("Location: ../dashboard.php?error=Insufficient funds");
    exit();
}

$check = mysqli_query($conn, "SELECT id FROM investments WHERE user_id = '$uid' AND status = 'active'");
if(mysqli_num_rows($check) > 0) {
    header("Location: ../dashboard.php?error=Already have an active plan");
    exit();
}

mysqli_query($conn, "UPDATE users SET balance = balance - $cost WHERE id = '$uid'");

// FIXED: Ensure these column names match your DB. 
// If you run the SQL fix below, this will work perfectly.
$sql = "INSERT INTO investments (user_id, vip_level, amount_invested, current_step, status) 
        VALUES ('$uid', '$vip_id', '$cost', 0, 'active')";

if(mysqli_query($conn, $sql)) {
    header("Location: ../dashboard.php?success=Investment activated!");
} else {
    // If it fails, give the money back
    mysqli_query($conn, "UPDATE users SET balance = balance + $cost WHERE id = '$uid'");
    header("Location: ../dashboard.php?error=Database Error");
}
?>
