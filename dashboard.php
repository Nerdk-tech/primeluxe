<?php
session_start();
include 'api/db.php';
if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id = '".$_SESSION['user_id']."'"));

// Only users with active investment can withdraw
$inv_check = mysqli_query($conn, "SELECT id FROM investments WHERE user_id = '".$_SESSION['user_id']."' AND status = 'active' LIMIT 1");
$can_withdraw = mysqli_num_rows($inv_check) > 0;

// Referral Details
$invitation_code = $u['id'];
$ref_link = "https://primeluxe-lgu5.onrender.com/?ref=" . $u['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard | Prime Luxe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root { --navy:#0b1b2b; --gold:#d4af37; }
body { background:#0e0e0e; padding-bottom:90px; font-family:'Segoe UI',sans-serif; color:#fff; }

/* Top Bar */
.top-nav-bar {
  background: linear-gradient(135deg,#d4af37,#8f6b1f);
  padding: 14px 18px;
  color:#000;
  display:flex;
  justify-content:space-between;
  align-items:center;
  font-weight:700;
}

/* Banner */
.banner-container {
  margin: 14px;
  border-radius: 14px;
  overflow:hidden;
  box-shadow:0 6px 14px rgba(212,175,55,.25);
}
.banner-container img { width:100%; display:block; }

/* Cards */
.invitation-card, .balance-section {
  background:#141414;
  margin: 0 14px 16px;
  padding: 16px;
  border-radius: 14px;
  border:1px solid rgba(212,175,55,.25);
  box-shadow:0 4px 12px rgba(0,0,0,.4);
}

.invitation-row {
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:10px;
  padding-bottom:10px;
  border-bottom:1px solid rgba(255,255,255,.08);
}

.invitation-label {
  font-size:11px;
  color:#aaa;
  font-weight:600;
  letter-spacing:.5px;
}

.invitation-value {
  font-size:15px;
  font-weight:800;
  color:var(--gold);
}

.share-link-box {
  background:#0c0c0c;
  padding:10px;
  border-radius:10px;
  font-size:11px;
  word-break:break-all;
  color:#ccc;
  border:1px dashed rgba(212,175,55,.4);
  display:flex;
  justify-content:space-between;
  align-items:center;
}

.copy-btn {
  background:linear-gradient(135deg,#d4af37,#8f6b1f);
  color:#000;
  border:none;
  padding:5px 14px;
  border-radius:20px;
  font-size:11px;
  font-weight:800;
  margin-left:10px;
}

/* Balance */
.balance-label {
  font-size:11px;
  color:#aaa;
  text-transform:uppercase;
  letter-spacing:1px;
}

.balance-val {
  font-size:30px;
  font-weight:900;
  color:var(--gold);
  margin:6px 0 2px;
}

/* Buttons */
.btn-gold {
  background:linear-gradient(135deg,#d4af37,#8f6b1f);
  border:none;
  color:#000;
  font-weight:800;
  border-radius:30px;
  padding:6px 18px;
}

.btn-outline-gold {
  background:transparent;
  border:1px solid var(--gold);
  color:var(--gold);
  font-weight:800;
  border-radius:30px;
  padding:6px 18px;
}

/* Bottom Nav */
.bottom-nav {
  background:#111;
  height:72px;
  position:fixed;
  bottom:0;
  width:100%;
  display:flex;
  justify-content:space-around;
  align-items:center;
  border-top:1px solid rgba(212,175,55,.25);
  z-index:1000;
}

.nav-link {
  text-decoration:none;
  color:#888;
  font-size:10px;
  text-align:center;
  flex:1;
}

.nav-link.active {
  color:var(--gold);
  font-weight:800;
}

.nav-link img {
  width:22px;
  display:block;
  margin:0 auto 3px;
  opacity:.5;
}

.nav-link.active img {
  opacity:1;
  filter:drop-shadow(0 0 3px rgba(212,175,55,.7));
}
</style>
</head>
<body>

<div class="top-nav-bar">
  <div>Hi, <?php echo htmlspecialchars($u['phone']); ?></div>
  <a href="https://wa.me/2348077502802" target="_blank">
    <img src="https://img.icons8.com/color/48/whatsapp.png" width="26">
  </a>
</div>

<div class="banner-container">
  <img src="https://primeluxe-lgu5.onrender.com/assets/banner.jpg" onerror="this.src='https://via.placeholder.com/400x150?text=Prime+Luxe+Investment'">
</div>

<div class="invitation-card">
  <div class="invitation-row">
    <div>
      <div class="invitation-label">INVITATION CODE</div>
      <div class="invitation-value"><?php echo $invitation_code; ?></div>
    </div>
    <button onclick="copyText('<?php echo $invitation_code; ?>')" class="copy-btn">COPY</button>
  </div>

  <div class="invitation-label mb-1">SHARE LINK</div>
  <div class="share-link-box">
    <span id="refLink"><?php echo $ref_link; ?></span>
    <button onclick="copyText('<?php echo $ref_link; ?>')" class="copy-btn">COPY</button>
  </div>
</div>

<div class="balance-section text-center">
  <div class="balance-label">Total Balance</div>
  <div class="balance-val">₦<?php echo number_format($u['balance'], 2); ?></div>

  <div class="d-flex justify-content-center gap-2 mt-3">
    <a href="deposit.php" class="btn btn-gold btn-sm px-4">DEPOSIT</a>
    <?php if($can_withdraw): ?>
      <a href="withdraw.php" class="btn btn-outline-gold btn-sm px-4">WITHDRAW</a>
    <?php else: ?>
      <button class="btn btn-outline-gold btn-sm px-4" disabled>WITHDRAW</button>
    <?php endif; ?>
  </div>
</div>

<div class="bottom-nav">
  <a href="dashboard.php" class="nav-link active">
    <img src="https://img.icons8.com/material-rounded/48/d4af37/home.png"><br>Home
  </a>
  <a href="orders.php" class="nav-link">
    <img src="https://img.icons8.com/material-outlined/48/000000/clipboard.png"><br>Orders
  </a>
  <a href="team.php" class="nav-link">
    <img src="https://img.icons8.com/material-outlined/48/000000/conference-call.png"><br>Team
  </a>
  <a href="settings.php" class="nav-link">
    <img src="https://img.icons8.com/material-outlined/48/000000/user-male-circle.png"><br>Profile
  </a>
</div>

<script>
function copyText(val){
  navigator.clipboard.writeText(val).then(()=>{
    alert("Copied!");
  });
}
</script>

</body>
</html>