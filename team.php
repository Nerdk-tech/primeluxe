<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* Level 1 (Direct Referrals) */
$level1 = mysqli_query($conn, "SELECT phone, created_at FROM users WHERE referred_by = '$uid'");
$count1 = mysqli_num_rows($level1);

/* Level 2 (Indirect Referrals) */
$level2 = mysqli_query($conn, "
    SELECT u2.phone, u2.created_at 
    FROM users u1 
    JOIN users u2 ON u1.id = u2.referred_by 
    WHERE u1.referred_by = '$uid'
");
$count2 = mysqli_num_rows($level2);

/* Referral Link */
$ref_link = "https://prime-luxe-lgu5.onrender.com/?ref=" . $uid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Team | Prime Luxe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root { --navy:#001f3f; --gold:#D4AF37; }
body { background:#f4f7f6; padding-bottom:90px; font-family:sans-serif; }

.team-header {
  background:linear-gradient(135deg,var(--navy),#003366);
  color:white;
  padding:28px 16px;
  border-radius:0 0 26px 26px;
  text-align:center;
  box-shadow:0 4px 10px rgba(0,0,0,.15);
}

.stat-card {
  background:white;
  border-radius:16px;
  padding:16px;
  box-shadow:0 4px 10px rgba(0,0,0,.05);
}

.level-card {
  background:white;
  border-radius:16px;
  border-left:6px solid var(--gold);
  padding:14px 16px;
  box-shadow:0 4px 10px rgba(0,0,0,.05);
}

.copy-btn {
  background:linear-gradient(135deg,#d4af37,#8f6b1f);
  border:none;
  color:#000;
  font-weight:800;
  border-radius:30px;
  padding:10px 16px;
  width:100%;
}

.partner-row {
  padding:14px 16px;
  border-bottom:1px solid #eee;
}

.partner-row:last-child { border-bottom:none; }
</style>
</head>
<body>

<div class="team-header">
  <h5 class="fw-bold mb-1">TEAM NETWORK</h5>
  <small class="opacity-75">Total Members: <b><?php echo $count1 + $count2; ?></b></small>
</div>

<div class="container mt-4">

  <!-- Invite Card -->
  <div class="stat-card mb-4 text-center">
    <div class="small fw-bold text-muted mb-2">INVITE FRIENDS</div>
    <input type="text" id="refLink" class="form-control text-center mb-3 fw-bold bg-light" value="<?php echo $ref_link; ?>" readonly>
    <button onclick="copyLink()" class="copy-btn">COPY LINK</button>
  </div>

  <!-- Levels -->
  <div class="level-card mb-3 d-flex justify-content-between align-items-center">
    <div>
      <div class="fw-bold">Level 1 (Direct)</div>
      <small class="text-muted"><?php echo $count1; ?> Members</small>
    </div>
    <span class="badge bg-success">25%</span>
  </div>

  <div class="level-card mb-4 d-flex justify-content-between align-items-center" style="border-left-color:#0d6efd;">
    <div>
      <div class="fw-bold">Level 2 (Indirect)</div>
      <small class="text-muted"><?php echo $count2; ?> Members</small>
    </div>
    <span class="badge bg-primary">3%</span>
  </div>

  <!-- Direct Partners -->
  <div class="fw-bold text-muted small mb-2 px-1">YOUR DIRECT PARTNERS</div>
  <div class="stat-card p-0 overflow-hidden">

    <?php
    if($count1 > 0) {
      while($r = mysqli_fetch_assoc($level1)) {
        $hidden_phone = substr($r['phone'], 0, 4) . "****" . substr($r['phone'], -3);
        echo "
        <div class='partner-row d-flex justify-content-between align-items-center'>
          <div>
            <div class='fw-bold'>$hidden_phone</div>
            <small class='text-muted'>Joined ".date('d M Y', strtotime($r['created_at']))."</small>
          </div>
          <span class='badge bg-light text-success border border-success'>Active</span>
        </div>";
      }
    } else {
      echo "<div class='text-center text-muted py-5 small'>No referrals yet. Share your link to earn.</div>";
    }
    ?>

  </div>

  <div class="mt-4">
    <a href="dashboard.php" class="btn btn-outline-secondary w-100 fw-bold rounded-pill">Back to Dashboard</a>
  </div>

</div>

<script>
function copyLink(){
  const el = document.getElementById('refLink');
  navigator.clipboard.writeText(el.value).then(()=>{
    alert("✅ Link copied!");
  });
}
</script>

</body>
</html>