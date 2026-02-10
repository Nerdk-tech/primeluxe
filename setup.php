<?php
include 'api/db.php';

echo "
<div style='font-family:sans-serif; max-width:520px; margin:50px auto; padding:22px; border:2px solid #d4af37; border-radius:14px; text-align:center; background:#0b0b0b; color:#fff;'>
  <h2 style='color:#d4af37; margin-bottom:10px;'>PrimeLuxe System Setup</h2>
  <p style='opacity:.7; font-size:13px;'>Finalizing tables and relational integrity...</p>
";

mysqli_query($conn, "SET NAMES utf8mb4");

/* 1. USERS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) DEFAULT 'Member',
  phone VARCHAR(20) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  balance DECIMAL(15,2) DEFAULT 400.00,
  bank_name VARCHAR(100) DEFAULT NULL,
  account_number VARCHAR(20) DEFAULT NULL,
  account_name VARCHAR(100) DEFAULT NULL,
  last_login_reward DATE DEFAULT '2000-01-01',
  referred_by INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* 2. INVESTMENTS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS investments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  vip_level INT NOT NULL,
  amount_invested DECIMAL(15,2) NOT NULL,
  daily_income DECIMAL(15,2) NOT NULL,
  current_step INT DEFAULT 0,
  max_steps INT NOT NULL,
  status VARCHAR(20) DEFAULT 'active',
  last_growth TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* 3. DEPOSITS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS deposits (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  sender_name VARCHAR(100),
  status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* FIX: Adding missing columns to Deposits table individually to avoid syntax errors */
mysqli_query($conn, "ALTER TABLE deposits ADD COLUMN bank_name VARCHAR(100) AFTER amount");
mysqli_query($conn, "ALTER TABLE deposits ADD COLUMN account_number VARCHAR(20) AFTER bank_name");
mysqli_query($conn, "ALTER TABLE deposits ADD COLUMN account_name VARCHAR(100) AFTER account_number");

/* 4. WITHDRAWALS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS withdrawals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  bank_name VARCHAR(100),
  account_number VARCHAR(20),
  account_name VARCHAR(100),
  status ENUM('pending', 'completed', 'rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

/* 5. TRANSACTIONS TABLE */
mysqli_query($conn, "
CREATE TABLE IF NOT EXISTS transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  amount DECIMAL(15,2) NOT NULL,
  type ENUM('credit', 'debit', 'referral', 'signup_bonus') NOT NULL,
  description VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

echo "
  <p style='color:#4caf50; font-weight:bold; margin-top:12px;'>✅ Database Fully Synced</p>
  <p style='font-size:11px; opacity:0.6;'>Users, Investments, Deposits, Withdrawals, and Transactions tables are ready.</p>
  <a href='index.php' style='display:inline-block; margin-top:15px; padding:10px 20px; background:#d4af37; color:#000; text-decoration:none; border-radius:8px; font-weight:bold;'>GO TO LOGIN</a>
</div>
";
?>
