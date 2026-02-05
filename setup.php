<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px; text-align:center;'>";
echo "<h2>PrimeLuxe System Setup</h2>";

// 1. Create Tables if they don't exist
$tables = [
    "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, phone VARCHAR(20) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, balance DECIMAL(10,2) DEFAULT 400.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS deposits (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS withdrawals (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), bank_name VARCHAR(100), account_number VARCHAR(20), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS investments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, vip_level INT, amount_invested DECIMAL(10,2), current_step INT DEFAULT 0, last_growth TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status VARCHAR(20) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
];

foreach($tables as $t) { mysqli_query($conn, $t); }

// 2. Safe Column Patches (Compatible with all MySQL versions)
function addColumn($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($check) == 0) {
        mysqli_query($conn, "ALTER TABLE `$table` ADD COLUMN `$column` $definition");
    }
}

// Fix Users table
addColumn($conn, 'users', 'username', 'VARCHAR(50) AFTER id');
addColumn($conn, 'users', 'referred_by', 'INT DEFAULT NULL AFTER password');

// Fix Investments table (The crash fix for last_growth)
addColumn($conn, 'investments', 'current_step', 'INT DEFAULT 0 AFTER amount_invested');
addColumn($conn, 'investments', 'last_growth', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER current_step');

echo "<p style='color:green;'>✅ All database errors fixed!</p>";
echo "<hr>";
echo "<a href='dashboard.php' style='display:inline-block; padding:12px 25px; background:#001f3f; color:#D4AF37; text-decoration:none; font-weight:bold; border-radius:5px;'>GO TO DASHBOARD</a>";
echo "</div>";
?>
