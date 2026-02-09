<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) exit("Access Denied");

$uid = $_SESSION['user_id'];
$vip_id = (int)$_POST['vip_id'];

// Configuration for VIP Plans
// Format: [Price, Daily Income, Duration in Days]
$plans = [
    1 => [3000, 300, 30],
    2 => [23000, 2400, 35],
    3 => [53000, 5800, 40],
    4 => [83000, 9500, 45],
    5 => [113000, 13500, 50]
];

if(!isset($plans[$vip_id])) {
    header("Location: ../dashboard.php?error=Invalid Plan");
    exit();
}

$cost = $plans[$vip_id][0];
$daily = $plans[$vip_id][1];
$max_steps = $plans[$vip_id][2];

// Start Transaction
mysqli_begin_transaction($conn);

try {
    // 1. Lock the user row for update (prevents balance-glitch double-clicks)
    $res = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$uid' FOR UPDATE");
    $user = mysqli_fetch_assoc($res);

    if($user['balance'] < $cost) {
        throw new Exception("Insufficient balance");
    }

    // 2. Deduct Balance
    mysqli_query($conn, "UPDATE users SET balance = balance - $cost WHERE id = '$uid'");

    // 3. Insert Investment Record
    $sql = "INSERT INTO investments (user_id, vip_level, amount_invested, daily_income, max_steps, current_step, status) 
            VALUES ('$uid', '$vip_id', '$cost', '$daily', '$max_steps', 0, 'active')";
    
    if(!mysqli_query($conn, $sql)) {
        throw new Exception("Database failed to record investment");
    }

    // 4. Record Transaction History (Optional but recommended)
    mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                         VALUES ('$uid', '$cost', 'debit', 'Purchased VIP $vip_id')");

    mysqli_commit($conn);
    header("Location: ../orders.php?success=Investment VIP $vip_id activated!");

} catch (Exception $e) {
    mysqli_rollback($conn);
    header("Location: ../dashboard.php?error=" . $e->getMessage());
}
?>
