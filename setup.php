<?php
include 'api/db.php';

echo "
<div style='font-family:sans-serif; max-width:520px; margin:50px auto; padding:22px; border:2px solid #d4af37; border-radius:14px; text-align:center; background:#0b0b0b; color:#fff;'>
  <h2 style='color:#d4af37; margin-bottom:10px;'>PrimeLuxe System Setup</h2>
  <p style='opacity:.7; font-size:13px;'>Initializing core database structure...</p>
";

mysqli_query($conn, "SET NAMES utf8mb4");

/* USERS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  phone VARCHAR(20) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  balance DECIMAL(10,2) DEFAULT 400.00,
  last_login_reward DATE DEFAULT '2000-01-01',
  referred_by INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* INVESTMENTS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS investments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  vip_level INT NOT NULL,
  amount_invested DECIMAL(10,2) NOT NULL,
  daily_income DECIMAL(10,2) NOT NULL,
  current_step INT DEFAULT 0,
  max_steps INT NOT NULL,
  status VARCHAR(20) DEFAULT 'active',
  last_growth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* FORCE ₦400 DEFAULT BALANCE */
mysqli_query($conn, "ALTER TABLE users MODIFY balance DECIMAL(10,2) DEFAULT 400.00");

/* ENSURE last_login_reward COLUMN EXISTS */
$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'last_login_reward'");
if(mysqli_num_rows($check) == 0){
    mysqli_query($conn, "ALTER TABLE users ADD last_login_reward DATE DEFAULT '2000-01-01'");
}

echo "
  <p style='color:#4caf50; font-weight:bold; margin-top:12px;'>✅ System Ready — ₦400 Signup Bonus Active</p>
</div>
";
?>