<?php
include 'api/db.php';
// Add missing columns to investments table
mysqli_query($conn, "ALTER TABLE investments ADD COLUMN IF NOT EXISTS daily_income DECIMAL(15,2) AFTER amount_invested");
mysqli_query($conn, "ALTER TABLE investments ADD COLUMN IF NOT EXISTS max_steps INT AFTER daily_income");
echo "Database Repair Complete. Refresh orders.php";
?>
