<?php
session_start();
include 'db.php';

$uid = $_SESSION['user_id'];
$amt = (float)$_POST['amount'];
$bank = mysqli_real_escape_string($conn, $_POST['bank_name']);
$acc = mysqli_real_escape_string($conn, $_POST['account_number']);

// 1. Minimum check
if($amt < 1000) { exit("Minimum withdrawal is 1000"); }

// 2. Check balance
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM users WHERE id = '$uid'"));
if($u['balance'] < $amt) { exit("Insufficient Balance"); }

// 3. Deduct and Record
mysqli_query($conn, "UPDATE users SET balance = balance - $amt WHERE id = '$uid'");
mysqli_query($conn, "INSERT INTO withdrawals (user_id, amount, bank_name, account_number, status) VALUES ('$uid', '$amt', '$bank', '$acc', 'pending')");

header("Location: ../dashboard.php?success=Withdrawal submitted. Ella will process it soon!");
?>
