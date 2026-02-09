<?php
/* PRIME LUXE - DATABASE CORE 
   Host: Aiven Cloud (SSL Enabled)
*/

$host = "prime-luxe-db-univversehackers-e9d1.g.aivencloud.com";
$port = "14162";
$user = "avnadmin";
$pass = "AVNS_3LAEpH1RXymVMM2inLf";
$dbname = "defaultdb";

// Initialize mysqli with SSL
$conn = mysqli_init();

// Force SSL encryption for cloud security
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

// Establish Connection
$connected = @mysqli_real_connect(
    $conn, 
    $host, 
    $user, 
    $pass, 
    $dbname, 
    $port, 
    NULL, 
    MYSQLI_CLIENT_SSL
);

if (!$connected) {
    // Log error internally, don't show specific DB details to the public
    error_log("Connection failed: " . mysqli_connect_error());
    die("Server is currently under maintenance. Please try again later.");
}

// Set charset to utf8mb4 for full emoji and special character support (important for usernames)
mysqli_set_charset($conn, "utf8mb4");

// Set the correct timezone for Nigeria (GMT+1)
date_default_timezone_set('Africa/Lagos');
?>
