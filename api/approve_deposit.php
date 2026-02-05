<?php
include 'db.php';
$id = $_GET['id'];

$dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id = '$id'"));
$uid = $dep['user_id'];
$amt = $dep['amount'];

// 1. Give User the money
mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = '$uid'");

// 2. Mark as completed
mysqli_query($conn, "UPDATE deposits SET status = 'completed' WHERE id = '$id'");

// 3. Optional: Add Referral Commission here (L1 25%, L2 3%)
// ...

header("Location: ../admin.php?msg=DepositApproved");
?>
