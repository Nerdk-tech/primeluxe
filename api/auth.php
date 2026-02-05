<?php
include 'db.php';
session_start();
if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $ref_by = isset($_SESSION['ref_by']) ? $_SESSION['ref_by'] : "NULL";
    
    $sql = "INSERT INTO users (username, phone, password, referred_by, balance) 
            VALUES ('$username', '$phone', '$password', $ref_by, 400.00)";
    if(mysqli_query($conn, $sql)){
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        header("Location: ../dashboard.php");
    } else { header("Location: ../index.php?error=Exists"); }
}
if(isset($_POST['login'])){
    $phone = $_POST['phone'];
    $res = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone'");
    $u = mysqli_fetch_assoc($res);
    if($u && password_verify($_POST['password'], $u['password'])){
        $_SESSION['user_id'] = $u['id'];
        header("Location: ../dashboard.php");
    } else { header("Location: ../index.php?error=Wrong"); }
}
?>
