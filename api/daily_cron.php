<?php
include 'db.php';

/* Ella's 24-Hour Growth Steps 
  Instead of adding a flat 200, we move them to the next milestone.
*/
$vip_steps = [
    1 => [7250, 11500, 15750, 20000],
    2 => [29750, 36500, 43250, 50000],
    3 => [58400, 63800, 69200, 74600, 80000],
    4 => [88400, 93800, 99200, 104600, 110000],
    5 => [120400, 127800, 135200, 142600, 150000]
];

// 1. Only pick users who haven't been paid in the last 24 hours
$sql = "SELECT * FROM investments WHERE status = 'active' AND last_growth <= NOW() - INTERVAL 24 HOUR";
$result = mysqli_query($conn, $sql);



while($inv = mysqli_fetch_assoc($result)) {
    $inv_id = $inv['id'];
    $uid   = $inv['user_id'];
    $vip   = $inv['vip_level'];
    $step  = $inv['current_step']; 
    
    // Check if there is a next step available for this VIP level
    if (isset($vip_steps[$vip][$step])) {
        $new_total = $vip_steps[$vip][$step];
        
        mysqli_begin_transaction($conn);
        try {
            // A. Update the User's Main Balance
            // Note: This replaces their balance with the new 'Step' amount
            mysqli_query($conn, "UPDATE users SET balance = $new_total WHERE id = $uid");
            
            // B. Log this in the earnings table so the user sees it in their history
            $msg = "VIP $vip Daily Growth (Day " . ($step + 1) . ")";
            mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                                 VALUES ($uid, $new_total, 'credit', '$msg')");
            
            // C. Move to the next step and update the time
            $next_step = $step + 1;
            $status = ($next_step >= count($vip_steps[$vip])) ? 'completed' : 'active';
            
            mysqli_query($conn, "UPDATE investments SET 
                current_step = $next_step, 
                last_growth = CURRENT_TIMESTAMP, 
                status = '$status' 
                WHERE id = $inv_id");
                
            mysqli_commit($conn);
            echo "✅ Processed: User $uid (VIP $vip) moved to Day $next_step.<br>";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo "❌ Failed: User $uid - " . $e->getMessage() . "<br>";
        }
    }
}
?>
