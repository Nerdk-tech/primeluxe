<?php
include 'db.php';

// Define the steps Ella gave you for each VIP level
$vip_steps = [
    1 => [7250, 11500, 15750, 20000],        // VIP 1 Steps
    2 => [29750, 36500, 43250, 50000],      // VIP 2 Steps
    3 => [58400, 63800, 69200, 74600, 80000], // VIP 3 Steps
    4 => [88400, 93800, 99200, 104600, 110000], // VIP 4 Steps
    5 => [120400, 127800, 135200, 142600, 150000] // VIP 5 Steps
];

// 1. Find investments where it's been at least 48 hours since the last growth
$sql = "SELECT * FROM investments WHERE status = 'active' AND last_growth <= NOW() - INTERVAL 48 HOUR";
$result = mysqli_query($conn, $sql);

while($inv = mysqli_fetch_assoc($result)) {
    $inv_id = $inv['id'];
    $uid = $inv['user_id'];
    $vip = $inv['vip_level'];
    $current_step_idx = $inv['current_step']; // Starts at 0
    
    // Check if there are steps left in this VIP plan
    if (isset($vip_steps[$vip][$current_step_idx])) {
        $new_balance = $vip_steps[$vip][$current_step_idx];
        
        // Update user balance to the new step amount
        mysqli_query($conn, "UPDATE users SET balance = $new_balance WHERE id = $uid");
        
        // Move to the next step index and update the timestamp
        $next_step = $current_step_idx + 1;
        
        // If this was the final step, mark investment as completed
        $new_status = ($next_step >= count($vip_steps[$vip])) ? 'completed' : 'active';
        
        mysqli_query($conn, "UPDATE investments SET 
            current_step = $next_step, 
            last_growth = CURRENT_TIMESTAMP, 
            status = '$new_status' 
            WHERE id = $inv_id");
            
        echo "User $uid moved to Step $next_step (₦$new_balance)<br>";
    }
}
?>
