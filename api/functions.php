<?php
// Function to get percentages and ROI based on VIP Tier
function getPlanData($vip_level) {
    $plans = [
        1 => ["roi" => 2.1, "ref" => 0.15],   // VIP 1: 15% ref
        2 => ["roi" => 2.3, "ref" => 0.08],   // VIP 2: 8% ref
        3 => ["roi" => 2.5, "ref" => 0.06],   // VIP 3: 6% ref
        4 => ["roi" => 2.7, "ref" => 0.03],   // VIP 4: 3% ref
        5 => ["roi" => 3.0, "ref" => 0.015]  // VIP 5: 1.5% ref
    ];
    return $plans[$vip_level] ?? null;
}

// Function to pay the person who referred the investor
function payUpline($upline_id, $amount, $vip_level, $conn) {
    $plan = getPlanData($vip_level);
    $commission = $amount * $plan['ref'];
    $sql = "UPDATE users SET balance = balance + $commission WHERE id = '$upline_id'";
    return mysqli_query($conn, $sql);
}
?>
