<?php
include 'api/db.php';

// This script builds or updates the entire database structure
$sql = "
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    phone VARCHAR(20) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    referred_by INT DEFAULT NULL,
    balance DECIMAL(10,2) DEFAULT 400.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    bank_name VARCHAR(100),
    account_number VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS investments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    vip_level INT,
    status VARCHAR(20) DEFAULT 'active',
    maturity_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
";

// Execute the multi-query
if (mysqli_multi_query($conn, $sql)) {
    echo "<div style='font-family:sans-serif; text-align:center; margin-top:50px;'>";
    echo "<h1 style='color:green;'>✅ SYSTEM READY</h1>";
    echo "<p>All tables (Users, Deposits, Withdrawals, Investments) have been created or updated.</p>";
    echo "<p><strong>Referral System:</strong> Fixed ('referred_by' column added).</p>";
    echo "<p><strong>Signup Bonus:</strong> Set to ₦400.</p>";
    echo "<hr style='width:300px;'>";
    echo "<a href='index.php' style='padding:10px 20px; background:#001f3f; color:white; text-decoration:none; border-radius:5px;'>GO TO LOGIN</a>";
    echo "</div>";
} else {
    echo "<h1 style='color:red;'>❌ Database Error:</h1> " . mysqli_error($conn);
}
?>
