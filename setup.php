<?php
include 'api/db.php';

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

if (mysqli_multi_query($conn, $sql)) {
    echo "<h1>✅ SUCCESS! Full Tables Created.</h1><p>Username, Referral, and VIP structures are now live.</p>";
} else {
    echo "<h1>❌ Error:</h1> " . mysqli_error($conn);
}
?>
