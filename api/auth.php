<?php
include 'db.php';
session_start();

if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Check if referral code exists in URL (optional)
    $ref = isset($_GET['ref']) ? $_GET['ref'] : NULL;
    
    $sql = "INSERT INTO users (username, phone, password, referred_by, balance) 
            VALUES ('$username', '$phone', '$password', '$ref', 400.00)";
    
    if(mysqli_query($conn, $sql)){
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        header("Location: ../dashboard.php");
    } else {
        header("Location: ../index.php?error=Username or Phone already exists");
    }
}

if(isset($_POST['login'])){
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $result = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone'");
    $user = mysqli_fetch_assoc($result);
    
    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id'];
        header("Location: ../dashboard.php");
    } else {
        header("Location: ../index.php?error=Invalid credentials");
    }
}
?>
