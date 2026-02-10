<?php
include 'db.php';

// --- SECURITY LOCK ---
$key = "ella_prime_99"; 
if (!isset($_GET['key']) || $_GET['key'] !== $key) {
    die("❌ Error: Unauthorized access.");
}
// ---------------------

$vip_steps = [
    1 => [7250, 11500, 15750, 20000],
    2 => [29750, 36500, 43250, 50000],
    3 => [58400, 63800, 69200, 74600, 80000],
    4 => [88400, 93800, 99200, 104600, 110000],
    5 => [120400, 127800, 135200, 142600, 150000]
];

// Logic: Check if 24 hours have passed since last growth
$sql = "SELECT * FROM investments WHERE status = 'active' AND last_growth <= NOW() - INTERVAL 24 HOUR";
$result = mysqli_query($conn, $sql);

while($inv = mysqli_fetch_assoc($result)) {
    $inv_id = $inv['id'];
    $uid   = $inv['user_id'];
    $vip   = $inv['vip_level'];
    $step  = $inv['current_step']; 
    
    if (isset($vip_steps[$vip][$step])) {
        $target_total = $vip_steps[$vip][$step];
        $prev_total = ($step == 0) ? $inv['amount_invested'] : $vip_steps[$vip][$step - 1];
        $profit_to_add = $target_total - $prev_total;

        mysqli_begin_transaction($conn);
        try {
            mysqli_query($conn, "UPDATE users SET balance = balance + $profit_to_add WHERE id = $uid");
            
            $msg = "VIP $vip Daily Growth (Day " . ($step + 1) . ")";
            mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                                 VALUES ($uid, $profit_to_add, 'credit', '$msg')");
            
            $next_step = $step + 1;
            $status = ($next_step >= count($vip_steps[$vip])) ? 'completed' : 'active';
            
            mysqli_query($conn, "UPDATE investments SET 
                current_step = $next_step, 
                last_growth = CURRENT_TIMESTAMP, 
                status = '$status' 
                WHERE id = $inv_id");
                
            mysqli_commit($conn);
            echo "✅ Day ".($step+1)." processed for User $uid.<br>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
        }
    }
}
?>
