<?php
session_start();
include 'api/db.php';

// Check if Ella is logged in
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* --- DEPOSIT APPROVAL LOGIC --- */
if(isset($_GET['approve'])){
    $dep_id = (int) $_GET['approve'];
    mysqli_begin_transaction($conn);

    try {
        $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id='$dep_id' FOR UPDATE"));

        if($d && $d['status'] === 'pending'){
            $uid = (int)$d['user_id'];
            $amt = (float)$d['amount'];

            // 1. Credit the user who deposited
            mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
            mysqli_query($conn, "UPDATE deposits SET status='approved' WHERE id='$dep_id'");
            
            // 2. Referral Rewards (L1: 25%, L2: 3%)
            $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id='$uid'"));
            if(!empty($u['referred_by'])){
                $lvl1_id = (int)$u['referred_by'];
                $comm1 = round($amt * 0.25, 2);
                mysqli_query($conn, "UPDATE users SET balance = balance + $comm1 WHERE id='$lvl1_id'");

                // Check for Level 2
                $u2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id='$lvl1_id'"));
                if(!empty($u2['referred_by'])){
                    $lvl2_id = (int)$u2['referred_by'];
                    $comm2 = round($amt * 0.03, 2);
                    mysqli_query($conn, "UPDATE users SET balance = balance + $comm2 WHERE id='$lvl2_id'");
                }
            }

            mysqli_commit($conn);
            header("Location: admin.php?msg=Approved");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: admin.php?err=Failed");
    }
    exit();
}

// Stats fetching
$pending_dep = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM deposits WHERE status='pending'"));
$pending_with = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM withdrawals WHERE status='pending'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Prime Luxe Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background:#f4f7f6; font-family: sans-serif; }
        .top-nav { background: #001f3f; color: white; padding: 15px; border-bottom: 4px solid #D4AF37; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="top-nav d-flex justify-content-between">
    <h5 class="m-0 fw-bold">PRIME LUXE ADMIN</h5>
    <a href="api/logout.php" class="btn btn-sm btn-outline-light">Logout</a>
</div>

<div class="container mt-4">
    <div class="row g-3 text-center">
        <div class="col-6">
            <div class="stat-card">
                <div class="text-muted small">Pending Deposits</div>
                <h3 class="fw-bold text-success"><?php echo $pending_dep; ?></h3>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card">
                <div class="text-muted small">Pending Cashouts</div>
                <h3 class="fw-bold text-danger"><?php echo $pending_with; ?></h3>
            </div>
        </div>
    </div>

    <div class="card mt-4 border-0 shadow-sm" style="border-radius:15px;">
        <div class="card-header bg-white fw-bold">Deposit Requests</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>User</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT d.*, u.phone FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status='pending'");
                    while($r = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $r['phone']; ?><br><small class="text-muted"><?php echo $r['sender_name']; ?></small></td>
                        <td class="fw-bold">₦<?php echo number_format($r['amount']); ?></td>
                        <td><a href="?approve=<?php echo $r['id']; ?>" class="btn btn-success btn-sm px-3">Approve</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
