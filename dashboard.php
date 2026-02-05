<?php
session_start();
include 'api/db.php';
include 'api/cron_growth.php'; // Runs growth check on every page load
if(!isset($_SESSION['user_id'])) header("Location: index.php");
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; padding-bottom: 90px; }
        .top-balance { background: var(--navy); color: white; padding: 30px 20px; border-bottom: 4px solid var(--gold); border-radius: 0 0 30px 30px; }
        .action-icon { text-align: center; text-decoration: none; color: #333; font-size: 12px; font-weight: bold; }
        .action-icon img { width: 45px; margin-bottom: 5px; display: block; margin-left: auto; margin-right: auto; }
        .vip-row { background: white; border-radius: 15px; margin-bottom: 15px; border: 1px solid #ddd; overflow: hidden; }
        .vip-header { background: var(--navy); color: var(--gold); padding: 10px 15px; font-weight: bold; display: flex; justify-content: space-between; }
        .step-box { font-size: 11px; color: #666; background: #fff9e6; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>

    <div class="top-balance text-center shadow">
        <h2 class="fw-bold mb-1">₦<?php echo number_format($u['balance'], 2); ?></h2>
        <div class="d-flex justify-content-center gap-2 mt-3">
            <a href="deposit.php" class="btn btn-warning fw-bold px-4">RECHARGE</a>
            <a href="withdraw.php" class="btn btn-outline-light fw-bold px-4">WITHDRAW</a>
        </div>
    </div>

    <div class="container mt-4">
        <div class="alert alert-info py-2 d-flex align-items-center justify-content-between shadow-sm" style="border-radius: 12px;">
            <span class="small fw-bold">📢 Join Official Community:</span>
            <a href="https://chat.whatsapp.com/YOUR_GROUP_LINK" class="btn btn-success btn-sm fw-bold">WhatsApp Group</a>
        </div>

        <div class="d-flex justify-content-around my-4">
            <a href="deposit.php" class="action-icon">
                <img src="https://img.icons8.com/color/96/wallet.png"> Recharge
            </a>
            <a href="team.php" class="action-icon">
                <img src="https://img.icons8.com/color/96/group.png"> My Team
            </a>
            <a href="https://wa.me/2348077502802" class="action-icon">
                <img src="https://img.icons8.com/color/96/whatsapp.png"> Support
            </a>
        </div>

        <h6 class="fw-bold text-muted mb-3">VIP INVESTMENT PLANS</h6>

        <?php
        $plans = [
            1 => ['name' => 'VIP 1', 'price' => 3000, 'steps' => '3k ➔ 7.2k ➔ 11.5k ➔ 15.7k ➔ 20k'],
            2 => ['name' => 'VIP 2', 'price' => 23000, 'steps' => '23k ➔ 29.7k ➔ 36.5k ➔ 43.2k ➔ 50k'],
            3 => ['name' => 'VIP 3', 'price' => 53000, 'steps' => '53k ➔ 58.4k ➔ 63.8k ➔ 69.2k ➔ 74.6k ➔ 80k'],
            4 => ['name' => 'VIP 4', 'price' => 83000, 'steps' => '83k ➔ 88.4k ➔ 93.8k ➔ 99.2k ➔ 104.6k ➔ 110k'],
            5 => ['name' => 'VIP 5', 'price' => 113000, 'steps' => '113k ➔ 120.4k ➔ 127.8k ➔ 135.2k ➔ 142.6k ➔ 150k']
        ];

        foreach($plans as $id => $p): ?>
        <div class="vip-row shadow-sm">
            <div class="vip-header">
                <span><?php echo $p['name']; ?></span>
                <span>₦<?php echo number_format($p['price']); ?></span>
            </div>
            <div class="p-3">
                <div class="step-box mb-3"><strong>Daily Growth Path:</strong> <?php echo $p['steps']; ?></div>
                <form action="api/buy_vip.php" method="POST">
                    <input type="hidden" name="vip_id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">INVEST NOW</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="fixed-bottom bg-white border-top d-flex justify-content-around py-2 shadow">
        <a href="dashboard.php" class="text-center text-decoration-none text-dark small fw-bold"><img src="https://img.icons8.com/material-outlined/24/001f3f/home--v1.png"><br>Home</a>
        <a href="team.php" class="text-center text-decoration-none text-muted small"><img src="https://img.icons8.com/material-outlined/24/cccccc/conference-call.png"><br>Team</a>
        <a href="settings.php" class="text-center text-decoration-none text-muted small"><img src="https://img.icons8.com/material-outlined/24/cccccc/user-male-circle.png"><br>Mine</a>
    </div>

</body>
</html>
