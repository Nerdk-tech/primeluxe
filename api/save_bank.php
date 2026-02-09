<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) { exit("Denied"); }

$uid = $_SESSION['user_id'];
$bank = mysqli_real_escape_string($conn, $_POST['bank_name']);
$acc  = mysqli_real_escape_string($conn, $_POST['account_number']);
$name = mysqli_real_escape_string($conn, $_POST['account_name']);

$sql = "UPDATE users SET 
        bank_name = '$bank', 
        account_number = '$acc', 
        account_name = '$name' 
        WHERE id = '$uid'";

if(mysqli_query($conn, $sql)) {
    header("Location: ../bank_details.php?success=Bank information updated!");
} else {
    header("Location: ../bank_details.php?error=Update failed");
}
exit();
