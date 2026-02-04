<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $amount = $_POST['amount'];
    $bank_name = $_POST['bank_name'];
    $account_number = $_POST['account_number'];

    // 1. Check if amount is up to 1k
    if ($amount < 1000) {
        die("<script>alert('Minimum withdrawal na ₦1,000.'); window.history.back();</script>");
    }

    // 2. Verify user has enough balance
    $user_query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($user_query);

    if ($user['balance'] >= $amount) {
        // 3. Deduct money from balance immediately
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id = '$user_id'");

        // 4. Insert into withdrawals table with bank info
        $sql = "INSERT INTO withdrawals (user_id, amount, bank_name, account_number, status) 
                VALUES ('$user_id', '$amount', '$bank_name', '$account_number', 'pending')";
        
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Withdrawal Request Sent! Admin go check am.'); window.location.href='../dashboard.php';</script>";
        }
    } else {
        echo "<script>alert('Insufficient balance for this withdrawal.'); window.history.back();</script>";
    }
}
?>
