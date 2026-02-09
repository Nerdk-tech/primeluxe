<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* --- APPROVAL ENGINE --- */
if(isset($_GET['approve'])){
    $dep_id = (int) $_GET['approve'];
    mysqli_begin_transaction($conn);

    try {
        $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id='$dep_id' FOR UPDATE"));

        if($d && $d['status'] === 'pending'){
            $uid = (int)$d['user_id'];
            $amt = (float)$d['amount'];

            // 1. Update User Balance & Deposit Status
            mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
            mysqli_query($conn, "UPDATE deposits SET status='approved' WHERE id='$dep_id'");
            
            // 2. Referral Commission (L1: 25%, L2: 3%)
            $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id='$uid'"));
            if(!empty($u['referred_by'])){
                $lvl1_id = (int)$u['referred_by'];
                $comm1 = round($amt * 0.25, 2);
                mysqli_query($conn, "UPDATE users SET balance = balance + $comm1 WHERE id='$lvl1_id'");

                $u2 = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id='$lvl1_id'"));
                if(!empty($u2['referred_by'])){
                    $lvl2_id = (int)$u2['referred_by'];
                    $comm2 = round($amt * 0.03, 2);
                    mysqli_query($conn, "UPDATE users SET balance = balance + $comm2 WHERE id='$lvl2_id'");
                }
            }

            mysqli_commit($conn);
            header("Location: admin.php?success=Approved");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        header("Location: admin.php?error=Failed");
    }
    exit();
}

// Fetch Stats
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
        body { background:#f0f2f5; font-family: sans-serif; }
        .admin-nav { background: #001f3f; border-bottom: 4px solid #D4AF37; padding: 15px; color: white; }
        .stat-card { background: white; border-radius: 15px; padding: 20px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
<nav class="admin-nav d-flex justify-content-between">
    <h5 class="m-0">PRIME LUXE ADMIN</h5>
    <a href="api/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
</nav>

<div class="container mt-4">
    <div class="row g-3">
        <div class="col-6">
            <div class="stat-card">
                <i class="bi bi-wallet2 text-success fs-3"></i>
                <div class="small text-muted">Pending Deposits</div>
                <h3><?php echo $pending_dep; ?></h3>
            </div>
        </div>
        <div class="col-6">
            <div class="stat-card">
                <i class="bi bi-cash-stack text-danger fs-3"></i>
                <div class="small text-muted">Pending Cashouts</div>
                <h3><?php echo $pending_with; ?></h3>
            </div>
        </div>
    </div>

    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header bg-white fw-bold">Pending Approval List</div>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>User</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT d.*, u.phone FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status='pending'");
                    while($r = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $r['phone']; ?><br><small class="text-muted"><?php echo $r['sender_name']; ?></small></td>
                        <td>₦<?php echo number_format($r['amount']); ?></td>
                        <td><a href="?approve=<?php echo $r['id']; ?>" class="btn btn-success btn-sm">APPROVE</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
