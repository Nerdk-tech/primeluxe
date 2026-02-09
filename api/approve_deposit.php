<?php
include '../api/db.php';

$id = $_GET['id'];

$d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id='$id' AND status='pending'"));
if(!$d) exit();

$uid = $d['user_id'];
$amt = $d['amount'];

mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
mysqli_query($conn, "UPDATE deposits SET status='approved' WHERE id='$id'");

header("Location: deposits.php?success=Deposit approved");
exit();
?>