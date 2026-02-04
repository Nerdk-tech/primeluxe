<?php
session_start();
include 'api/db.php';
$u_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$u_id'"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: #001f3f; color: white;">
    <div class="container mt-5 p-4 bg-white text-dark rounded shadow" style="max-width: 400px;">
        <h4 class="fw-bold">Account Settings</h4>
        <hr>
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Phone:</strong> <?php echo $user['phone']; ?></p>
        <p><strong>Wallet:</strong> ₦<?php echo number_format($user['balance'], 2); ?></p>
        <a href="dashboard.php" class="btn btn-dark w-100">Back</a>
    </div>
</body>
</html>
