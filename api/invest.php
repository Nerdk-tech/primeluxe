<?php
include 'db.php';
include 'functions.php'; 
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if(isset($_POST['invest'])) {
    $user_id = $_SESSION['user_id'];
    $vip_level = (int)$_POST['vip_level'];
    
    // 1. STOPS THE "3 VIPS" PROBLEM:
    // Check if user already has this VIP ACTIVE. 
    // If they already have it, don't add another one!
    $already_has = mysqli_query($conn, "SELECT id FROM investments 
                                        WHERE user_id = '$user_id' 
                                        AND vip_level = '$vip_level' 
                                        AND status = 'active'");
    
    if(mysqli_num_rows($already_has) > 0) {
        header("Location: ../dashboard.php?error=You already have an active VIP $vip_level plan.");
        exit();
    }

    // 2. SYNCED PLAN DATA (Matches your daily_cron.php steps)
    $plans = [
        1 => ['price' => 3000,   'daily' => 4250, 'steps' => 4],
        2 => ['price' => 23000,  'daily' => 6750, 'steps' => 4],
        3 => ['price' => 53000,  'daily' => 5400, 'steps' => 5], // 58400 - 53000 = 5400
        4 => ['price' => 83000,  'daily' => 5400, 'steps' => 5],
        5 => ['price' => 113000, 'daily' => 7400, 'steps' => 5]
    ];

    if(!isset($plans[$vip_level])) {
        header("Location: ../dashboard.php?error=Invalid Plan.");
        exit();
    }

    $amount = $plans[$vip_level]['price'];
    $daily  = $plans[$vip_level]['daily'];
    $steps  = $plans[$vip_level]['steps'];

    mysqli_begin_transaction($conn);
    try {
        $user_check = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id' FOR UPDATE");
        $user = mysqli_fetch_assoc($user_check);
        
        if($user['balance'] < $amount) { throw new Exception("Insufficient Balance"); }

        // Deduct Balance
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id = '$user_id'");
        
        // Log Transaction
        mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                             VALUES ('$user_id', '$amount', 'debit', 'Purchased VIP $vip_level')");

        // Create Investment
        $sql = "INSERT INTO investments (user_id, amount_invested, daily_income, vip_level, current_step, max_steps, status, last_growth) 
                VALUES ('$user_id', '$amount', '$daily', '$vip_level', 0, '$steps', 'active', NOW())";
        mysqli_query($conn, $sql);

        mysqli_commit($conn);
        echo "<script>alert('✅ VIP $vip_level Activated! Check growth in Orders.'); window.location.href='../orders.php';</script>";
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: ../dashboard.php?error=".$e->getMessage());
    }
}
?>
