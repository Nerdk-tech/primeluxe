<?php
$host = "prime-luxe-db-univversehackers-e9d1.g.aivencloud.com";
$port = "14162";
$user = "avnadmin";
$pass = "AVNS_3LAEpH1RXymVMM2inLf";
$dbname = "defaultdb";

// Initialize mysqli with SSL
$conn = mysqli_init();

// Aiven uses a self-signed CA. We skip verification but force encryption.
mysqli_ssl_set($conn, NULL, NULL, NULL, NULL, NULL);

if (!mysqli_real_connect($conn, $host, $user, $pass, $dbname, $port, NULL, MYSQLI_CLIENT_SSL)) {
    die("Database Connection Failed: " . mysqli_connect_error());
}
?>
