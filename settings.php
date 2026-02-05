<?php
session_start();
include 'api/db.php';
if(!isset($_SESSION['user_id'])) header("Location: index.php");

$u_id = $_SESSION['user_id'];
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '$u_id'"));
$ref_link = "https://prime-luxe-lgu5.onrender.com/index.php?ref=" . $u_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f4f7f6; padding-bottom: 90px; }
        .header-section { background: var(--navy); color: white; padding: 40px 20px; border-bottom: 5px solid var(--gold); border-radius: 0 0 40px 40px; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; margin-top: -30px; border: 1px solid #ddd; }
        .list-link { background: white; border-radius: 12px; padding: 15px; display: flex; align-items: center; text-decoration: none; color: #333; margin-bottom: 10px; font-weight: bold; }
        .list-link img { width: 25px; margin-right: 15px; }
    </style>
</head>
<body>

    <div class="header-section text-center">
        <h3 class="fw-bold"><?php echo $u['username']; ?></h3>
        <p class="small opacity-75">ID: <?php echo $u['id']; ?> | <?php echo $u['phone']; ?></p>
    </div>

    <div class="container">
        <div class="stat-card shadow-sm text-center mb-4">
            <small class="text-muted text-uppercase fw-bold">Current Balance</small>
            <h2 class="fw-bold text-dark">₦<?php echo number_format($u['balance'], 2); ?></h2>
        </div>

        <div class="p-3 bg-white border rounded mb-4 text-center shadow-sm">
            <small class="fw-bold text-muted">Invite Link (15% Commission)</small>
            <input type="text" class="form-control form-control-sm text-center my-2" value="<?php echo $ref_link; ?>" id="myLink" readonly>
            <button onclick="copyLink()" class="btn btn-dark btn-sm w-100">COPY LINK</button>
        </div>

        <a href="team.php" class="list-link shadow-sm">
            <img src="https://img.icons8.com/color/48/group.png"> My Team Details
        </a>
        <a href="withdraw.php" class="list-link shadow-sm">
            <img src="https://img.icons8.com/color/48/get-cash.png"> Withdraw Funds
        </a>
        <a href="https://wa.me/2348077502802" class="list-link shadow-sm text-success">
            <img src="https://img.icons8.com/color/48/whatsapp.png"> WhatsApp Support
        </a>
        <a href="logout.php" class="list-link shadow-sm text-danger mt-4">
            <img src="https://img.icons8.com/color/48/exit.png"> Logout Account
        </a>
    </div>

    <div class="fixed-bottom bg-white border-top d-flex justify-content-around py-2">
        <a href="dashboard.php" class="text-center text-decoration-none text-muted small"><img src="https://img.icons8.com/material-outlined/24/cccccc/home--v1.png"><br>Home</a>
        <a href="team.php" class="text-center text-decoration-none text-muted small"><img src="https://img.icons8.com/material-outlined/24/cccccc/conference-call.png"><br>Team</a>
        <a href="settings.php" class="text-center text-decoration-none text-dark small fw-bold"><img src="https://img.icons8.com/material-outlined/24/001f3f/user-male-circle.png"><br>Mine</a>
    </div>

    <script>
    function copyLink() {
        var copyText = document.getElementById("myLink");
        copyText.select();
        document.execCommand("copy");
        alert("Referral link copied!");
    }
    </script>
</body>
</html>
