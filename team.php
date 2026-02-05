<?php
session_start();
include 'api/db.php';
$u_id = $_SESSION['user_id'];
$refs = mysqli_query($conn, "SELECT phone FROM users WHERE referred_by = $u_id");
?>
<!DOCTYPE html>
<html>
<head><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light p-4">
    <h4 class="fw-bold">My Team</h4>
    <div class="card p-3 mb-3 text-center bg-navy" style="background:#001f3f; color:#D4AF37;">
        <small>Your Referral Link:</small>
        <p class="small">https://prime-luxe-lgu5.onrender.com/index.php?ref=<?=$u_id?></p>
    </div>
    <h6 class="fw-bold">My Referrals:</h6>
    <ul class="list-group shadow-sm">
        <?php while($r = mysqli_fetch_assoc($refs)) echo "<li class='list-group-item'>".$r['phone']."</li>"; ?>
    </ul>
    <a href="dashboard.php" class="btn btn-secondary w-100 mt-4">Back</a>
</body>
</html>
