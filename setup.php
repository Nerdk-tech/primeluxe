<?php
include 'api/db.php';

$sql = "
-- 1. Add username if it's missing
ALTER TABLE users ADD COLUMN IF NOT EXISTS username VARCHAR(50) AFTER id;

-- 2. Add referred_by if it's missing (THIS FIXES YOUR ERROR)
ALTER TABLE users ADD COLUMN IF NOT EXISTS referred_by INT DEFAULT NULL AFTER password;

-- 3. Make sure balance defaults to 400
ALTER TABLE users MODIFY COLUMN balance DECIMAL(10,2) DEFAULT 400.00;

-- 4. Ensure other tables exist
CREATE TABLE IF NOT EXISTS deposits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS withdrawals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    amount DECIMAL(10,2),
    bank_name VARCHAR(100),
    account_number VARCHAR(20),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
";

if (mysqli_multi_query($conn, $sql)) {
    echo "<h1 style='color:green; text-align:center;'>✅ DATABASE PATCHED!</h1>";
    echo "<p style='text-align:center;'>The 'referred_by' column has been added. Your Team page will work now.</p>";
    echo "<div style='text-align:center;'><a href='team.php'>Go to My Team</a></div>";
} else {
    echo "<h1>❌ Error:</h1> " . mysqli_error($conn);
}
?>
