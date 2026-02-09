<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

// Fetch all active investments for the user
$query = "SELECT * FROM investments WHERE user_id = '$uid' AND status = 'active' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Investments | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; }
        body { background:#f8f9fa; font-family:sans-serif; padding-bottom:100px; }
        .top-nav { background:var(--navy); color:white; padding:15px; border-bottom: 3px solid var(--gold); }
        .order-card { background:white; border-radius:18px; border:none; border-left: 5px solid var(--navy); }
        .income-timer { background: #e7f3ff; color:#0d6efd; font-weight:bold; font-size:13px; padding: 10px; border-radius: 10px; }
        .plan-icon { background:var(--navy); border-radius:15px; width:70px; height:70px; display:flex; align-items:center; justify-content:center; }
        
        /* Fixed WhatsApp Floating Button */
        .float-wa { position:fixed; bottom:90px; left:20px; z-index: 1000; }
        
        .bottom-nav { background:white; border-top:1px solid #eee; position:fixed; bottom:0; width:100%; height:75px; display:flex; justify-content:space-around; align-items:center; z-index: 1000; }
        .nav-item { text-decoration:none; color:#bbb; font-size:11px; text-align:center; }
        .nav-item.active { color:var(--navy); font-weight:bold; }
        .nav-item i { font-size: 22px; display:block; margin-bottom: 2px; }
    </style>
</head>
<body>

<div class="top-nav text-center">
    <h6 class="mb-0 fw-bold">Active Investments</h6>
</div>

<div class="container mt-4">
<?php if(mysqli_num_rows($result) > 0): ?>
    <?php while($inv = mysqli_fetch_assoc($result)): 
        $earned = $inv['current_step'] * $inv['daily_income'];
    ?>
    <div class="card order-card shadow-sm p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-1">LUXE VIP <?php echo $inv['vip_level']; ?></h5>
                <div class="small text-muted mb-1">Duration: <span class="text-dark fw-bold"><?php echo $inv['current_step']; ?> / <?php echo $inv['max_steps']; ?> Days</span></div>
                <div class="small text-muted mb-1">Daily Income: <span class="text-success fw-bold">₦<?php echo number_format($inv['daily_income']); ?></span></div>
                <div class="small text-muted">Total Earned: <span class="text-primary fw-bold">₦<?php echo number_format($earned); ?></span></div>
            </div>
            <div class="plan-icon shadow">
                <i class="bi bi-shield-check text-white fs-1"></i>
            </div>
        </div>

        <div class="text-center mt-3 pt-2">
            <div class="income-timer">
                <i class="bi bi-stopwatch"></i> Next Settlement in: <span id="timer-<?php echo $inv['id']; ?>">--h : --m : --s</span>
            </div>
        </div>
    </div>
    <?php endwhile; ?>
<?php else: ?>
    <div class="text-center mt-5">
        <i class="bi bi-cart-x text-muted" style="font-size: 60px;"></i>
        <p class="text-muted mt-3">You have no active products at the moment.</p>
        <a href="dashboard.php" class="btn btn-primary rounded-pill px-5 fw-bold shadow">Buy Now</a>
    </div>
<?php endif; ?>
</div>

<a href="https://wa.me/2348077502802" class="float-wa">
    <img src="https://img.icons8.com/color/48/whatsapp.png" alt="WhatsApp Support">
</a>

<div class="bottom-nav shadow">
    <a href="dashboard.php" class="nav-item">
        <i class="bi bi-house"></i>Home
    </a>
    <a href="orders.php" class="nav-item active">
        <i class="bi bi-clipboard-check-fill"></i>Orders
    </a>
    <a href="team.php" class="nav-item">
        <i class="bi bi-people"></i>Team
    </a>
    <a href="settings.php" class="nav-item">
        <i class="bi bi-person-circle"></i>Profile
    </a>
</div>

<script>
function updateTimers() {
    const now = new Date();
    const next = new Date();
    next.setHours(24,0,0,0); // Countdown to midnight
    const diff = next - now;

    const h = Math.floor(diff / (1000 * 60 * 60));
    const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const s = Math.floor((diff % (1000 * 60)) / 1000);

    const timeStr = h + "h : " + m + "m : " + s + "s";
    
    // Select all timer spans and update them
    document.querySelectorAll('[id^="timer-"]').forEach(timer => {
        timer.innerHTML = timeStr;
    });
}
setInterval(updateTimers, 1000);
updateTimers();
</script>

</body>
</html>
