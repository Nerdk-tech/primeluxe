<?php
include 'api/db.php';

echo "<div style='font-family:sans-serif; max-width:500px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px;'>";
echo "<h2 style='text-align:center;'>PrimeLuxe System Setup</h2>";

// 1. Create the Users table first
$usersTable = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    referred_by INT DEFAULT NULL,
    balance DECIMAL(10,2) DEFAULT 400.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if(mysqli_query($conn, $usersTable)) {
    echo "<p style='color:green;'>✅ Users table ready.</p>";
}

// 2. Individual patches for existing tables (Standard SQL)
// We use @ to suppress errors if the column already exists
mysqli_query($conn, "ALTER TABLE users ADD COLUMN username VARCHAR(50) AFTER id");
mysqli_query($conn, "ALTER TABLE users ADD COLUMN referred_by INT DEFAULT NULL AFTER password");
mysqli_query($conn, "ALTER TABLE users MODIFY COLUMN balance DECIMAL(10,2) DEFAULT 400.00");

echo "<p style='color:green;'>✅ Database columns patched.</p>";

// 3. Create Deposits table
$depTable = "
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $depTable);

// 4. Create Withdrawals table
$withTable = "
CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    bank_name VARCHAR(100),
    account_number VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
mysqli_query($conn, $withTable);

echo "<p style='color:green;'>✅ Financial tables ready.</p>";
echo "<hr><div style='text-align:center;'>";
echo "<p><strong>Project:</strong> PrimeLuxe v1.0 Live</p>";
echo "<a href='index.php' style='display:inline-block; padding:10px 20px; background:#001f3f; color:#D4AF37; text-decoration:none; font-weight:bold; border-radius:5px;'>LAUNCH SITE</a>";
echo "</div></div>";
?>
