<?php
session_start();
include 'api/db.php';

// Check if Admin is logged in (Optional but recommended)
// if(!isset($_SESSION['admin'])) { header("Location: admin_login.php"); exit(); }

if(isset($_GET['approve'])){
    $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id = '".$_GET['approve']."'"));
    $uid = $d['user_id']; 
    $amt = $d['amount'];

    // 1. Update User's Balance with their deposit
    mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = $uid");
    
    // 2. Mark deposit as completed
    mysqli_query($conn, "UPDATE deposits SET status = 'done' WHERE id = '".$_GET['approve']."'");
    
    // 3. LEVEL 1 REFERRAL BONUS (25%)
    $user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $uid"));
    $lvl1_parent = $user_data['referred_by'];

    if($lvl1_parent){
        $bonus1 = $amt * 0.25; // 25% from the chart
        mysqli_query($conn, "UPDATE users SET balance = balance + $bonus1 WHERE id = $lvl1_parent");

        // 4. LEVEL 2 REFERRAL BONUS (3%)
        $lvl1_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $lvl1_parent"));
        $lvl2_parent = $lvl1_data['referred_by'];

        if($lvl2_parent){
            $bonus2 = $amt * 0.03; // 3% from the chart
            mysqli_query($conn, "UPDATE users SET balance = balance + $bonus2 WHERE id = $lvl2_parent");
        }
    }
    
    header("Location: admin.php?msg=Approved and Bonuses Paid");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Prime Luxe | Admin Panel</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1">Prime Luxe Admin</span>
        </div>
    </nav>

    <div class="container">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Pending Deposits</h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>User ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $res = mysqli_query($conn, "SELECT * FROM deposits WHERE status = 'pending' ORDER BY id DESC");
                            if(mysqli_num_rows($res) > 0){
                                while($r = mysqli_fetch_assoc($res)) {
                                    echo "<tr>
                                            <td><span class='badge bg-secondary'>#{$r['user_id']}</span></td>
                                            <td class='fw-bold'>₦" . number_format($r['amount'], 2) . "</td>
                                            <td><span class='badge bg-warning text-dark'>Pending</span></td>
                                            <td>
                                                <a href='?approve={$r['id']}' class='btn btn-success btn-sm px-3 fw-bold' onclick=\"return confirm('Confirm deposit of ₦{$r['amount']}?')\">Approve</a>
                                            </td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4' class='text-center py-4 text-muted'>No pending deposits found.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="text-center">
            <a href="dashboard.php" class="btn btn-outline-dark btn-sm">Return to Dashboard</a>
        </div>
    </div>
</body>
</html>
