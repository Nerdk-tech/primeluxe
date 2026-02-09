<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$uid = $_SESSION['user_id'];
$amt = (float)$_POST['amount'];
$bank = mysqli_real_escape_string($conn, $_POST['bank_name']);
$acc = mysqli_real_escape_string($conn, $_POST['account_number']);
$acc_name = mysqli_real_escape_string($conn, $_POST['account_name']); // Added for bank clarity

// 1. Minimum Withdrawal Check
if($amt < 1000) {
    header("Location: ../withdraw.php?error=Minimum withdrawal is ₦1,000");
    exit();
}

// START TRANSACTION
mysqli_begin_transaction($conn);

try {
    // 2. Check Balance with 'FOR UPDATE' 
    // This locks the row so no other script can change the balance until we are done.
    $query = "SELECT balance FROM users WHERE id = '$uid' FOR UPDATE";
    $res = mysqli_query($conn, $query);
    $u = mysqli_fetch_assoc($res);

    if(!$u || $u['balance'] < $amt) {
        throw new Exception("Insufficient Balance");
    }

    // 3. Deduct Balance
    $deduct = mysqli_query($conn, "UPDATE users SET balance = balance - $amt WHERE id = '$uid'");
    if(!$deduct) throw new Exception("Failed to update balance");

    // 4. Record Withdrawal Request
    $insert = mysqli_query($conn, "INSERT INTO withdrawals (user_id, amount, bank_name, account_number, account_name, status) 
                                   VALUES ('$uid', '$amt', '$bank', '$acc', '$acc_name', 'pending')");
    if(!$insert) throw new Exception("Failed to record withdrawal");

    // 5. Log Transaction for User History
    mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                         VALUES ('$uid', '$amt', 'debit', 'Withdrawal Request')");

    // COMMIT ALL CHANGES
    mysqli_commit($conn);
    header("Location: ../withdraw_history.php?success=Withdrawal submitted! Ella will process it shortly.");

} catch (Exception $e) {
    // UNDO EVERYTHING IF AN ERROR OCCURRED
    mysqli_rollback($conn);
    $msg = $e->getMessage();
    header("Location: ../withdraw.php?error=$msg");
}
?>
