<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) exit();

$uid = $_SESSION['user_id'];
$amount = mysqli_real_escape_string($conn, $_POST['amt']);
$sender = mysqli_real_escape_string($conn, $_POST['sender_name']);

mysqli_query($conn, "
INSERT INTO deposits (user_id, amount, sender_name, status)
VALUES ('$uid', '$amount', '$sender', 'pending')
");

header("Location: ../deposit.php?success=Proof submitted. Await admin approval.");
exit();
?>