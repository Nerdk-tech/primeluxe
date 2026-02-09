<?php
session_start();
include 'api/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$uid = $_SESSION['user_id'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$uid'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; --teal:#008080; }
        body { background:#f5f7fa; font-family:sans-serif; padding-bottom:90px; }

        .profile-header {
            background: linear-gradient(135deg, var(--navy), #002b5c);
            color:white;
            padding:40px 20px;
            text-align:center;
        }
        .balance-box {
            font-size:28px;
            font-weight:800;
            letter-spacing:0.5px;
            margin-bottom:10px;
        }
        .invest-btn {
            background:white;
            color:var(--navy);
            border:none;
            padding:8px 26px;
            border-radius:30px;
            font-weight:700;
            text-decoration:none;
            display:inline-block;
        }

        .action-row {
            display:flex;
            gap:12px;
            padding:15px;
        }
        .btn-blue {
            background:var(--navy);
            color:white;
            flex:1;
            border:none;
            padding:12px;
            border-radius:12px;
            font-weight:700;
            text-decoration:none;
            text-align:center;
        }

        .menu-list {
            background:white;
            margin:0 15px;
            border-radius:14px;
            overflow:hidden;
            box-shadow:0 4px 10px rgba(0,0,0,.05);
        }
        .menu-item {
            display:flex;
            align-items:center;
            padding:15px;
            border-bottom:1px solid #f0f0f0;
            text-decoration:none;
            color:#333;
            font-size:14px;
        }
        .menu-item:last-child { border-bottom:none; }
        .menu-item i {
            margin-right:12px;
            color:var(--navy);
            font-size:18px;
        }
        .menu-item .chevron {
            margin-left:auto;
            color:#bbb;
            font-size:18px;
        }
        .menu-item:active { background:#f8f9fa; }

        .btn-logout {
            background:#dc3545;
            color:white;
            margin:20px 15px;
            padding:15px;
            border-radius:14px;
            text-align:center;
            text-decoration:none;
            display:block;
            font-weight:700;
        }

        .bottom-nav {
            background:white;
            position:fixed;
            bottom:0;
            width:100%;
            display:flex;
            justify-content:space-around;
            padding:10px 0;
            border-top:1px solid #eee;
        }
        .nav-link {
            color:#aaa;
            text-decoration:none;
            font-size:11px;
            text-align:center;
        }
        .nav-link.active {
            color:var(--navy);
            font-weight:700;
        }
    </style>
</head>
<body>

<div class="profile-header">
    <div class="balance-box">₦<?php echo number_format($u['balance'], 2); ?></div>
    <a href="dashboard.php" class="invest-btn">Invest to Earn</a>
</div>

<div class="action-row">
    <a href="deposit.php" class="btn-blue">Recharge</a>
    <a href="withdraw.php" class="btn-blue">Withdraw</a>
</div>

<div class="menu-list">
    <a href="earnings.php" class="menu-item">
        <i class="bi bi-wallet2"></i> My Earnings <span class="chevron">›</span>
    </a>
    <a href="team.php" class="menu-item">
        <i class="bi bi-people"></i> Team <span class="chevron">›</span>
    </a>
    <a href="deposit_history.php" class="menu-item">
        <i class="bi bi-arrow-down-circle"></i> Deposit History <span class="chevron">›</span>
    </a>
    <a href="withdraw_history.php" class="menu-item">
        <i class="bi bi-arrow-up-circle"></i> Withdrawal History <span class="chevron">›</span>
    </a>
    <a href="bank_details.php" class="menu-item">
        <i class="bi bi-bank"></i> Bank Details <span class="chevron">›</span>
    </a>
    <a href="https://chat.whatsapp.com/LLGhNA8L0HfDNmU34S8CDt" class="menu-item">
        <i class="bi bi-whatsapp"></i> WhatsApp Group <span class="chevron">›</span>
    </a>
    <a href="https://wa.me/2348077502802" class="menu-item">
        <i class="bi bi-broadcast"></i> WhatsApp Channel <span class="chevron">›</span>
    </a>
</div>

<a href="api/logout.php" class="btn-logout"><i class="bi bi-power"></i> Logout</a>

<div class="bottom-nav">
    <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door" style="font-size:22px;"></i><br>Home</a>
    <a href="dashboard.php" class="nav-link"><i class="bi bi-grid" style="font-size:22px;"></i><br>Products</a>
    <a href="team.php" class="nav-link"><i class="bi bi-people" style="font-size:22px;"></i><br>Team</a>
    <a href="settings.php" class="nav-link active"><i class="bi bi-person-circle" style="font-size:22px;"></i><br>Profile</a>
</div>

</body>
</html>