<?php
session_start();
include 'api/db.php';

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
            mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
            mysqli_query($conn, "UPDATE deposits SET status='approved' WHERE id='$dep_id'");
            
            // Referral Logic
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
            header("Location: admin.php?msg=Deposit Approved");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
    }
}

/* --- WITHDRAWAL APPROVAL LOGIC --- */
if(isset($_GET['pay_withdrawal'])){
    $with_id = (int)$_GET['pay_withdrawal'];
    // Simply mark as completed because money was deducted during request
    mysqli_query($conn, "UPDATE withdrawals SET status='completed' WHERE id='$with_id'");
    header("Location: admin.php?msg=Withdrawal Marked Paid");
    exit();
}

/* --- WITHDRAWAL REJECTION (REFUND) LOGIC --- */
if(isset($_GET['reject_withdrawal'])){
    $with_id = (int)$_GET['reject_withdrawal'];
    mysqli_begin_transaction($conn);
    try {
        $w = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM withdrawals WHERE id='$with_id' FOR UPDATE"));
        if($w && $w['status'] === 'pending'){
            $uid = $w['user_id'];
            $amt = $w['amount'];
            // Refund the money to the user
            mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id='$uid'");
            mysqli_query($conn, "UPDATE withdrawals SET status='rejected' WHERE id='$with_id'");
            mysqli_commit($conn);
            header("Location: admin.php?msg=Withdrawal Rejected & Refunded");
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
    }
    exit();
}

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
        .table-card { background: white; border-radius: 15px; border:none; margin-bottom: 2rem; }
    </style>
</head>
<body>

<div class="top-nav d-flex justify-content-between">
    <h5 class="m-0 fw-bold">PRIME LUXE ADMIN</h5>
    <a href="api/logout.php" class="btn btn-sm btn-outline-light">Logout</a>
</div>

<div class="container mt-4">
    <div class="row g-3 text-center">
        <div class="col-6"><div class="stat-card"><div class="text-muted small">Pending Deposits</div><h3 class="fw-bold text-success"><?php echo $pending_dep; ?></h3></div></div>
        <div class="col-6"><div class="stat-card"><div class="text-muted small">Pending Cashouts</div><h3 class="fw-bold text-danger"><?php echo $pending_with; ?></h3></div></div>
    </div>

    <div class="card table-card mt-4 shadow-sm">
        <div class="card-header bg-white fw-bold py-3"><i class="bi bi-wallet2 me-2"></i>Deposit Requests</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>User/Sender</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $res = mysqli_query($conn, "SELECT d.*, u.phone FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status='pending'");
                    while($r = mysqli_fetch_assoc($res)): ?>
                    <tr>
                        <td><?php echo $r['phone']; ?><br><small class="text-muted"><?php echo $r['sender_name']; ?></small></td>
                        <td class="fw-bold text-success">₦<?php echo number_format($r['amount']); ?></td>
                        <td><a href="?approve=<?php echo $r['id']; ?>" class="btn btn-success btn-sm px-3">Approve</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card table-card shadow-sm">
        <div class="card-header bg-white fw-bold py-3 text-danger"><i class="bi bi-bank me-2"></i>Withdrawal Requests</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light"><tr><th>User / Bank Info</th><th>Amount</th><th>Action</th></tr></thead>
                <tbody>
                    <?php
                    $res_w = mysqli_query($conn, "SELECT w.*, u.phone FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.status='pending' ORDER BY w.id DESC");
                    while($w = mysqli_fetch_assoc($res_w)): ?>
                    <tr>
                        <td>
                            <span class="fw-bold"><?php echo $w['phone']; ?></span><br>
                            <small class="text-muted">
                                <?php echo $w['bank_name']; ?> | <?php echo $w['account_number']; ?><br>
                                <b><?php echo $w['account_name']; ?></b>
                            </small>
                        </td>
                        <td class="fw-bold text-danger">₦<?php echo number_format($w['amount']); ?></td>
                        <td>
                            <div class="d-flex gap-2">
                                <a href="?pay_withdrawal=<?php echo $w['id']; ?>" class="btn btn-primary btn-sm">Paid</a>
                                <a href="?reject_withdrawal=<?php echo $w['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Refund money to user and reject?')">Reject</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
