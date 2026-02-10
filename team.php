<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* Level 1 (Direct Referrals) */
$level1 = mysqli_query($conn, "SELECT phone, created_at FROM users WHERE referred_by = '$uid' ORDER BY id DESC");
$count1 = mysqli_num_rows($level1);

/* Level 2 (Indirect Referrals) 
   Logic: Find users who were referred by the people YOU referred.
*/
$level2 = mysqli_query($conn, "
    SELECT u2.phone, u2.created_at 
    FROM users u2 
    JOIN users u1 ON u2.referred_by = u1.id 
    WHERE u1.referred_by = '$uid'
");
$count2 = mysqli_num_rows($level2);

/* --- FIXED REFERRAL LINK --- 
   Points to index.php which handles the auto-toggle to Register 
*/
$ref_link = "https://" . $_SERVER['HTTP_HOST'] . "/index.php?ref=" . $uid;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Team | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; }
        body { background:#f4f7f6; padding-bottom:100px; font-family:sans-serif; }
        .team-header {
            background: linear-gradient(135deg, var(--navy), #003366);
            color:white; padding:35px 15px; border-radius:0 0 30px 30px;
            text-align:center; box-shadow:0 4px 15px rgba(0,0,0,0.2);
        }
        .stat-card { background:white; border-radius:18px; padding:20px; box-shadow:0 4px 12px rgba(0,0,0,0.05); border:none; }
        .level-card {
            background:white; border-radius:18px; border-left:6px solid var(--gold);
            padding:16px; box-shadow:0 4px 12px rgba(0,0,0,0.05);
        }
        .copy-btn {
            background: linear-gradient(135deg, var(--gold), #b38f2d);
            border:none; color:white; font-weight:bold; border-radius:12px;
            padding:12px; width:100%; transition: 0.3s;
        }
        .copy-btn:active { transform: scale(0.98); }
        .partner-row { padding:15px; border-bottom:1px solid #f0f0f0; }
        .partner-row:last-child { border-bottom:none; }
        .form-control:focus { box-shadow: none; border-color: var(--gold); }
    </style>
</head>
<body>

<div class="team-header shadow">
    <h4 class="fw-bold mb-1">My Network</h4>
    <p class="small opacity-75 mb-0">Total Team Size: <b><?php echo $count1 + $count2; ?> Members</b></p>
</div>

<div class="container mt-4">

    <div class="stat-card mb-4">
        <label class="small fw-bold text-muted mb-2 text-uppercase">Invite Link</label>
        <div class="d-flex gap-2">
            <input type="text" id="refLink" class="form-control bg-light border-0 small py-2" value="<?php echo $ref_link; ?>" readonly>
        </div>
        <button onclick="copyLink()" class="copy-btn mt-3 shadow-sm">
            <i class="bi bi-share-fill me-2"></i> COPY REFERRAL LINK
        </button>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="level-card h-100">
                <div class="small fw-bold text-muted">Level 1</div>
                <div class="h5 fw-bold m-0"><?php echo $count1; ?></div>
                <span class="badge bg-success mt-2">25% Comm.</span>
            </div>
        </div>
        <div class="col-6">
            <div class="level-card h-100" style="border-left-color: #007bff;">
                <div class="small fw-bold text-muted">Level 2</div>
                <div class="h5 fw-bold m-0"><?php echo $count2; ?></div>
                <span class="badge bg-primary mt-2">3% Comm.</span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
        <span class="fw-bold text-muted small text-uppercase">Direct Partners</span>
        <span class="badge rounded-pill bg-light text-dark border"><?php echo $count1; ?> Total</span>
    </div>
    
    <div class="stat-card p-0 overflow-hidden">
        <?php if($count1 > 0): ?>
            <?php while($r = mysqli_fetch_assoc($level1)): 
                $hidden_phone = substr($r['phone'], 0, 4) . "****" . substr($r['phone'], -3);
            ?>
                <div class="partner-row d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-navy"><?php echo $hidden_phone; ?></div>
                        <div class="text-muted" style="font-size: 11px;">
                            <i class="bi bi-calendar-check me-1"></i>Joined <?php echo date('d M Y', strtotime($r['created_at'])); ?>
                        </div>
                    </div>
                    <span class="text-success small fw-bold">Active <i class="bi bi-check-circle-fill"></i></span>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center text-muted py-5 small">
                <i class="bi bi-person-plus fs-1 opacity-25 d-block mb-2"></i>
                No referrals yet. Share your link to start earning!
            </div>
        <?php endif; ?>
    </div>

    <div class="mt-4 mb-5">
        <a href="dashboard.php" class="btn btn-dark w-100 fw-bold rounded-pill py-3 shadow-sm">
            <i class="bi bi-arrow-left me-2"></i> Back to Dashboard
        </a>
    </div>
</div>

<script>
function copyLink(){
    const el = document.getElementById('refLink');
    el.select();
    el.setSelectionRange(0, 99999); // Compatibility for mobile
    navigator.clipboard.writeText(el.value).then(()=>{
        alert("✅ Link copied! Share with your Friends.");
    });
}
</script>

</body>
</html>
