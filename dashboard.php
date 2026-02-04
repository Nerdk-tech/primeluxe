<?php
session_start();
include 'api/db.php';
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id']; 
$query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);
$current_balance = $user['balance'] ?? "0.00"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --luxe-gold: #D4AF37; --luxe-navy: #001f3f; }
        body { background: #f8f9fa; font-family: 'Inter', sans-serif; padding-bottom: 80px; }
        .top-nav { background: var(--luxe-navy); border-bottom: 3px solid var(--luxe-gold); color: white; padding: 15px; }
        .balance-box { background: linear-gradient(135deg, var(--luxe-navy), #003366); color: white; border-radius: 20px; border: 1.5px solid var(--luxe-gold); }
        .btn-luxe { background: var(--luxe-gold); color: #000; font-weight: 700; border: none; border-radius: 8px; text-decoration: none; }
        .vip-card { border: none; border-radius: 15px; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border: 1px solid #eee; }
        .action-icon { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        .fixed-bottom { background: #fff; border-top: 1px solid #ddd; }
        .nav-item-luxe { text-decoration: none; color: #666; font-size: 11px; font-weight: 600; }
        .nav-item-luxe.active { color: var(--luxe-navy); }
        .nav-item-luxe img { width: 24px; height: 24px; margin-bottom: 2px; }
    </style>
</head>
<body>

    <div class="top-nav text-center">
        <h5 class="m-0" style="color: var(--luxe-gold); letter-spacing: 2px; font-weight: bold;">PRIME LUXE</h5>
    </div>

    <div class="container mt-4">
        <div class="card balance-box p-4 text-center shadow-lg">
            <p class="small text-uppercase mb-1 opacity-75">Available Balance</p>
            <h1 class="display-6 fw-bold">₦<?php echo number_format($current_balance, 2); ?></h1>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <a href="deposit.php" class="btn btn-luxe px-4 py-2">DEPOSIT</a>
                <a href="withdraw.php" class="btn btn-outline-light px-4 py-2">WITHDRAW</a>
            </div>
        </div>

        <div class="row text-center mt-4 g-2">
            <div class="col-3">
                <a href="deposit.php" class="text-decoration-none text-dark">
                    <div class="action-icon bg-primary"><img src="https://img.icons8.com/ios-filled/25/ffffff/wallet.png"/></div>
                    <small class="d-block mt-2 fw-bold">Recharge</small>
                </a>
            </div>
            <div class="col-3">
                <a href="withdraw.php" class="text-decoration-none text-dark">
                    <div class="action-icon bg-danger"><img src="https://img.icons8.com/ios-filled/25/ffffff/money-box.png"/></div>
                    <small class="d-block mt-2 fw-bold">Withdraw</small>
                </a>
            </div>
            <div class="col-3">
                <a href="team.php" class="text-decoration-none text-dark">
                    <div class="action-icon bg-info"><img src="https://img.icons8.com/ios-filled/25/ffffff/user-group-man-man.png"/></div>
                    <small class="d-block mt-2 fw-bold">My Team</small>
                </a>
            </div>
            <div class="col-3">
                <a href="gift.php" class="text-decoration-none text-dark">
                    <div class="action-icon bg-success"><img src="https://img.icons8.com/ios-filled/25/ffffff/gift.png"/></div>
                    <small class="d-block mt-2 fw-bold">Daily Gift</small>
                </a>
            </div>
        </div>
        
        <h6 class="mt-5 fw-bold text-uppercase small text-muted">Investment Plans</h6>
        <div class="row g-3">
            <?php
            $plans = [
                ['level' => 1, 'price' => 3000, 'range' => '3k - 20k'],
                ['level' => 2, 'price' => 23000, 'range' => '23k - 50k'],
                ['level' => 3, 'price' => 53000, 'range' => '53k - 80k'],
                ['level' => 4, 'price' => 83000, 'range' => '83k - 110k'],
                ['level' => 5, 'price' => 113000, 'range' => '113k - 150k']
            ];

            foreach ($plans as $plan): ?>
            <div class="col-6">
                <div class="card vip-card p-3 text-center">
                    <span class="badge bg-dark mb-2">VIP <?php echo $plan['level']; ?></span>
                    <h5 class="fw-bold m-0">₦<?php echo number_format($plan['price']); ?></h5>
                    <p class="text-muted small mb-2"><?php echo $plan['range']; ?></p>
                    <div class="text-success small fw-bold mb-3">ROI: 2.1x / 7 Days</div>
                    <form action="api/invest.php" method="POST">
                        <input type="hidden" name="amount" value="<?php echo $plan['price']; ?>">
                        <input type="hidden" name="vip_level" value="<?php echo $plan['level']; ?>">
                        <button type="submit" name="invest" class="btn btn-luxe btn-sm w-100">INVEST NOW</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="fixed-bottom d-flex justify-content-around py-2 shadow-lg">
        <a href="dashboard.php" class="nav-item-luxe active text-center">
            <img src="https://img.icons8.com/material-outlined/24/001f3f/home--v1.png"/><br>Home
        </a>
        <a href="orders.php" class="nav-item-luxe text-center">
            <img src="https://img.icons8.com/material-outlined/24/cccccc/list.png"/><br>Orders
        </a>
        <a href="team.php" class="nav-item-luxe text-center">
            <img src="https://img.icons8.com/material-outlined/24/cccccc/conference-call.png"/><br>Team
        </a>
        <a href="settings.php" class="nav-item-luxe text-center">
            <img src="https://img.icons8.com/material-outlined/24/cccccc/user-male-circle.png"/><br>Mine
        </a>
    </div>

</body>
</html>
