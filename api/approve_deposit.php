<?php
session_start();
include '../api/db.php';

// Check if admin is actually logged in
if(!isset($_SESSION['admin'])) {
    exit("Unauthorized access");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);

// 1. Fetch deposit and lock the row to prevent "Double Approval" bugs
mysqli_begin_transaction($conn);

$query = "SELECT * FROM deposits WHERE id='$id' AND status='pending' FOR UPDATE";
$result = mysqli_query($conn, $query);
$d = mysqli_fetch_assoc($result);

if(!$d) {
    mysqli_rollback($conn);
    header("Location: ../admin.php?error=Deposit already processed or not found");
    exit();
}

$uid = $d['user_id'];
$amt = $d['amount'];

// 2. Credit the User's Balance
mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");

// 3. Update Deposit Status
mysqli_query($conn, "UPDATE deposits SET status='approved', approved_at=NOW() WHERE id='$id'");

/* ============================================================
   AUTOMATED REFERRAL COMMISSIONS (L1: 25% | L2: 3%)
   ============================================================ */
// Check for Level 1 Upline
$user_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id='$uid'"));
$upline1_id = $user_info['referred_by'];

if(!empty($upline1_id)) {
    // Pay Level 1 (25%)
    $comm1 = $amt * 0.25;
    mysqli_query($conn, "UPDATE users SET balance = balance + $comm1 WHERE id='$upline1_id'");
    
    // Check for Level 2 Upline
    $upline1_info = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id='$upline1_id'"));
    $upline2_id = $upline1_info['referred_by'];
    
    if(!empty($upline2_id)) {
        // Pay Level 2 (3%)
        $comm2 = $amt * 0.03;
        mysqli_query($conn, "UPDATE users SET balance = balance + $comm2 WHERE id='$upline2_id'");
    }
}

// Commit all changes at once
mysqli_commit($conn);

header("Location: ../admin.php?success=Deposit approved and commissions paid");
exit();
?>
