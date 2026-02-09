<?php
session_start();
include 'api/db.php';

/* ======================
   ADMIN AUTH GUARD
====================== */
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}

/* ======================
   APPROVE WITHDRAWAL
====================== */
if(isset($_GET['approve_id'])){
    $id = (int)$_GET['approve_id'];

    mysqli_begin_transaction($conn);

    $w = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT user_id, amount, status FROM withdrawals WHERE id = $id FOR UPDATE"
    ));

    if($w && $w['status'] === 'pending'){
        mysqli_query($conn, "UPDATE withdrawals SET status = 'completed' WHERE id = $id");
        mysqli_commit($conn);
        header("Location: admin_withdrawals.php?msg=Withdrawal Paid");
        exit();
    }

    mysqli_rollback($conn);
    header("Location: admin_withdrawals.php?msg=Invalid Request");
    exit();
}

/* ======================
   REJECT & REFUND
====================== */
if(isset($_GET['reject_id'])){
    $id = (int)$_GET['reject_id'];

    mysqli_begin_transaction($conn);

    $w = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT user_id, amount, status FROM withdrawals WHERE id = $id FOR UPDATE"
    ));

    if($w && $w['status'] === 'pending'){
        // Refund the full amount back to the user's balance
        mysqli_query($conn, "UPDATE users SET balance = balance + {$w['amount']} WHERE id = {$w['user_id']}");
        mysqli_query($conn, "UPDATE withdrawals SET status = 'rejected' WHERE id = $id");
        mysqli_commit($conn);
        header("Location: admin_withdrawals.php?msg=Rejected & Refunded");
        exit();
    }

    mysqli_rollback($conn);
    header("Location: admin_withdrawals.php?msg=Invalid Request");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Payouts | Prime Luxe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
:root { --navy:#001f3f; --gold:#D4AF37; }
body { background:#f4f7f6; font-family:sans-serif; }
.payout-card { border-radius:20px; border:none; overflow:hidden; }
.btn-paid { background: #28a745; color:white; border:none; font-weight:bold; border-radius:10px; }
.btn-paid:hover { background: #218838; color:white; }
.fee-badge { font-size: 10px; background: #fff3cd; color: #856404; padding: 2px 8px; border-radius: 5px; font-weight: bold; }
</style>
</head>
<body class="p-2">

<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4 mt-3 px-2">
    <div>
      <h4 class="fw-bold mb-0 text-navy">Withdrawal Portal</h4>
      <small class="text-muted">Total Pending: <span class="badge bg-danger rounded-pill">
        <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT id FROM withdrawals WHERE status='pending'")); ?>
      </span></small>
    </div>
    <div class="d-flex gap-2">
        <a href="admin.php" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm">Deposits</a>
        <a href="api/logout.php" class="btn btn-outline-danger btn-sm rounded-pill"><i class="bi bi-power"></i></a>
    </div>
  </div>

  <?php if(isset($_GET['msg'])): ?>
    <div class="alert bg-white border-0 shadow-sm small py-2 text-center text-primary fw-bold mb-4 rounded-3">
      <i class="bi bi-info-circle me-1"></i> <?php echo htmlspecialchars($_GET['msg']); ?>
    </div>
  <?php endif; ?>

  

  <div class="card payout-card shadow-sm">
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead class="bg-navy text-white" style="background: var(--navy);">
          <tr style="font-size:12px;">
            <th class="py-3 ps-3 text-white">BENEFICIARY</th>
            <th class="py-3 text-white">BANK DETAILS</th>
            <th class="py-3 text-white">NET TO PAY</th>
            <th class="py-3 text-center text-white">ACTION</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $res = mysqli_query($conn,"
            SELECT w.*, u.phone 
            FROM withdrawals w 
            JOIN users u ON w.user_id = u.id 
            WHERE w.status = 'pending' 
            ORDER BY w.id DESC
          ");

          if(mysqli_num_rows($res) > 0){
            while($r = mysqli_fetch_assoc($res)){
              $fee = $r['amount'] * 0.15;
              $to_pay = $r['amount'] - $fee;
              ?>
              <tr>
                <td class="ps-3">
                    <div class="fw-bold"><?php echo htmlspecialchars($r['account_name']); ?></div>
                    <small class="text-muted"><?php echo $r['phone']; ?></small>
                </td>
                <td style="line-height:1.2;">
                  <div class="fw-bold text-navy"><?php echo $r['account_number']; ?></div>
                  <div class="small text-uppercase fw-bold text-muted" style="font-size:10px;"><?php echo $r['bank_name']; ?></div>
                </td>
                <td>
                  <div class="fw-bold text-success" style="font-size:16px;">₦<?php echo number_format($to_pay, 0); ?></div>
                  <div class="fee-badge">-15% Fee (₦<?php echo number_format($fee,0); ?>)</div>
                </td>
                <td class="text-center px-3">
                  <div class="d-grid gap-1">
                    <a href="?approve_id=<?php echo $r['id']; ?>" 
                       class="btn btn-paid btn-sm py-2"
                       onclick="return confirm('Confirm payment of ₦<?php echo number_format($to_pay,2); ?>?')">
                       MARK PAID
                    </a>
                    <a href="?reject_id=<?php echo $r['id']; ?>" 
                       class="text-danger small mt-1 text-decoration-none fw-bold" 
                       onclick="return confirm('Reject and refund ₦<?php echo number_format($r['amount'],2); ?> to user balance?')">
                       REJECT & REFUND
                    </a>
                  </div>
                </td>
              </tr>
              <?php
            }
          } else {
            echo "<tr><td colspan='4' class='text-center py-5 text-muted'>
                <i class='bi bi-cup-hot fs-1 opacity-25 d-block mb-2'></i>
                No pending cashouts. Time for a break!
            </td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

</body>
</html>
