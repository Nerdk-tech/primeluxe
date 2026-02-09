<?php
session_start();
include 'db.php';

// 1. Basic Auth Guard
if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_SESSION['user_id'];
    
    // 2. Sanitize and Validate Inputs
    $amount = filter_var($_POST['amt'], FILTER_VALIDATE_FLOAT);
    $sender = trim(mysqli_real_escape_string($conn, $_POST['sender_name']));

    // 3. Prevent empty or invalid submissions
    if ($amount === false || $amount <= 0) {
        header("Location: ../deposit.php?error=Invalid amount entered.");
        exit();
    }

    if (empty($sender)) {
        header("Location: ../deposit.php?error=Sender name is required.");
        exit();
    }

    // 4. Use Prepared Statements to prevent SQL Injection
    $stmt = $conn->prepare("INSERT INTO deposits (user_id, amount, sender_name, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->bind_param("ids", $uid, $amount, $sender);

    if ($stmt->execute()) {
        header("Location: ../deposit.php?success=Proof submitted! Admin will verify within 1-6 hours.");
    } else {
        // Log error for admin, show generic message to user
        error_log("Deposit Error: " . $stmt->error);
        header("Location: ../deposit.php?error=System busy. Please try again later.");
    }

    $stmt->close();
    exit();
}
?>
