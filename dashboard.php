<?php
session_start();
include 'api/db.php';
include 'api/cron_growth.php'; 

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; padding-bottom: 100px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .top-balance { background: var(--navy); color: white; padding: 35px 20px; border-bottom: 5px solid var(--gold); border-radius: 0 0 35px 35px; }
        
        /* Main Action Grid */
        .action-card { background: white; border-radius: 20px; padding: 20px 10px; margin-top: -30px; }
        .action-icon { text-align: center; text-decoration: none; color: #333; font-size: 11px; font-weight: 700; width: 23%; }
        .action-icon img { width: 42px; margin-bottom: 8px; transition: 0.3s; }
        .action-icon:active img { transform: scale(0.9); }

        .vip-row { background: white; border-radius: 18px; margin-bottom: 18px; border: 1px solid #eee; overflow: hidden; }
        .vip-header { background: var(--navy); color: var(--gold); padding: 12px 18px; font-weight: bold; display: flex; justify-content: space-between; align-items: center; }
        .step-box { font-size: 11px; color: #555; background: #fffcf0; padding: 12px; border-radius: 8px; border-left: 3px solid var(--gold); }
        
        /* Navigation */
        .bottom-nav { background: white; border-top: 1px solid #eee; height: 75px; }
        .nav-item { text-decoration: none; color: #bbb; font-size: 11px; font-weight: 600; text-align: center; }
        .nav-item.active { color: var(--navy); }
        .nav-item img { width: 24px; display: block; margin: 0 auto 3px; }
    </style>
</head>
<body>

    <div class="top-balance text-center shadow-lg">
        <p class="small opacity-75 mb-1">Current Balance</p>
        <h1 class="fw-bold mb-3">₦<?php echo number_format($u['balance'], 2); ?></h1>
        <div class="d-flex justify-content-center gap-3">
            <a href="deposit.php" class="btn btn-warning fw-bold px-4 rounded-pill shadow">RECHARGE</a>
            <a href="withdraw.php" class="btn btn-outline-light fw-bold px-4 rounded-pill">WITHDRAW</a>
        </div>
    </div>

    <div class="container mt-2">
        <div class="action-card shadow-sm d-flex justify-content-between mb-4">
            <a href="deposit.php" class="action-icon">
                <img src="https://img.icons8.com/color/96/bank-cards.png"><br>Deposit
            </a>
            <a href="team.php" class="action-icon">
                <img src="https://img.icons8.com/color/96/conference-call.png"><br>Team
            </a>
            <a href="withdraw_history.php" class="action-icon">
                <img src="https://img.icons8.com/color/96/list.png"><br>Logs
            </a>
            <a href="https://wa.me/2348077502802" class="action-icon">
                <img src="https://img.icons8.com/color/96/whatsapp.png"><br>Support
            </a>
        </div>

        <div class="alert alert-warning py-2 px-3 d-flex align-items-center justify-content-between shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="small fw-bold text-dark">📢 Join Our Community</div>
            <a href="https://chat.whatsapp.com/LLGhNA8L0HfDNmU34S8CDt?mode=gi_t" class="btn btn-success btn-sm fw-bold rounded-pill px-3">JOIN NOW</a>
        </div>

        <h6 class="fw-bold text-muted mb-3 px-1 text-uppercase small">Investment Packages</h6>

        <?php
        $plans = [
            1 => ['name' => 'VIP 1', 'price' => 3000, 'steps' => '3k ➔ 7.2k ➔ 11.5k ➔ 15.7k ➔ 20k'],
            2 => ['name' => 'VIP 2', 'price' => 23000, 'steps' => '23k ➔ 29.7k ➔ 36.5k ➔ 43.2k ➔ 50k'],
            3 => ['name' => 'VIP 3', 'price' => 53000, 'steps' => '53k ➔ 80k Profit Goal'],
            4 => ['name' => 'VIP 4', 'price' => 83000, 'steps' => '83k ➔ 110k Profit Goal'],
            5 => ['name' => 'VIP 5', 'price' => 113000, 'steps' => '113k ➔ 150k Profit Goal']
        ];

        foreach($plans as $id => $p): ?>
        <div class="vip-row shadow-sm">
            <div class="vip-header">
                <span><?php echo $p['name']; ?></span>
                <span>₦<?php echo number_format($p['price']); ?></span>
            </div>
            <div class="p-3">
                <div class="step-box mb-3"><strong>Growth:</strong> <?php echo $p['steps']; ?></div>
                <form action="api/buy_vip.php" method="POST">
                    <input type="hidden" name="vip_id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-warning w-100 fw-bold rounded-pill py-2 shadow-sm">INVEST NOW</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="fixed-bottom bottom-nav d-flex justify-content-around align-items-center shadow-lg">
        <a href="dashboard.php" class="nav-item active">
            <img src="https://img.icons8.com/material-rounded/24/001f3f/home.png">Home
        </a>
        <a href="orders.php" class="nav-item">
            <img src="https://img.icons8.com/material-outlined/24/cccccc/clipboard.png">Orders
        </a>
        <a href="team.php" class="nav-item">
            <img src="https://img.icons8.com/material-outlined/24/cccccc/conference-call.png">Team
        </a>
        <a href="settings.php" class="nav-item">
            <img src="https://img.icons8.com/material-outlined/24/cccccc/user-male-circle.png">Mine
        </a>
    </div>

</body>
</html>
