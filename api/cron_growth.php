<?php
include 'db.php';

/* Ella's 24-Hour Growth Steps (Daily ROI)
*/
$vip_steps = [
    1 => [7250, 11500, 15750, 20000],
    2 => [29750, 36500, 43250, 50000],
    3 => [58400, 63800, 69200, 74600, 80000],
    4 => [88400, 93800, 99200, 104600, 110000],
    5 => [120400, 127800, 135200, 142600, 150000]
];

// Changed INTERVAL to 24 HOUR for daily updates
$sql = "SELECT * FROM investments WHERE status = 'active' AND last_growth <= NOW() - INTERVAL 24 HOUR";
$result = mysqli_query($conn, $sql);



while($inv = mysqli_fetch_assoc($result)) {
    $inv_id = $inv['id'];
    $uid = $inv['user_id'];
    $vip = $inv['vip_level'];
    $current_step_idx = $inv['current_step']; 
    
    if (isset($vip_steps[$vip][$current_step_idx])) {
        $new_balance = $vip_steps[$vip][$current_step_idx];
        
        mysqli_begin_transaction($conn);
        
        try {
            // Update balance
            mysqli_query($conn, "UPDATE users SET balance = $new_balance WHERE id = $uid");
            
            // Log earning
            $desc = "Daily VIP $vip Growth (Step " . ($current_step_idx + 1) . ")";
            mysqli_query($conn, "INSERT INTO earnings_log (user_id, amount, description) VALUES ($uid, $new_balance, '$desc')");
            
            // Increment step and check for completion
            $next_step = $current_step_idx + 1;
            $new_status = ($next_step >= count($vip_steps[$vip])) ? 'completed' : 'active';
            
            mysqli_query($conn, "UPDATE investments SET 
                current_step = $next_step, 
                last_growth = CURRENT_TIMESTAMP, 
                status = '$new_status' 
                WHERE id = $inv_id");
                
            mysqli_commit($conn);
            echo "Day " . $next_step . " processed for User $uid.<br>";
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "Error processing User $uid.<br>";
        }
    }
}
?>
