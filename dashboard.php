<?php
session_start();
include 'api/db.php';
// Assuming user is logged in, fetch their real-time balance
$user_id = $_SESSION['user_id'] ?? 1; 
$query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($query);
$current_balance = $user['balance'] ?? "400.00"; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --luxe-gold: #D4AF37; --luxe-navy: #001f3f; }
        body { background: #fdfdfd; font-family: 'Inter', sans-serif; }
        .top-nav { background: var(--luxe-navy); border-bottom: 3px solid var(--luxe-gold); color: white; padding: 15px; }
        .balance-box { background: linear-gradient(135deg, var(--luxe-navy), #003366); color: white; border-radius: 20px; border: 1.5px solid var(--luxe-gold); }
        .btn-luxe { background: var(--luxe-gold); color: #000; font-weight: 700; border: none; border-radius: 8px; text-decoration: none; display: inline-block; }
        .vip-card { border: none; border-radius: 15px; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <div class="top-nav text-center">
        <?php if(file_exists('assets/logo.png')): ?>
            <img src="assets/logo.png" alt="Prime Luxe" style="height: 40px;">
        <?php else: ?>
            <h5 class="m-0 text-gold">PRIME LUXE</h5>
        <?php endif; ?>
    </div>

    <div class="container mt-4">
        <div class="card balance-box p-4 text-center shadow-lg">
            <p class="small text-uppercase mb-1 opacity-75">Your Balance</p>
            <h1 class="display-5 fw-bold">₦<?php echo number_format($current_balance, 2); ?></h1>
            <div class="d-flex justify-content-center gap-3 mt-3">
                <button class="btn btn-luxe px-4 py-2">DEPOSIT</button>
                <a href="withdraw.php" class="btn btn-outline-light px-4 py-2">WITHDRAW</a>
            </div>
        </div>
        
        <h6 class="mt-4 fw-bold">AVAILABLE INVESTMENTS</h6>
<div class="row g-3">
    <?php
    // Define the plans based on Ella's rules
    $plans = [
        ['level' => 1, 'price' => 3000, 'range' => '3k - 20k'],
        ['level' => 2, 'price' => 23000, 'range' => '23k - 50k'],
        ['level' => 3, 'price' => 53000, 'range' => '53k - 80k'],
        ['level' => 4, 'price' => 83000, 'range' => '83k - 110k'],
        ['level' => 5, 'price' => 113000, 'range' => '113k - 150k']
    ];

    foreach ($plans as $plan): ?>
    <div class="col-6">
        <div class="card vip-card p-3 text-center border-gold">
            <span class="badge bg-dark mb-2 py-2">VIP <?php echo $plan['level']; ?></span>
            <h6 class="fw-bold m-0">₦<?php echo number_format($plan['price']); ?></h6>
            <small class="text-muted"><?php echo $plan['range']; ?></small>
            <br>
            <small class="text-success fw-bold">ROI: 2.1x / 7 Days</small>
            <form action="api/invest.php" method="POST">
                <input type="hidden" name="amount" value="<?php echo $plan['price']; ?>">
                <input type="hidden" name="vip_level" value="<?php echo $plan['level']; ?>">
                <button type="submit" name="invest" class="btn btn-luxe btn-sm mt-3 w-100">JOIN PLAN</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
