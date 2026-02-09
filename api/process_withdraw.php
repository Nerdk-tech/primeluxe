<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) exit();

$uid = $_SESSION['user_id'];
$bank = mysqli_real_escape_string($conn, $_POST['bank_name']);
$acc = mysqli_real_escape_string($conn, $_POST['account_number']);
$acc_name = mysqli_real_escape_string($conn, $_POST['account_name']);
$amount = (float) $_POST['amount'];

$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM users WHERE id='$uid'"));
if($amount > $u['balance']) {
    header("Location: ../withdraw.php?error=Insufficient balance");
    exit();
}

mysqli_query($conn, "
INSERT INTO withdrawals (user_id, amount, bank_name, account_number, account_name, status)
VALUES ('$uid','$amount','$bank','$acc','$acc_name','pending')
");

header("Location: ../withdraw_history.php?success=Request submitted");
exit();
?>