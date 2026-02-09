<?php
include 'db.php';
include 'functions.php'; // Contains getPlanData() and processTeamCommissions()
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if(isset($_POST['invest'])) {
    $user_id = $_SESSION['user_id'];
    $vip_level = (int)$_POST['vip_level'];
    
    // Fetch plan details from your function
    $plan = getPlanData($vip_level);
    if(!$plan) {
        header("Location: ../dashboard.php?error=Invalid Plan Selected");
        exit();
    }

    // Fixed prices based on VIP level (prevents users from editing the 'amount' in the browser)
    $vip_prices = [1 => 3000, 2 => 23000, 3 => 53000, 4 => 83000, 5 => 113000];
    $amount = $vip_prices[$vip_level];

    // Start Transaction for data safety
    mysqli_begin_transaction($conn);

    try {
        // 1. Check balance and LOCK the row (FOR UPDATE) to prevent double-click hacks
        $check = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id' FOR UPDATE");
        $user = mysqli_fetch_assoc($check);
        
        if($user['balance'] < $amount) {
            throw new Exception("Insufficient Balance");
        }

        // 2. Deduct money
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id = '$user_id'");
        
        // 3. Log the debit in transaction history
        mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                             VALUES ('$user_id', '$amount', 'debit', 'Purchased VIP $vip_level')");

        // 4. Start the investment (Using current_step logic for your 24hr cron)
        $sql = "INSERT INTO investments (user_id, amount, vip_level, current_step, status, last_growth) 
                VALUES ('$user_id', '$amount', '$vip_level', 0, 'active', NOW())";
        mysqli_query($conn, $sql);

        // 5. PAY THE UPLINES (Referral Commissions)
        // This calls the function we just built!
        processTeamCommissions($user_id, $amount, $vip_level, $conn);

        // If everything is perfect, save to database
        mysqli_commit($conn);
        header("Location: ../dashboard.php?msg=VIP $vip_level Activated Successfully!");

    } catch (Exception $e) {
        // If anything fails, undo everything (refund the user)
        mysqli_rollback($conn);
        $error_msg = $e->getMessage();
        header("Location: ../dashboard.php?error=$error_msg");
    }
}
?>
