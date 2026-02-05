<?php
session_start();
include 'api/db.php';
if(!isset($_SESSION['user_id'])) header("Location: index.php");
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'"));

// Get active investments for this user
$active_inv = mysqli_query($conn, "SELECT * FROM investments WHERE user_id = '".$u['id']."' AND status = 'active'");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; padding-bottom: 80px; }
        .vip-row { background: white; border-radius: 15px; margin-bottom: 15px; border: 1px solid #ddd; overflow: hidden; }
        .vip-header { background: var(--navy); color: var(--gold); padding: 10px 15px; font-weight: bold; display: flex; justify-content: space-between; }
        .step-text { font-size: 11px; color: #666; background: #fff9e6; padding: 8px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="p-3 text-center text-white" style="background: var(--navy); border-bottom: 3px solid var(--gold);">
        <h5 class="m-0 fw-bold">PRIME LUXE</h5>
    </div>

    <div class="container mt-3">
        <div class="card p-3 mb-4 shadow-sm border-0 text-white" style="background: linear-gradient(45deg, #001f3f, #004080); border-radius: 15px;">
            <small>Available Balance</small>
            <h2 class="fw-bold">₦<?php echo number_format($u['balance'], 2); ?></h2>
        </div>

        <?php if(mysqli_num_rows($active_inv) > 0): ?>
            <div class="alert alert-success small py-2">✅ You have an active investment growing!</div>
        <?php endif; ?>

        <h6 class="fw-bold text-muted mb-3">SELECT A PLAN</h6>

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
                <div class="step-text mb-3"><strong>Growth:</strong> <?php echo $p['steps']; ?></div>
                <form action="api/buy_vip.php" method="POST">
                    <input type="hidden" name="vip_id" value="<?php echo $id; ?>">
                    <button type="submit" class="btn btn-warning w-100 fw-bold">INVEST NOW</button>
                </form>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</body>
</html>
