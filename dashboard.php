<?php
session_start();
include 'api/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$uid = $_SESSION['user_id'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$uid'"));

// Logic: Withdrawal only if they have an active investment
$inv_check = mysqli_query($conn, "SELECT id FROM investments WHERE user_id = '$uid' AND status = 'active' LIMIT 1");
$can_withdraw = mysqli_num_rows($inv_check) > 0;

// Ella's VIP Tier Data
$plans = [
    'VIP 1 (30 Days Duration)' => [
        ['id' => 1, 'price' => 3000, 'daily' => 400, 'total' => 12000, 'days' => 30],
        ['id' => 2, 'price' => 9000, 'daily' => 500, 'total' => 15000, 'days' => 30],
        ['id' => 3, 'price' => 15000, 'daily' => 650, 'total' => 19500, 'days' => 30],
        ['id' => 4, 'price' => 21000, 'daily' => 1000, 'total' => 30000, 'days' => 30],
        ['id' => 5, 'price' => 51000, 'daily' => 2000, 'total' => 60000, 'days' => 30],
    ],
    'VIP 2 (40 Days Duration)' => [
        ['id' => 6, 'price' => 80000, 'daily' => 2500, 'total' => 100000, 'days' => 40],
        ['id' => 7, 'price' => 100000, 'daily' => 3000, 'total' => 120000, 'days' => 40],
        ['id' => 8, 'price' => 150000, 'daily' => 4250, 'total' => 170000, 'days' => 40],
        ['id' => 9, 'price' => 250000, 'daily' => 6750, 'total' => 270000, 'days' => 40],
    ],
    'VIP 3 (40 Days Duration)' => [
        ['id' => 10, 'price' => 500000, 'daily' => 13000, 'total' => 520000, 'days' => 40],
        ['id' => 11, 'price' => 800000, 'daily' => 22500, 'total' => 900000, 'days' => 40],
        ['id' => 12, 'price' => 1000000, 'daily' => 30000, 'total' => 1200000, 'days' => 40],
    ]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Luxe Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --main-blue: #007bff; --bg-grey: #f0f2f5; }
        body { background: var(--bg-grey); font-family: sans-serif; padding-bottom: 90px; }
        .header-section { background: #3b82f6; color: white; padding: 30px 15px 60px; text-align: center; border-radius: 0 0 25px 25px; }
        .balance-card { background: white; color: #333; border-radius: 20px; padding: 25px; margin: -40px 15px 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; }
        .action-row { display: flex; gap: 10px; padding: 0 15px 20px; }
        .btn-action { background: #3b82f6; color: white; flex: 1; border: none; padding: 12px; border-radius: 10px; font-weight: bold; text-decoration: none; text-align: center; font-size: 14px; }
        .support-links { background: #fff; margin: 0 15px 20px; padding: 15px; border-radius: 15px; border-left: 5px solid #25d366; }
        .product-card { background: white; border-radius: 15px; overflow: hidden; border: none; box-shadow: 0 2px 8px rgba(0,0,0,0.05); margin-bottom: 15px; }
        .product-img { background: #800020; color: white; padding: 10px; text-align: center; font-size: 11px; font-weight: bold; }
        .btn-buy { background: #007bff; color: white; border-radius: 20px; width: 100%; font-weight: bold; border: none; padding: 8px; margin-top: 10px; }
        .category-title { color: #001f3f; font-weight: 800; padding: 10px 15px; margin-top: 10px; border-left: 4px solid #3b82f6; }
        .bottom-nav { background: white; position: fixed; bottom: 0; width: 100%; display: flex; justify-content: space-around; padding: 12px 0; border-top: 1px solid #ddd; z-index: 100; }
        .nav-link { color: #888; text-decoration: none; font-size: 12px; text-align: center; }
        .nav-link.active { color: #3b82f6; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header-section">
        <h5 class="fw-bold m-0">PRIME LUXE</h5>
        <p class="small opacity-75">Welcome, <?php echo $u['phone']; ?></p>
    </div>

    <div class="balance-card">
        <p class="text-muted small mb-1">Available Balance</p>
        <h2 class="fw-bold">₦<?php echo number_format($u['balance'], 2); ?></h2>
    </div>

    <div class="action-row">
        <a href="deposit.php" class="btn-action">Deposit</a>
        <a href="<?php echo $can_withdraw ? 'withdraw.php' : '#'; ?>" 
           class="btn-action" 
           onclick="<?php echo !$can_withdraw ? "alert('You must have an active investment to withdraw'); return false;" : ""; ?>">
           Withdraw
        </a>
    </div>

    <div class="support-links">
        <div class="small fw-bold text-success mb-1"><i class="bi bi-whatsapp"></i> Support Channel</div>
        <a href="https://chat.whatsapp.com/LLGhNA8L0HfDNmU34S8CDt?mode=gi_t" class="btn btn-sm btn-outline-success rounded-pill px-3 me-2">WhatsApp Group</a>
        <a href="https://wa.me/2348077502802" class="btn btn-sm btn-success rounded-pill px-3">Contact Support</a>
    </div>

    <div class="container">
        <?php foreach($plans as $cat => $items): ?>
            <div class="category-title mb-3"><?php echo $cat; ?></div>
            <div class="row g-3">
                <?php foreach($items as $p): ?>
                <div class="col-6">
                    <div class="product-card">
                        <div class="product-img">PRIME LUXE INVESTMENT</div>
                        <div class="p-3">
                            <div class="small text-muted">Price: <b class="text-dark">₦<?php echo number_format($p['price']); ?></b></div>
                            <div class="small text-muted">Daily: <b class="text-success">₦<?php echo number_format($p['daily']); ?></b></div>
                            <div class="small text-muted">Term: <b class="text-danger"><?php echo $p['days']; ?> Days</b></div>
                            <div class="small text-muted">Total: <b class="text-primary">₦<?php echo number_format($p['total']); ?></b></div>
                            <form action="api/buy_vip.php" method="POST">
                                <input type="hidden" name="vip_id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn-buy shadow-sm">INVEST</button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="bottom-nav">
        <a href="dashboard.php" class="nav-link active"><i class="bi bi-house-door fs-4"></i><br>Home</a>
        <a href="orders.php" class="nav-link"><i class="bi bi-wallet2 fs-4"></i><br>Orders</a>
        <a href="team.php" class="nav-link"><i class="bi bi-people fs-4"></i><br>Team</a>
        <a href="settings.php" class="nav-link"><i class="bi bi-person-circle fs-4"></i><br>Profile</a>
    </div>

</body>
</html>
