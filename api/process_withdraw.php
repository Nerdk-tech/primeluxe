<?php
session_start();
include 'db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_SESSION['user_id'];
    $bank = mysqli_real_escape_string($conn, $_POST['bank_name']);
    $acc = mysqli_real_escape_string($conn, $_POST['account_number']);
    $acc_name = mysqli_real_escape_string($conn, $_POST['account_name']);
    $amount = (float) $_POST['amount'];

    // 1. Basic validation
    if ($amount < 1000) { // Set a minimum withdrawal limit
        header("Location: ../withdraw.php?error=Minimum withdrawal is ₦1,000");
        exit();
    }

    // Start Database Transaction
    mysqli_begin_transaction($conn);

    try {
        // 2. Fetch and LOCK the user's balance to prevent race conditions
        $res = mysqli_query($conn, "SELECT balance FROM users WHERE id='$uid' FOR UPDATE");
        $u = mysqli_fetch_assoc($res);

        if($amount > $u['balance']) {
            throw new Exception("Insufficient balance");
        }

        // 3. Deduct the amount from balance IMMEDIATELY
        // This prevents the user from spending the money while the withdrawal is pending
        mysqli_query($conn, "UPDATE users SET balance = balance - $amount WHERE id='$uid'");

        // 4. Log the withdrawal request
        $stmt = $conn->prepare("INSERT INTO withdrawals (user_id, amount, bank_name, account_number, account_name, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("idsss", $uid, $amount, $bank, $acc, $acc_name);
        
        if (!$stmt->execute()) {
            throw new Exception("Database error. Request failed.");
        }

        // 5. Log the transaction for the user's history
        mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) VALUES ('$uid', '$amount', 'debit', 'Withdrawal Request')");

        mysqli_commit($conn);
        header("Location: ../withdraw_history.php?success=Withdrawal request of ₦" . number_format($amount) . " submitted!");

    } catch (Exception $e) {
        // If anything goes wrong, undo the balance deduction
        mysqli_rollback($conn);
        $msg = $e->getMessage();
        header("Location: ../withdraw.php?error=$msg");
    }
    exit();
}
?>
