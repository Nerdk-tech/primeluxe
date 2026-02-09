<?php
session_start();
include 'api/db.php';

/* ======================
   ADMIN AUTH CHECK
====================== */
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* ======================
   DASHBOARD STATS
====================== */
$total_dep = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT IFNULL(SUM(amount),0) total FROM deposits WHERE status='approved'"
))['total'];

$total_with = mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT IFNULL(SUM(amount),0) total FROM withdrawals WHERE status='completed'"
))['total'];

$pending_dep = mysqli_num_rows(mysqli_query($conn,
    "SELECT id FROM deposits WHERE status='pending'"
));

$pending_with = mysqli_num_rows(mysqli_query($conn,
    "SELECT id FROM withdrawals WHERE status='pending'"
));

/* ======================
   APPROVE DEPOSIT
====================== */
if(isset($_GET['approve'])){
    $dep_id = (int) $_GET['approve'];

    mysqli_begin_transaction($conn);

    $d = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM deposits WHERE id='$dep_id' FOR UPDATE"
    ));

    if(!$d || $d['status'] !== 'pending'){
        mysqli_rollback($conn);
        header("Location: admin.php?err=InvalidRequest");
        exit();
    }

    $uid = (int)$d['user_id'];
    $amt = (float)$d['amount'];

    /* Credit user */
    mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
    mysqli_query($conn, "UPDATE deposits SET status='approved', approved_at=NOW() WHERE id='$dep_id'");

    /* ======================
       REFERRAL COMMISSIONS
    ====================== */
    $u = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT referred_by FROM users WHERE id='$uid'"
    ));

    if(!empty($u['referred_by'])){
        $lvl1 = (int)$u['referred_by'];
        $bonus1 = round($amt * 0.25, 2);
        mysqli_query($conn, "UPDATE users SET balance = balance + $bonus1 WHERE id='$lvl1'");

        $u2 = mysqli_fetch_assoc(mysqli_query($conn,
            "SELECT referred_by FROM users WHERE id='$lvl1'"
        ));

        if(!empty($u2['referred_by'])){
            $lvl2 = (int)$u2['referred_by'];
            $bonus2 = round($amt * 0.03, 2);
            mysqli_query($conn, "UPDATE users SET balance = balance + $bonus2 WHERE id='$lvl2'");
        }
    }

    mysqli_commit($conn);
    header("Location: admin.php?msg=DepositApproved");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prime Luxe Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f4f7f6; }
        .nav-btn { border-radius: 12px; font-weight: bold; }
        .stat-card { border-radius: 15px; border:none; }
        .sender-badge { background:#e3f2fd; color:#0d47a1; padding:3px 8px; border-radius:6px; font-size:11px; font-weight:bold; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container text-center">
        <span class="navbar-brand mx-auto fw-bold text-warning">PRIME LUXE COMMAND</span>
    </div>
</nav>

<div class="container">

    <!-- NAV BUTTONS -->
    <div class="row g-2 mb-4 text-center">
        <div class="col-6">
            <a href="admin.php" class="btn btn-warning w-100 py-3 nav-btn shadow-sm">
                💰 DEPOSITS 
                <span class="badge bg-danger"><?php echo $pending_dep; ?></span>
            </a>
        </div>
        <div class="col-6">
            <a href="admin_withdrawals.php" class="btn btn-dark w-100 py-3 nav-btn shadow-sm">
                🏦 CASH-OUTS 
                <span class="badge bg-danger"><?php echo $pending_with; ?></span>
            </a>
        </div>
    </div>

    <!-- STATS -->
    <div class="row mb-4 text-center">
        <div class="col-6">
            <div class="card stat-card bg-success text-white p-3 shadow-sm">
                <small class="opacity-75">Total Revenue</small>
                <h6 class="mb-0">₦<?php echo number_format($total_dep,2); ?></h6>
            </div>
        </div>
        <div class="col-6">
            <div class="card stat-card bg-primary text-white p-3 shadow-sm">
                <small class="opacity-75">Total Paid</small>
                <h6 class="mb-0">₦<?php echo number_format($total_with,2); ?></h6>
            </div>
        </div>
    </div>

    <!-- PENDING DEPOSITS -->
    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-muted small">PENDING DEPOSITS</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>USER / SENDER</th>
                            <th>AMOUNT</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn,
                            "SELECT d.*, u.phone 
                             FROM deposits d 
                             JOIN users u ON d.user_id = u.id 
                             WHERE d.status='pending' 
                             ORDER BY d.id DESC"
                        );

                        if(mysqli_num_rows($res) > 0):
                            while($r = mysqli_fetch_assoc($res)):
                        ?>
                        <tr>
                            <td>
                                <div class="fw-bold" style="font-size:13px;"><?php echo htmlspecialchars($r['phone']); ?></div>
                                <div class="sender-badge">NAME: <?php echo htmlspecialchars($r['sender_name'] ?? 'N/A'); ?></div>
                            </td>
                            <td class="fw-bold text-success">₦<?php echo number_format($r['amount'],2); ?></td>
                            <td>
                                <a href="?approve=<?php echo $r['id']; ?>"
                                   onclick="return confirm('Approve ₦<?php echo number_format($r['amount'],2); ?> deposit?')"
                                   class="btn btn-sm btn-success fw-bold rounded-pill px-3">
                                   APPROVE
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">No pending deposits ☕</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

</body>
</html>