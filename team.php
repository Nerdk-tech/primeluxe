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

// FIX: Point link to root URL to prevent "Not Found" error on Render
$ref_link = "https://prime-luxe-lgu5.onrender.com/?ref=" . $u_id;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Luxe | My Network</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f4f7f6; padding-bottom: 80px; }
        .team-header { background: var(--navy); color: white; padding: 30px 15px; border-bottom: 4px solid var(--gold); border-radius: 0 0 30px 30px; }
        .comm-badge { font-size: 11px; padding: 5px 10px; border-radius: 20px; font-weight: bold; }
        .level-card { background: white; border-radius: 15px; border-left: 6px solid var(--gold); transition: transform 0.2s; }
        .level-card:active { transform: scale(0.98); }
        .form-control:focus { box-shadow: none; border: 1px solid var(--gold); }
    </style>
</head>
<body>

    <div class="team-header text-center shadow">
        <h4 class="fw-bold mb-1">TEAM NETWORK</h4>
        <p class="small opacity-75 mb-0">Total Members: <span class="fw-bold text-gold"><?php echo ($count1 + $count2); ?></span></p>
    </div>

    <div class="container mt-4">
        <div class="card p-3 mb-4 text-center border-0 shadow-sm" style="border-radius:20px;">
            <label class="small fw-bold text-muted mb-2 text-uppercase letter-spacing-1">Invite Friends</label>
            <div class="input-group mb-2">
                <input type="text" class="form-control text-center bg-light border-0 fw-bold" value="<?php echo $ref_link; ?>" id="myLink" readonly>
            </div>
            <button onclick="copyLink()" class="btn btn-dark fw-bold w-100 py-3 shadow-sm rounded-pill">COPY LINK</button>
        </div>

        <div class="level-card p-3 mb-3 shadow-sm d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0 text-navy">Level 1 (Direct)</h6>
                <small class="text-muted"><?php echo $count1; ?> Active Members</small>
            </div>
            <span class="comm-badge bg-success text-white">25% PROFIT</span>
        </div>

        <div class="level-card p-3 mb-4 shadow-sm d-flex justify-content-between align-items-center" style="border-left-color: #007bff;">
            <div>
                <h6 class="fw-bold mb-0 text-navy">Level 2 (Indirect)</h6>
                <small class="text-muted"><?php echo $count2; ?> Active Members</small>
            </div>
            <span class="comm-badge bg-primary text-white">3% PROFIT</span>
        </div>

        <h6 class="fw-bold text-muted small mb-3 px-1">YOUR DIRECT PARTNERS</h6>
        <div class="list-group shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <?php 
            if($count1 > 0) {
                while($r = mysqli_fetch_assoc($level1)) {
                    $hidden_phone = substr($r['phone'], 0, 4) . "****" . substr($r['phone'], -3);
                    echo "<div class='list-group-item d-flex justify-content-between align-items-center py-3 border-0 border-bottom'>
                            <div>
                                <span class='fw-bold text-navy'>$hidden_phone</span><br>
                                <small class='text-muted'>Joined ".date('d M Y', strtotime($r['created_at']))."</small>
                            </div>
                            <span class='badge rounded-pill bg-light text-success border border-success'>Verified</span>
                          </div>";
                }
            } else {
                echo "<div class='list-group-item text-center text-muted py-5 small'>You haven't invited anyone yet. Start sharing!</div>";
            }
            ?>
        </div>

        <div class="mt-4 px-2">
            <a href="dashboard.php" class="btn btn-outline-navy w-100 fw-bold rounded-pill py-2 border-2">BACK TO DASHBOARD</a>
        </div>
    </div>

    <script>
    async function copyLink() {
        const linkInput = document.getElementById("myLink");
        try {
            await navigator.clipboard.writeText(linkInput.value);
            alert("✅ Link copied! Share it with your friends.");
        } catch (err) {
            // Fallback for older browsers
            linkInput.select();
            document.execCommand("copy");
            alert("✅ Link copied!");
        }
    }
    </script>
</body>
</html>
