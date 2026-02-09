<?php
// Function to get percentages and ROI based on VIP Tier
function getPlanData($vip_level) {
    $plans = [
        1 => ["roi" => 2.1, "ref1" => 0.15, "ref2" => 0.05], // 15% L1, 5% L2
        2 => ["roi" => 2.3, "ref1" => 0.08, "ref2" => 0.03], // 8% L1, 3% L2
        3 => ["roi" => 2.5, "ref1" => 0.06, "ref2" => 0.02], // 6% L1, 2% L2
        4 => ["roi" => 2.7, "ref1" => 0.03, "ref2" => 0.01], // 3% L1, 1% L2
        5 => ["roi" => 3.0, "ref1" => 0.015, "ref2" => 0.005] // 1.5% L1, 0.5% L2
    ];
    return $plans[$vip_level] ?? null;
}

// Comprehensive Referral Payment Function
function processTeamCommissions($investor_id, $amount, $vip_level, $conn) {
    $plan = getPlanData($vip_level);
    if (!$plan) return false;

    // 1. Fetch the Direct Upline (Level 1)
    $res = mysqli_query($conn, "SELECT referred_by FROM users WHERE id = '$investor_id'");
    $user = mysqli_fetch_assoc($res);
    $l1_id = $user['referred_by'] ?? null;

    if ($l1_id) {
        $comm1 = $amount * $plan['ref1'];
        mysqli_query($conn, "UPDATE users SET balance = balance + $comm1 WHERE id = '$l1_id'");
        
        // Log the commission for transparency
        mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                             VALUES ('$l1_id', '$comm1', 'referral', 'L1 Commission from User #$investor_id')");

        // 2. Fetch the Indirect Upline (Level 2)
        $res2 = mysqli_query($conn, "SELECT referred_by FROM users WHERE id = '$l1_id'");
        $upline1 = mysqli_fetch_assoc($res2);
        $l2_id = $upline1['referred_by'] ?? null;

        if ($l2_id) {
            $comm2 = $amount * $plan['ref2'];
            mysqli_query($conn, "UPDATE users SET balance = balance + $comm2 WHERE id = '$l2_id'");
            
            mysqli_query($conn, "INSERT INTO transactions (user_id, amount, type, description) 
                                 VALUES ('$l2_id', '$comm2', 'referral', 'L2 Commission from User #$investor_id')");
        }
    }
    return true;
}
?>
