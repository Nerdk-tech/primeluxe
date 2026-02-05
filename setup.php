<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px; text-align:center;'>";
echo "<h2>PrimeLuxe System Setup</h2>";

// 1. Create/Update Tables
$tables = [
    "CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, phone VARCHAR(20) UNIQUE NOT NULL, password VARCHAR(255) NOT NULL, balance DECIMAL(10,2) DEFAULT 400.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS deposits (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS withdrawals (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, amount DECIMAL(10,2), bank_name VARCHAR(100), account_number VARCHAR(20), status VARCHAR(20) DEFAULT 'pending', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
    "CREATE TABLE IF NOT EXISTS investments (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT, vip_level INT, amount_invested DECIMAL(10,2), current_step INT DEFAULT 0, last_growth TIMESTAMP DEFAULT CURRENT_TIMESTAMP, status VARCHAR(20) DEFAULT 'active', created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)"
];

foreach($tables as $t) { mysqli_query($conn, $t); }

// 2. Fix the "Unknown Column" Errors (Patches)
mysqli_query($conn, "ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(50) AFTER id");
mysqli_query($conn, "ALTER TABLE users ADD COLUMN IF NOT EXISTS referred_by INT DEFAULT NULL AFTER password");

// THIS FIXES THE CRON ERROR:
mysqli_query($conn, "ALTER TABLE investments ADD COLUMN IF NOT EXISTS current_step INT DEFAULT 0 AFTER amount_invested");
mysqli_query($conn, "ALTER TABLE investments ADD COLUMN IF NOT EXISTS last_growth TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER current_step");

echo "<p style='color:green;'>✅ All Database Columns Patched & Fixed!</p>";
echo "<hr>";
echo "<a href='dashboard.php' style='display:inline-block; padding:12px 25px; background:#001f3f; color:#D4AF37; text-decoration:none; font-weight:bold; border-radius:5px;'>BACK TO HOME</a>";
echo "</div>";
?>
