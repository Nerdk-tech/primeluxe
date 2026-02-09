<?php
include 'api/db.php';

echo "<h3>Repairing Database...</h3>";

// 1. Function to safely add a column if it doesn't exist
function addColumn($conn, $table, $column, $definition) {
    $check = mysqli_query($conn, "SHOW COLUMNS FROM `$table` LIKE '$column'");
    if (mysqli_num_rows($check) == 0) {
        $sql = "ALTER TABLE `$table` ADD `$column` $definition";
        if (mysqli_query($conn, $sql)) {
            echo "✅ Column '$column' added to '$table'.<br>";
        } else {
            echo "❌ Error adding '$column': " . mysqli_error($conn) . "<br>";
        }
    } else {
        echo "ℹ️ Column '$column' already exists in '$table'.<br>";
    }
}

// 2. Execute the repairs for the investments table
addColumn($conn, 'investments', 'daily_income', "DECIMAL(15,2) AFTER amount_invested");
addColumn($conn, 'investments', 'max_steps', "INT AFTER daily_income");

// 3. Execute repairs for the users table (Bank Details)
addColumn($conn, 'users', 'bank_name', "VARCHAR(100) DEFAULT NULL");
addColumn($conn, 'users', 'account_number', "VARCHAR(20) DEFAULT NULL");
addColumn($conn, 'users', 'account_name', "VARCHAR(100) DEFAULT NULL");

echo "<br><b>Repair Complete!</b> <a href='dashboard.php'>Return to Dashboard</a>";
?>
