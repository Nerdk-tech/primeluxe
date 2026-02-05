<?php
session_start();
include 'db.php';

if(isset($_POST['bank_name'])){
    $u_id = $_POST['user_id'];
    $amt = (float)$_POST['amount']; // Convert to number for math
    $bank = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $acc = mysqli_real_escape_string($conn, $_POST['account_number']);

    // 1. CHECK MINIMUM LIMIT (1k)
    if($amt < 1000){
        header("Location: ../withdraw.php?error=Minimum withdrawal is ₦1,000");
        exit();
    }

    // 2. CHECK USER BALANCE
    $res = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM users WHERE id = '$u_id'"));
    
    if($res['balance'] >= $amt){
        // 3. Subtract money and log the request
        mysqli_query($conn, "UPDATE users SET balance = balance - '$amt' WHERE id = '$u_id'");
        mysqli_query($conn, "INSERT INTO withdrawals (user_id, amount, bank_name, account_number, status) 
                            VALUES ('$u_id', '$amt', '$bank', '$acc', 'pending')");
        
        header("Location: ../withdraw.php?success=Request Sent! Wait for alert.");
    } else {
        header("Location: ../withdraw.php?error=Insufficient Balance!");
    }
}
?>
