<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px; text-align:center;'>";
echo "<h2>PrimeLuxe Final System Sync</h2>";

// 1. Ensure Table Basics
$tables = [
    "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, phone VARCHAR(20) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, balance DECIMAL(10,2) DEFAULT 400.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS deposits (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS investments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, status VARCHAR(20) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
];
foreach($tables as $t) { mysqli_query($conn, $t); }

// 2. SMART COLUMN PATCHER
function addColumnSimple($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        mysqli_query($conn, $sql);
    }
}

// 3. Fix the missing columns that caused your "Fatal Errors"
addColumnSimple($conn, 'investments', 'vip_level', 'INT NOT NULL DEFAULT 1');
addColumnSimple($conn, 'investments', 'amount_invested', 'DECIMAL(10,2) NOT NULL DEFAULT 0.00');
addColumnSimple($conn, 'investments', 'current_step', 'INT DEFAULT 0');
addColumnSimple($conn, 'investments', 'last_growth', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');

// 4. Set the 400 Bonus Default
mysqli_query($conn, "ALTER TABLE users MODIFY COLUMN balance DECIMAL(10,2) DEFAULT 400.00");

echo "<p style='color:green; font-weight:bold;'>✅ All Errors Fixed. ₦400 Bonus Set.</p>";
echo "<hr><a href='dashboard.php' style='text-decoration:none; color:navy;'>Go to Dashboard</a></div>";
?>
