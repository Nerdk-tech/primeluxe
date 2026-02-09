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
    <title>My Profile | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; }
        body { background:#f4f7f6; font-family: sans-serif; padding-bottom:100px; }

        .profile-header {
            background: linear-gradient(135deg, var(--navy), #002b5c);
            color:white;
            padding:45px 20px;
            text-align:center;
            border-bottom: 4px solid var(--gold);
            border-radius: 0 0 25px 25px;
        }
        .balance-box {
            font-size:32px;
            font-weight:800;
            margin-bottom:15px;
        }
        .invest-btn {
            background: var(--gold);
            color: white;
            border:none;
            padding:10px 30px;
            border-radius:30px;
            font-weight:700;
            text-decoration:none;
            display:inline-block;
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }

        .action-row { display:flex; gap:12px; padding:20px 15px; }
        .btn-blue {
            background:var(--navy);
            color:white;
            flex:1;
            padding:14px;
            border-radius:15px;
            font-weight:700;
            text-decoration:none;
            text-align:center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .menu-list {
            background:white;
            margin:0 15px;
            border-radius:18px;
            overflow:hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.05);
        }
        .menu-item {
            display:flex;
            align-items:center;
            padding:16px;
            border-bottom:1px solid #f1f1f1;
            text-decoration:none;
            color:#333;
            font-size:14px;
            font-weight: 500;
        }
        .menu-item:last-child { border-bottom:none; }
        .menu-item i { margin-right:15px; color:var(--navy); font-size:18px; }
        .menu-item .chevron { margin-left:auto; color:#ccc; }

        .btn-logout {
            background:#ff4d4d;
            color:white;
            margin:25px 15px;
            padding:15px;
            border-radius:15px;
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
            padding:12px 0;
            border-top:1px solid #eee;
            z-index: 1000;
        }
        .nav-link { color:#aaa; text-decoration:none; font-size:11px; text-align:center; }
        .nav-link.active { color:var(--navy); font-weight:700; }
    </style>
</head>
<body>

<div class="profile-header shadow">
    <p class="small opacity-75 mb-1">Total Balance</p>
    <div class="balance-box">₦<?php echo number_format($u['balance'], 2); ?></div>
    <a href="dashboard.php" class="invest-btn">Start Investing</a>
</div>

<div class="action-row">
    <a href="deposit.php" class="btn-blue">Deposit</a>
    <a href="withdraw.php" class="btn-blue">Withdraw</a>
</div>

<div class="menu-list">
    <a href="history.php" class="menu-item">
        <i class="bi bi-clock-history"></i> Transaction History <span class="chevron">›</span>
    </a>
    <a href="team.php" class="menu-item">
        <i class="bi bi-people-fill"></i> My Team <span class="chevron">›</span>
    </a>
    <a href="bank_details.php" class="menu-item">
        <i class="bi bi-bank2"></i> Bank Settings <span class="chevron">›</span>
    </a>
    <a href="https://chat.whatsapp.com/LLGhNA8L0HfDNmU34S8CDt" class="menu-item">
        <i class="bi bi-whatsapp"></i> WhatsApp Group <span class="chevron">›</span>
    </a>
    <a href="https://wa.me/2348077502802" class="menu-item">
        <i class="bi bi-headset"></i> Online Support <span class="chevron">›</span>
    </a>
</div>

<a href="api/logout.php" class="btn-logout"><i class="bi bi-box-arrow-right"></i> Log Out</a>

<div class="bottom-nav shadow-lg">
    <a href="dashboard.php" class="nav-link"><i class="bi bi-house-door fs-4"></i><br>Home</a>
    <a href="orders.php" class="nav-link"><i class="bi bi-clipboard-check fs-4"></i><br>Orders</a>
    <a href="team.php" class="nav-link"><i class="bi bi-people fs-4"></i><br>Team</a>
    <a href="settings.php" class="nav-link active"><i class="bi bi-person-circle-fill fs-4"></i><br>Profile</a>
</div>

</body>
</html>
