<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; background:#0b0b0b; color:#fff; padding:20px; border-radius:10px; max-width:500px; margin:auto;'>";
echo "<h3 style='color:#d4af37;'>PrimeLuxe System Repair</h3>";

// 1. Function to safely add a column if it doesn't exist
function addColumn($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "ALTER TABLE `$table` ADD `$column` $definition";
        if (mysqli_query($conn, $sql)) {
            echo "✅ Column <b>'$column'</b> added to <b>'$table'</b>.<br>";
        } else {
            echo "❌ Error adding '$column': " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Column '$column' already exists in '$table'.<br>";
    }
}

// --- FIX INVESTMENTS TABLE ---
addColumn($conn, 'investments', 'daily_income', "DECIMAL(15,2) AFTER amount_invested");
addColumn($conn, 'investments', 'max_steps', "INT AFTER daily_income");

// --- FIX WITHDRAWALS TABLE (The crash fix) ---
addColumn($conn, 'withdrawals', 'bank_name', "VARCHAR(100) AFTER amount");
addColumn($conn, 'withdrawals', 'account_name', "VARCHAR(100) AFTER bank_name");
addColumn($conn, 'withdrawals', 'account_number', "VARCHAR(20) AFTER account_name");

// --- FIX USERS TABLE ---
addColumn($conn, 'users', 'bank_name', "VARCHAR(100) DEFAULT NULL");
addColumn($conn, 'users', 'account_number', "VARCHAR(20) DEFAULT NULL");
addColumn($conn, 'users', 'account_name', "VARCHAR(100) DEFAULT NULL");

echo "<hr style='border:1px solid #333;'>";

// 2. Logic Fix: Change 0/0 Days to actual limits
// Based on your cron steps: VIP 1&2 = 4 steps, others = 5 steps
mysqli_query($conn, "UPDATE investments SET max_steps = 4 WHERE vip_level IN (1,2) AND (max_steps = 0 OR max_steps IS NULL)");
mysqli_query($conn, "UPDATE investments SET max_steps = 5 WHERE vip_level IN (3,4,5) AND (max_steps = 0 OR max_steps IS NULL)");
echo "🛠️ Fixed '0/0 Days' duration issues.<br>";

echo "<br><b>Repair Complete!</b> <br><br> <a href='dashboard.php' style='background:#d4af37; color:#000; padding:10px; text-decoration:none; border-radius:5px;'>Return to Dashboard</a>";
echo "</div>";
?>
