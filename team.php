<?php
session_start();
include 'api/db.php';

// Security: Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$u_id = $_SESSION['user_id'];

// Get the user's phone for the link
$u_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT phone FROM users WHERE id = '$u_id'"));

// Fetch only users referred by THIS user
$refs = mysqli_query($conn, "SELECT phone, created_at FROM users WHERE referred_by = '$u_id'");

// Generate the Link
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
        .team-header { background: var(--navy); color: white; padding: 30px 20px; border-bottom: 4px solid var(--gold); border-radius: 0 0 30px 30px; }
        .desc-card { background: white; border-radius: 15px; padding: 15px; border-left: 5px solid var(--gold); }
    </style>
</head>
<body>

    <div class="team-header text-center">
        <h3 class="fw-bold">My Team</h3>
        <p class="small opacity-75">Invite friends and earn 15% commission</p>
    </div>

    <div class="container mt-4">
        <div class="desc-card shadow-sm mb-4">
            <h6 class="fw-bold text-navy">About PrimeLuxe</h6>
            <p class="small text-muted mb-0">
                PrimeLuxe is a short-term investment platform focused on fast, smart, and secure returns. 
                We provide flexible opportunities designed to help you grow your money quickly 
                while maintaining transparency, reliability, and ease of use.
            </p>
        </div>

        <div class="card p-3 mb-4 text-center border-0 shadow-sm">
            <label class="small fw-bold text-muted mb-2">YOUR UNIQUE REFERRAL LINK</label>
            <input type="text" class="form-control text-center mb-2" value="<?php echo $ref_link; ?>" id="myLink" readonly>
            <button onclick="copyLink()" class="btn btn-dark fw-bold w-100">COPY LINK</button>
        </div>

        <h6 class="fw-bold text-muted mb-3">TEAM MEMBERS (<?php echo mysqli_num_rows($refs); ?>)</h6>
        
        <div class="list-group shadow-sm">
            <?php 
            if(mysqli_num_rows($refs) > 0) {
                while($r = mysqli_fetch_assoc($refs)) {
                    echo "<div class='list-group-item d-flex justify-content-between align-items-center'>
                            <div>
                                <span class='fw-bold text-dark'>".substr($r['phone'], 0, 4)."****".substr($r['phone'], -3)."</span><br>
                                <small class='text-muted'>Joined: ".date('M d, Y', strtotime($r['created_at']))."</small>
                            </div>
                            <span class='badge bg-success'>Level 1</span>
                          </div>";
                }
            } else {
                echo "<div class='list-group-item text-center text-muted py-4'>No team members yet. Start inviting!</div>";
            }
            ?>
        </div>

        <a href="dashboard.php" class="btn btn-outline-secondary w-100 mt-4 fw-bold">BACK TO DASHBOARD</a>
    </div>

    <script>
    function copyLink() {
        var copyText = document.getElementById("myLink");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        document.execCommand("copy");
        alert("Referral link copied to clipboard!");
    }
    </script>
</body>
</html>
