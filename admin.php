<?php
session_start();
include 'api/db.php';

// Stats for Ella's dashboard
$total_dep = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM deposits WHERE status = 'done'"))['total'] ?? 0;
$total_with = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(amount) as total FROM withdrawals WHERE status = 'completed'"))['total'] ?? 0;
$pending_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM deposits WHERE status = 'pending'"));
$pending_with_count = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM withdrawals WHERE status = 'pending'"));

if(isset($_GET['approve'])){
    $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id = '".$_GET['approve']."'"));
    $uid = $d['user_id']; 
    $amt = $d['amount'];

    mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = $uid");
    mysqli_query($conn, "UPDATE deposits SET status = 'done' WHERE id = '".$_GET['approve']."'");
    
    // Level 1 (25%) and Level 2 (3%) logic
    $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $uid"));
    $lvl1_parent = $user_data['referred_by'];

    if($lvl1_parent){
        $bonus1 = $amt * 0.25;
        mysqli_query($conn, "UPDATE users SET balance = balance + $bonus1 WHERE id = $lvl1_parent");

        $lvl1_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $lvl1_parent"));
        $lvl2_parent = $lvl1_data['referred_by'];

        if($lvl2_parent){
            $bonus2 = $amt * 0.03;
            mysqli_query($conn, "UPDATE users SET balance = balance + $bonus2 WHERE id = $lvl2_parent");
        }
    }
    header("Location: admin.php?msg=Deposit Approved");
    exit();
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
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark shadow-sm mb-4">
    <div class="container text-center">
        <span class="navbar-brand mx-auto fw-bold text-warning">PRIME LUXE COMMAND CENTER</span>
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
                🏦 WITHDRAWALS 
                <span class="badge bg-danger"><?php echo $pending_with_count; ?></span>
            </a>
        </div>
    </div>

    <div class="row mb-4 text-center">
        <div class="col-6">
            <div class="card stat-card bg-success text-white p-2 shadow-sm">
                <small>Total In</small>
                <h6 class="mb-0">₦<?php echo number_format($total_dep); ?></h6>
            </div>
        </div>
        <div class="col-6">
            <div class="card stat-card bg-primary text-white p-2 shadow-sm">
                <small>Total Out</small>
                <h6 class="mb-0">₦<?php echo number_format($total_with); ?></h6>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 20px;">
        <div class="card-body">
            <h6 class="fw-bold mb-3 text-muted">PENDING DEPOSITS</h6>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr class="small text-muted">
                            <th>USER</th>
                            <th>AMOUNT</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT * FROM deposits WHERE status = 'pending' ORDER BY id DESC");
                        while($r = mysqli_fetch_assoc($res)) {
                            echo "<tr>
                                    <td><small class='fw-bold'>#{$r['user_id']}</small></td>
                                    <td><span class='text-success fw-bold'>₦" . number_format($r['amount']) . "</span></td>
                                    <td>
                                        <a href='?approve={$r['id']}' class='btn btn-sm btn-success px-3 fw-bold rounded-pill shadow-sm'>APPROVE</a>
                                    </td>
                                  </tr>";
                        }
                        if(mysqli_num_rows($res) == 0) echo "<tr><td colspan='3' class='text-center py-4 text-muted'>No work for now! ☕</td></tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
