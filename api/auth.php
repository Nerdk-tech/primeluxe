<?php
include 'db.php';
session_start();

// 1. REGISTRATION LOGIC
if(isset($_POST['register'])){
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    // Clean phone number (removes spaces, dashes, etc.)
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']); 
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    // Referral Logic
    $ref_by = isset($_SESSION['ref_id']) ? (int)$_SESSION['ref_id'] : "NULL";
    
    // Prevent duplicate phones
    $check = mysqli_query($conn, "SELECT id FROM users WHERE phone = '$phone'");
    if(mysqli_num_rows($check) > 0) {
        header("Location: ../index.php?error=This phone number is already in use.");
        exit();
    }

    $sql = "INSERT INTO users (username, phone, password, referred_by, balance) 
            VALUES ('$username', '$phone', '$password', $ref_by, 400.00)";
            
    if(mysqli_query($conn, $sql)){
        $new_user_id = mysqli_insert_id($conn);
        
        // Secure the session
        session_regenerate_id(true);
        $_SESSION['user_id'] = $new_user_id;
        $_SESSION['username'] = $username;
        
        unset($_SESSION['ref_id']); 
        header("Location: ../dashboard.php");
        exit();
    } else { 
        header("Location: ../index.php?error=System error during registration."); 
        exit();
    }
}

// 2. LOGIN LOGIC
if(isset($_POST['login'])){
    $phone = preg_replace('/[^0-9]/', '', $_POST['phone']);
    $res = mysqli_query($conn, "SELECT * FROM users WHERE phone = '$phone'");
    $u = mysqli_fetch_assoc($res);
    
    if($u && password_verify($_POST['password'], $u['password'])){
        // Security: Prevent session fixation
        session_regenerate_id(true);
        
        $_SESSION['user_id'] = $u['id'];
        $_SESSION['username'] = $u['username'];
        
        header("Location: ../dashboard.php");
        exit();
    } else { 
        header("Location: ../index.php?error=Incorrect details. Please try again."); 
        exit();
    }
}
?>
