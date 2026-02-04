<?php
include 'db.php';
session_start(); // Essential to keep the user logged in

// Registration Logic
if(isset($_POST['register'])){
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $bonus = 400.00;
    
    $sql = "INSERT INTO users (phone, password, balance) VALUES ('$phone', '$password', '$bonus')";
    
    if(mysqli_query($conn, $sql)){
        $_SESSION['user_id'] = mysqli_insert_id($conn); // Get the ID of the new user
        header("Location: ../dashboard.php?success=Welcome!");
    } else {
        header("Location: ../index.php?error=Phone number already exists");
    }
}

// Login Logic (Add this so the login form actually works)
if(isset($_POST['login'])){
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone'");
    $user = mysqli_fetch_assoc($result);
    
    if($user && password_verify($password, $user['password'])){
        $_SESSION['user_id'] = $user['id']; // Save user ID to session
        header("Location: ../dashboard.php");
    } else {
        header("Location: ../index.php?error=Invalid phone or password");
    }
}
?>
