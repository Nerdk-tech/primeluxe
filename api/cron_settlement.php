<?php
include 'db.php';

// Prevent accidental browser access - only allow via CLI/Cron
if (php_sapi_name() !== 'cli' && !isset($_GET['run_secret_key'])) {
    die("Access Denied.");
}

// 1. Get all active investments that haven't finished their 30 days
$query = "SELECT i.*, u.id as user_id FROM investments i 
          JOIN users u ON i.user_id = u.id 
          WHERE i.status = 'active' AND i.current_step < i.max_steps";

$result = mysqli_query($conn, $query);

$processed = 0;

while ($inv = mysqli_fetch_assoc($result)) {
    $inv_id = $inv['id'];
    $user_id = $inv['user_id'];
    $daily_profit = $inv['daily_income'];

    // Start Transaction for each payout
    mysqli_begin_transaction($conn);

    try {
        // A. Add daily profit to user balance
        mysqli_query($conn, "UPDATE users SET balance = balance + $daily_profit WHERE id = '$user_id'");

        // B. Log the credit in transactions
        mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                             VALUES ('$user_id', '$daily_profit', 'credit', 'Daily ROI: VIP {$inv['vip_level']}')");

        // C. Update investment: increment step and check if finished
        $new_step = $inv['current_step'] + 1;
        $status = ($new_step >= $inv['max_steps']) ? 'completed' : 'active';
        
        mysqli_query($conn, "UPDATE investments SET 
                             current_step = '$new_step', 
                             status = '$status', 
                             last_growth = NOW() 
                             WHERE id = '$inv_id'");

        mysqli_commit($conn);
        $processed++;

    } catch (Exception $e) {
        mysqli_rollback($conn);
    }
}

echo "Settlement Complete. Processed $processed investments.";
?>
