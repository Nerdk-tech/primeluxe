<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$u_id = $_SESSION['user_id'];

// Get Level 1 Referrals (Directly invited by user)
$level1 = mysqli_query($conn, "SELECT phone, created_at FROM users WHERE referred_by = '$u_id'");
$count1 = mysqli_num_rows($level1);

// Get Level 2 Referrals (Invited by Level 1 users)
$level2_query = "SELECT u2.phone, u2.created_at 
                 FROM users u1 
                 JOIN users u2 ON u1.id = u2.referred_by 
                 WHERE u1.referred_by = '$u_id'";
$level2 = mysqli_query($conn, $level2_query);
$count2 = mysqli_num_rows($level2);

$ref_link = "https://prime-luxe-lgu5.onrender.com/index.php?ref=" . $u_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; padding-bottom: 80px; }
        .team-header { background: var(--navy); color: white; padding: 25px 15px; border-bottom: 4px solid var(--gold); border-radius: 0 0 25px 25px; }
        .comm-badge { font-size: 11px; padding: 4px 8px; border-radius: 20px; font-weight: bold; }
        .level-card { background: white; border-radius: 12px; border-left: 5px solid var(--gold); }
    </style>
</head>
<body>

    <div class="team-header text-center shadow">
        <h4 class="fw-bold mb-1">My Network</h4>
        <p class="small opacity-75 mb-0">Total Team Size: <?php echo ($count1 + $count2); ?></p>
    </div>

    <div class="container mt-4">
        <div class="card p-3 mb-4 text-center border-0 shadow-sm" style="border-radius:15px;">
            <label class="small fw-bold text-muted mb-2 text-uppercase">My Invitation Link</label>
            <input type="text" class="form-control text-center mb-2 bg-light border-0" value="<?php echo $ref_link; ?>" id="myLink" readonly>
            <button onclick="copyLink()" class="btn btn-dark fw-bold w-100 py-2">COPY & SHARE</button>
        </div>

        <div class="level-card p-3 mb-3 shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0">Level 1 Referrals</h6>
                <small class="text-muted"><?php echo $count1; ?> Members Joined</small>
            </div>
            <span class="comm-badge bg-success text-white">25% COMM</span>
        </div>

        <div class="level-card p-3 mb-4 shadow-sm d-flex justify-content-between align-items-center" style="border-left-color: #28a745;">
            <div>
                <h6 class="fw-bold mb-0">Level 2 Referrals</h6>
                <small class="text-muted"><?php echo $count2; ?> Members Joined</small>
            </div>
            <span class="comm-badge bg-primary text-white">3% COMM</span>
        </div>

        <h6 class="fw-bold text-muted small mb-3">RECENT JOINEES (LVL 1)</h6>
        <div class="list-group shadow-sm">
            <?php 
            if($count1 > 0) {
                while($r = mysqli_fetch_assoc($level1)) {
                    echo "<div class='list-group-item d-flex justify-content-between align-items-center border-0 border-bottom'>
                            <div>
                                <span class='fw-bold'>".substr($r['phone'], 0, 4)."****".substr($r['phone'], -3)."</span><br>
                                <small class='text-muted'>".date('M d, Y', strtotime($r['created_at']))."</small>
                            </div>
                            <span class='badge bg-light text-dark'>LVL 1</span>
                          </div>";
                }
            } else {
                echo "<div class='list-group-item text-center text-muted py-4 small'>No direct referrals yet.</div>";
            }
            ?>
        </div>

        <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-4 fw-bold rounded-pill">BACK TO HOME</a>
    </div>

    <script>
    function copyLink() {
        var copyText = document.getElementById("myLink");
        copyText.select();
        document.execCommand("copy");
        alert("Referral link copied to clipboard!");
    }
    </script>
</body>
</html>
