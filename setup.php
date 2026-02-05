<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px; text-align:center;'>";
echo "<h2>PrimeLuxe System Recovery</h2>";

// 1. Core Table Creation
$tables = [
    "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, phone VARCHAR(20) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, balance DECIMAL(10,2) DEFAULT 400.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS deposits (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS withdrawals (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), bank_name VARCHAR(100), account_number VARCHAR(20), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS investments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, vip_level INT, amount_invested DECIMAL(10,2), status VARCHAR(20) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
];

foreach($tables as $t) { mysqli_query($conn, $t); }

// 2. SMART COLUMN PATCHER (Fixed Syntax)
function addColumnSimple($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($check) == 0) {
        // FIXED: Using single quotes for the SQL query string
        $sql = "ALTER TABLE `$table` ADD COLUMN `$column` $definition";
        if(!mysqli_query($conn, $sql)) {
            echo "<p style='color:red;'>Error adding $column: " . mysqli_error($conn) . "</p>";
        }
    }
}

// --- APPLY PATCHES ---

// Fix Users
addColumnSimple($conn, 'users', 'username', 'VARCHAR(50)');
addColumnSimple($conn, 'users', 'referred_by', 'INT DEFAULT NULL');

// Fix Deposits (FIXED SYNTAX: Using single quotes for the default value)
addColumnSimple($conn, 'deposits', 'sender_name', "VARCHAR(255) DEFAULT 'Not Provided'");

// Fix Investments
addColumnSimple($conn, 'investments', 'current_step', 'INT DEFAULT 0');
addColumnSimple($conn, 'investments', 'last_growth', 'TIMESTAMP DEFAULT CURRENT_TIMESTAMP');

echo "<p style='color:green;'>✅ Database structure successfully synchronized!</p>";
echo "<hr>";
echo "<a href='dashboard.php' style='display:inline-block; padding:12px 25px; background:#001f3f; color:#D4AF37; text-decoration:none; font-weight:bold; border-radius:5px;'>GO TO DASHBOARD</a>";
echo "</div>";
?>
