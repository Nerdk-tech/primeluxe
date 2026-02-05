<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px; text-align:center;'>";
echo "<h2>PrimeLuxe System Setup</h2>";

// 1. Create Tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, phone VARCHAR(20) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, balance DECIMAL(10,2) DEFAULT 400.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS deposits (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS withdrawals (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), bank_name VARCHAR(100), account_number VARCHAR(20), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS investments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, vip_level INT, amount_invested DECIMAL(10,2), current_step INT DEFAULT 0, last_growth TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status VARCHAR(20) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
];

foreach($tables as $t) { mysqli_query($conn, $t); }

// 2. Safe Column Patches (These won't cause "Duplicate" errors anymore)
$check_user = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'username'");
if(mysqli_num_rows($check_user) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN username VARCHAR(50) AFTER id");
}

$check_ref = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'referred_by'");
if(mysqli_num_rows($check_ref) == 0) {
    mysqli_query($conn, "ALTER TABLE users ADD COLUMN referred_by INT DEFAULT NULL AFTER password");
}

echo "<p style='color:green;'>✅ Database is up to date and synchronized.</p>";
echo "<hr>";
echo "<a href='dashboard.php' style='display:inline-block; padding:12px 25px; background:#001f3f; color:#D4AF37; text-decoration:none; font-weight:bold; border-radius:5px; margin-top:10px;'>BACK TO HOME</a>";
echo "</div>";
?>
