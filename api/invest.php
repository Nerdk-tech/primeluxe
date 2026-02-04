<?php
include 'db.php';
include 'functions.php';
session_start();

if(isset($_POST['invest'])) {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $vip_level = $_POST['vip_level'];
    
    // Check if user has enough money
    $check = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($check);
    
    if($user['balance'] >= $amount) {
        // Deduct money
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id = '$user_id'");
        
        // Start 7-day timer
        $end_date = date('Y-m-d H:i:s', strtotime('+7 days'));
        mysqli_query($conn, "INSERT INTO investments (user_id, amount, vip_level, end_date) 
                             VALUES ('$user_id', '$amount', '$vip_level', '$end_date')");
        
        header("Location: ../dashboard.php?msg=Investment Started!");
    } else {
        header("Location: ../dashboard.php?error=Insufficient Balance");
    }
}
?>
