<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) { exit("Access Denied"); }

$uid = $_SESSION['user_id'];
$vip_id = (int)$_POST['vip_id'];

// MASTER PLAN DATA (Must match your Dashboard exactly)
$plans = [
    1 => ['price' => 3000, 'daily' => 400, 'days' => 30],
    2 => ['price' => 9000, 'daily' => 500, 'days' => 30],
    3 => ['price' => 15000, 'daily' => 650, 'days' => 30],
    4 => ['price' => 21000, 'daily' => 1000, 'days' => 30],
    5 => ['price' => 51000, 'daily' => 2000, 'days' => 30],
    6 => ['price' => 80000, 'daily' => 2500, 'days' => 40],
    7 => ['price' => 100000, 'daily' => 3000, 'days' => 40],
    8 => ['price' => 150000, 'daily' => 4250, 'days' => 40],
    9 => ['price' => 250000, 'daily' => 6750, 'days' => 40],
    10 => ['price' => 500000, 'daily' => 13000, 'days' => 40],
    11 => ['price' => 800000, 'daily' => 22500, 'days' => 40],
    12 => ['price' => 1000000, 'daily' => 30000, 'days' => 40]
];

if(!isset($plans[$vip_id])) {
    header("Location: ../dashboard.php?error=Invalid Plan");
    exit();
}

$selected = $plans[$vip_id];
$cost = $selected['price'];
$income = $selected['daily'];
$days = $selected['days'];

mysqli_begin_transaction($conn);

try {
    $res = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$uid' FOR UPDATE");
    $user = mysqli_fetch_assoc($res);

    if($user['balance'] < $cost) {
        throw new Exception("Insufficient balance to buy this VIP plan.");
    }

    // Deduct Balance
    mysqli_query($conn, "UPDATE users SET balance = balance - $cost WHERE id = '$uid'");

    // Record Investment with Daily Income and Max Steps
    $sql = "INSERT INTO investments (user_id, vip_level, amount_invested, daily_income, current_step, max_steps, status) 
            VALUES ('$uid', '$vip_id', '$cost', '$income', 0, '$days', 'active')";
    
    if(!mysqli_query($conn, $sql)) {
        throw new Exception("System error recording investment.");
    }

    mysqli_commit($conn);
    header("Location: ../orders.php?success=Investment Started!");

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: ../dashboard.php?error=" . $e->getMessage());
}
?>
