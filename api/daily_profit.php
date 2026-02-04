<?php
include 'db.php';

// This script finds all active investments and adds profit
$query = "SELECT * FROM investments WHERE status = 'active'";
$result = mysqli_query($conn, $query);

while ($row = mysqli_fetch_assoc($result)) {
    $user_id = $row['user_id'];
    $vip_level = $row['vip_level'];
    
    // Define daily profits for each VIP
    $profit = 0;
    if ($vip_level == 1) $profit = 200; // VIP 1 earns 200 daily
    if ($vip_level == 2) $profit = 600; // VIP 2 earns 600 daily
    
    // Update user balance
    mysqli_query($conn, "UPDATE users SET balance = balance + $profit WHERE id = '$user_id'");
}

echo "Daily profits processed successfully!";
?>
