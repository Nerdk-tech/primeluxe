<?php
session_start();
include 'api/db.php';
if(!isset($_SESSION['user_id'])) header("Location: index.php");
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --gold: #D4AF37; --navy: #001f3f; }
        body { background: #f8f9fa; padding-bottom: 80px; font-family: sans-serif; }
        .balance-box { background: linear-gradient(135deg, var(--navy), #003366); color: white; border-radius: 20px; border: 1.5px solid var(--gold); }
        .vip-card { background: #fff; border-radius: 15px; border: 1px solid #eee; text-align: center; padding: 15px; }
    </style>
</head>
<body>
    <div class="p-3 text-center text-white" style="background: var(--navy);">
        <h5 class="m-0 fw-bold" style="color: var(--gold);">PRIME LUXE</h5>
    </div>

    <div class="container mt-3">
        <div class="card balance-box p-4 text-center shadow">
            <p class="small mb-1 opacity-75">Wallet Balance</p>
            <h1 class="fw-bold">₦<?php echo number_format($u['balance'], 2); ?></h1>
            <div class="d-flex justify-content-center gap-2 mt-2">
                <a href="deposit.php" class="btn btn-warning btn-sm fw-bold">RECHARGE</a>
                <a href="withdraw.php" class="btn btn-outline-light btn-sm fw-bold">WITHDRAW</a>
            </div>
        </div>

        <div class="alert alert-info mt-3 py-2 small shadow-sm">
            <img src="https://img.icons8.com/color/20/whatsapp.png"> Join: <a href="https://chat.whatsapp.com/LLGhNA8L0HfDNmU34S8CDt" class="fw-bold">WhatsApp Group</a>
        </div>

        <div class="row text-center mt-3 g-2">
            <div class="col-4"><a href="deposit.php" class="text-decoration-none text-dark"><img src="https://img.icons8.com/ios-filled/30/007bff/wallet.png"><br><small class="fw-bold">Recharge</small></a></div>
            <div class="col-4"><a href="team.php" class="text-decoration-none text-dark"><img src="https://img.icons8.com/ios-filled/30/17a2b8/conference-call.png"><br><small class="fw-bold">My Team</small></a></div>
            <div class="col-4"><a href="https://wa.me/2348077502802" class="text-decoration-none text-dark"><img src="https://img.icons8.com/ios-filled/30/28a745/whatsapp.png"><br><small class="fw-bold">Support</small></a></div>
        </div>

        <h6 class="mt-4 fw-bold text-muted small">VIP PLANS</h6>
        <div class="row g-2">
            <?php $ps = [['1',3000],['2',23000],['3',53000],['4',83000],['5',113000]];
            foreach($ps as $p): ?>
            <div class="col-6">
                <div class="vip-card shadow-sm">
                    <span class="badge bg-dark mb-2">VIP <?=$p[0]?></span>
                    <h5 class="fw-bold mb-0">₦<?=number_format($p[1])?></h5>
                    <small class="text-success fw-bold">ROI: 2.1x</small>
                    <button class="btn btn-warning btn-sm w-100 mt-2 fw-bold">INVEST</button>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="fixed-bottom bg-white border-top d-flex justify-content-around py-2">
        <a href="dashboard.php" class="text-center text-decoration-none text-dark"><img src="https://img.icons8.com/material-outlined/24/001f3f/home--v1.png"><br><small>Home</small></a>
        <a href="team.php" class="text-center text-decoration-none text-muted"><img src="https://img.icons8.com/material-outlined/24/cccccc/conference-call.png"><br><small>Team</small></a>
        <a href="settings.php" class="text-center text-decoration-none text-muted"><img src="https://img.icons8.com/material-outlined/24/cccccc/user-male-circle.png"><br><small>Mine</small></a>
    </div>
</body>
</html>
