<?php
session_start();
include 'api/db.php';

// Stats for Ella's dashboard
$total_dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM deposits WHERE status = 'done'"))['total'] ?? 0;
$total_with = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM withdrawals WHERE status = 'completed'"))['total'] ?? 0;
$pending_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM deposits WHERE status = 'pending'"));
$pending_with_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM withdrawals WHERE status = 'pending'"));

// APPROVAL LOGIC
if(isset($_GET['approve'])){
    $dep_id = mysqli_real_escape_string($conn, $_GET['approve']);
    $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id = '$dep_id'"));
    
    if($d && $d['status'] == 'pending'){
        $uid = $d['user_id']; 
        $amt = $d['amount'];

        // 1. Give money to user
        mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = $uid");
        mysqli_query($conn, "UPDATE deposits SET status = 'done' WHERE id = '$dep_id'");
        
        // 2. REFERRAL COMMISSION LOGIC
        // 
        $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $uid"));
        $lvl1_parent = $user_data['referred_by'];

        if($lvl1_parent && $lvl1_parent != 'NULL' && $lvl1_parent != ''){
            // Level 1 Bonus (25%)
            $bonus1 = $amt * 0.25;
            mysqli_query($conn, "UPDATE users SET balance = balance + $bonus1 WHERE id = $lvl1_parent");

            // Check for Level 2
            $lvl1_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $lvl1_parent"));
            $lvl2_parent = $lvl1_data['referred_by'];

            if($lvl2_parent && $lvl2_parent != 'NULL' && $lvl2_parent != ''){
                // Level 2 Bonus (3%)
                $bonus2 = $amt * 0.03;
                mysqli_query($conn, "UPDATE users SET balance = balance + $bonus2 WHERE id = $lvl2_parent");
            }
        }
        header("Location: admin.php?msg=DepositApproved");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prime Luxe Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-btn { border-radius: 10px; font-weight: bold; transition: 0.3s; }
        .stat-card { border-radius: 15px; border: none; }
        .sender-badge { background: #e3f2fd; color: #0d47a1; padding: 2px 8px; border-radius: 5px; font-size: 11px; font-weight: bold; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container text-center">
        <span class="navbar-brand mx-auto fw-bold text-warning">PRIME LUXE COMMAND</span>
    </div>
</nav>

<div class="container">
    <div class="row g-2 mb-4 text-center">
        <div class="col-6">
            <a href="admin.php" class="btn btn-warning w-100 py-3 nav-btn shadow-sm">
                💰 DEPOSITS 
                <span class="badge bg-danger"><?php echo $pending_count; ?></span>
            </a>
        </div>
        <div class="col-6">
            <a href="admin_withdrawals.php" class="btn btn-dark w-100 py-3 nav-btn shadow-sm">
                🏦 CASH-OUTS 
                <span class="badge bg-danger"><?php echo $pending_with_count; ?></span>
            </a>
        </div>
    </div>

    <div class="row mb-4 text-center">
        <div class="col-6">
            <div class="card stat-card bg-success text-white p-2 shadow-sm">
                <small class="opacity-75">Total Revenue</small>
                <h6 class="mb-0">₦<?php echo number_format($total_dep); ?></h6>
            </div>
        </div>
        <div class="col-6">
            <div class="card stat-card bg-primary text-white p-2 shadow-sm">
                <small class="opacity-75">Total Paid</small>
                <h6 class="mb-0">₦<?php echo number_format($total_with); ?></h6>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-muted small">PENDING RECHARGES</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>USER / SENDER NAME</th>
                            <th>AMOUNT</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Fetching phone and sender_name to help Ella verify
                        $res = mysqli_query($conn, "SELECT d.*, u.phone FROM deposits d JOIN users u ON d.user_id = u.id WHERE d.status = 'pending' ORDER BY d.id DESC");
                        while($r = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                    <td>
                                        <div class='fw-bold' style='font-size:13px;'>{$r['phone']}</div>
                                        <div class='sender-badge'>NAME: " . ($r['sender_name'] ?? 'NOT GIVEN') . "</div>
                                    </td>
                                    <td><span class='text-success fw-bold'>₦" . number_format($r['amount']) . "</span></td>
                                    <td>
                                        <a href='?approve={$r['id']}' class='btn btn-sm btn-success px-3 fw-bold rounded-pill'>APPROVE</a>
                                    </td>
                                  </tr>";
                        }
                        if(mysqli_num_rows($res) == 0) echo "<tr><td colspan='3' class='text-center py-5 text-muted'>No pending alerts! ☕</td></tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
