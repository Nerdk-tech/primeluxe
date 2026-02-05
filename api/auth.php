<?php
include 'db.php';
session_start();

// 1. REGISTRATION LOGIC
if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // MATCHED: This now matches the 'ref_id' we set in index.php
    $ref_by = isset($_SESSION['ref_id']) ? "'".$_SESSION['ref_id']."'" : "NULL";
    
    // Check if phone already exists to prevent duplicate accounts
    $check = mysqli_query($conn, "SELECT id FROM users WHERE phone = '$phone'");
    if(mysqli_num_rows($check) > 0) {
        header("Location: ../index.php?error=Phone number already registered");
        exit();
    }

    $sql = "INSERT INTO users (username, phone, password, referred_by, balance) 
            VALUES ('$username', '$phone', '$password', $ref_by, 400.00)";
            
    if(mysqli_query($conn, $sql)){
        $_SESSION['user_id'] = mysqli_insert_id($conn);
        
        // Clear the referral session after success so it doesn't linger
        unset($_SESSION['ref_id']); 
        
        header("Location: ../dashboard.php");
        exit();
    } else { 
        header("Location: ../index.php?error=Registration failed"); 
        exit();
    }
}

// 2. LOGIN LOGIC
if(isset($_POST['login'])){
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone'");
    $u = mysqli_fetch_assoc($res);
    
    if($u && password_verify($_POST['password'], $u['password'])){
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['username'] = $u['username']; // Storing username for the dashboard
        header("Location: ../dashboard.php");
        exit();
    } else { 
        header("Location: ../index.php?error=Invalid phone or password"); 
        exit();
    }
}
?>
