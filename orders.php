<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

// Fetch active investment
$query = "SELECT * FROM investments WHERE user_id = '$uid' AND status = 'active' ORDER BY id DESC LIMIT 1";
$result = mysqli_query($conn, $query);
$inv = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; --teal:#008080; }
        body { background:#f4f7f6; font-family:sans-serif; padding-bottom:90px; }
        .top-nav { background:var(--navy); color:white; padding:15px; }
        .order-card { background:white; border-radius:14px; border:none; }
        .income-timer { color:#0d6efd; font-weight:bold; font-size:14px; }
        .plan-icon { background:var(--navy); border-radius:12px; width:80px; height:80px; display:flex; align-items:center; justify-content:center; }

        .bottom-nav { background:white; border-top:1px solid #eee; position:fixed; bottom:0; width:100%; height:70px; display:flex; justify-content:space-around; align-items:center; }
        .nav-item { text-decoration:none; color:#bbb; font-size:11px; text-align:center; }
        .nav-item.active { color:var(--navy); font-weight:bold; }
        .nav-item img { width:24px; display:block; margin:0 auto 3px; }
    </style>
</head>
<body>

<div class="top-nav d-flex align-items-center">
    <a href="dashboard.php" class="text-white text-decoration-none me-3">←</a>
    <h5 class="mb-0 fw-bold mx-auto">Orders</h5>
</div>

<div class="container mt-4">
<?php if($inv): 
    $earned = $inv['current_step'] * $inv['daily_income'];
?>
    <div class="card order-card shadow-sm p-3">
        <div class="d-flex justify-content-between">
            <div>
                <h5 class="fw-bold">VIP <?php echo $inv['vip_level']; ?></h5>
                <div class="small text-muted mb-1">
                    Investment Days:
                    <span class="text-dark fw-bold">
                        <?php echo $inv['current_step']; ?> / <?php echo $inv['max_steps']; ?> Days
                    </span>
                </div>
                <div class="small text-muted mb-1">
                    Daily Income:
                    <span class="text-dark fw-bold">₦<?php echo number_format($inv['daily_income']); ?></span>
                </div>
                <div class="small text-muted">
                    Earned Income:
                    <span class="text-dark fw-bold">₦<?php echo number_format($earned); ?></span>
                </div>
            </div>
            <div class="plan-icon">
                <img src="https://img.icons8.com/external-flat-icons-inmotus-design/64/ffffff/external-Growth-stock-market-flat-icons-inmotus-design.png" width="40">
            </div>
        </div>

        <div class="text-center mt-4 pt-3 border-top">
            <div class="income-timer">
                Income in <span id="timer">--h : --m : --s</span>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="text-center mt-5">
        <p class="text-muted">You have no active investments.</p>
        <a href="dashboard.php" class="btn btn-warning rounded-pill px-4 fw-bold">Invest Now</a>
    </div>
<?php endif; ?>
</div>

<a href="#" style="position:fixed; bottom:90px; left:20px;">
    <img src="https://img.icons8.com/color/48/telegram-app.png">
</a>

<div class="bottom-nav">
    <a href="dashboard.php" class="nav-item">
        <img src="https://img.icons8.com/material-outlined/24/cccccc/home.png">Home
    </a>
    <a href="orders.php" class="nav-item active">
        <img src="https://img.icons8.com/material-rounded/24/001f3f/clipboard.png">Orders
    </a>
    <a href="team.php" class="nav-item">
        <img src="https://img.icons8.com/material-outlined/24/cccccc/conference-call.png">Team
    </a>
    <a href="settings.php" class="nav-item">
        <img src="https://img.icons8.com/material-outlined/24/cccccc/user-male-circle.png">Profile
    </a>
</div>

<script>
function updateTimer() {
    const now = new Date();
    const next = new Date();
    next.setHours(24,0,0,0);
    const diff = next - now;

    const h = Math.floor(diff / (1000 * 60 * 60));
    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((diff % (1000 * 60)) / 1000);

    document.getElementById('timer').innerHTML = h + "h : " + m + "m : " + s + "s";
}
setInterval(updateTimer, 1000);
updateTimer();
</script>

</body>
</html>