<?php
session_start();
include 'api/db.php';

// SIMPLE ADMIN AUTH
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* ======================
   DASHBOARD STATS
====================== */
$total_dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount),0) total FROM deposits WHERE status='approved'"))['total'];
$total_with = mysqli_fetch_assoc(mysqli_query($conn, "SELECT IFNULL(SUM(amount),0) total FROM withdrawals WHERE status='completed'"))['total'];
$pending_dep = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM deposits WHERE status='pending'"));
$pending_with = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM withdrawals WHERE status='pending'"));

/* ======================
   THE APPROVAL ENGINE
====================== */
if(isset($_GET['approve'])){
    $dep_id = (int) $_GET['approve'];

    // Start Transaction to ensure data integrity
    mysqli_begin_transaction($conn);

    $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id='$dep_id' FOR UPDATE"));

    if($d && $d['status'] === 'pending'){
        $uid = (int)$d['user_id'];
        $amt = (float)$d['amount'];

        // 1. Credit the User who deposited
        mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
        
        // 2. Mark deposit as approved
        mysqli_query($conn, "UPDATE deposits SET status='approved' WHERE id='$dep_id'");

        /* ======================
           REFERRAL LOGIC (L1: 25%, L2: 3%)
        ====================== */
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
        header("Location: admin.php?msg=Success");
    } else {
        mysqli_rollback($conn);
        header("Location: admin.php?err=AlreadyProcessed");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prime Luxe Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body { background:#f0f2f5; font-family: sans-serif; }
        .admin-nav { background: #001f3f; border-bottom: 4px solid #D4AF37; }
        .stat-box { border-radius: 15px; border: none; transition: 0.3s; }
        .stat-box:hover { transform: translateY(-5px); }
        .table-card { border-radius: 20px; border: none; overflow: hidden; }
        .btn-approve { border-radius: 10px; font-weight: bold; background: #28a745; border: none; color: white; }
    </style>
</head>
<body>

<nav class="admin-nav p-3 mb-4 shadow">
    <div class="container d-flex justify-content-between align-items-center">
        <h5 class="text-white fw-bold m-0"><i class="bi bi-shield-lock me-2"></i>ADMIN PANEL</h5>
        <a href="api/logout.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</a>
    </div>
</nav>

<div class="container">
    <div class="row g-3 mb-4 text-center">
        <div class="col-6">
            <div class="card stat-box bg-white p-3 shadow-sm">
                <i class="bi bi-arrow-down-circle text-success fs-3"></i>
                <div class="small fw-bold text-muted mt-1">Pending Deposits</div>
                <h4 class="fw-bold mb-0"><?php echo $pending_dep; ?></h4>
            </div>
        </div>
        <div class="col-6">
            <a href="admin_withdrawals.php" class="text-decoration-none">
                <div class="card stat-box bg-white p-3 shadow-sm">
                    <i class="bi bi-arrow-up-circle text-danger fs-3"></i>
                    <div class="small fw-bold text-muted mt-1">Pending Cashouts</div>
                    <h4 class="fw-bold mb-0 text-dark"><?php echo $pending_with; ?></h4>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card bg-dark text-white p-3 shadow-sm border-0" style="border-radius:15px; background: linear-gradient(45deg, #001f3f, #003366);">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="opacity-75">Total Approved Deposits</small>
                        <h3 class="fw-bold mb-0">₦<?php echo number_format($total_dep, 2); ?></h3>
                    </div>
                    <i class="bi bi-graph-up-arrow fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold m-0"><i class="bi bi-clock-history me-2 text-warning"></i>PENDING APPROVALS</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="small text-muted">
                            <th class="ps-3">USER INFO</th>
                            <th>AMOUNT</th>
                            <th class="text-center">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $res = mysqli_query($conn, "SELECT d.*, u.phone FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status='pending' ORDER BY d.id DESC");
                        if(mysqli_num_rows($res) > 0):
                            while($r = mysqli_fetch_assoc($res)):
                        ?>
                        <tr>
                            <td class="ps-3">
                                <div class="fw-bold"><?php echo htmlspecialchars($r['phone']); ?></div>
                                <div class="text-muted" style="font-size: 11px;">Sender: <?php echo htmlspecialchars($r['sender_name']); ?></div>
                            </td>
                            <td><span class="badge bg-success-subtle text-success fs-6">₦<?php echo number_format($r['amount']); ?></span></td>
                            <td class="text-center">
                                <a href="?approve=<?php echo $r['id']; ?>" class="btn btn-sm btn-approve px-3 shadow-sm" onclick="return confirm('Confirm receipt of ₦<?php echo $r['amount']; ?>?')">
                                    APPROVE
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; else: ?>
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">
                                <i class="bi bi-check2-all fs-1 d-block opacity-25"></i>
                                All deposits cleared!
                            </td>
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
