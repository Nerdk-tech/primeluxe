<?php
session_start();
include 'api/db.php';

// Approval Logic
if(isset($_GET['approve_id'])){
    $id = $_GET['approve_id'];
    mysqli_query($conn, "UPDATE withdrawals SET status = 'completed' WHERE id = '$id'");
    header("Location: admin_withdrawals.php?msg=Withdrawal Marked as Paid");
    exit();
}

// Rejection Logic (Refunds user balance)
if(isset($_GET['reject_id'])){
    $id = $_GET['reject_id'];
    $w = mysqli_fetch_assoc(mysqli_query($conn, "SELECT user_id, amount FROM withdrawals WHERE id = '$id'"));
    $uid = $w['user_id']; $amt = $w['amount'];
    
    mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = '$uid'");
    mysqli_query($conn, "UPDATE withdrawals SET status = 'rejected' WHERE id = '$id'");
    header("Location: admin_withdrawals.php?msg=Rejected and Refunded");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Withdrawal Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold text-navy">Pending Withdrawals</h4>
            <a href="admin.php" class="btn btn-sm btn-dark">Deposit Admin</a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>User</th>
                            <th>Bank Details</th>
                            <th>Gross (Fee)</th>
                            <th>Net to Pay</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT w.*, u.phone FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.status = 'pending' ORDER BY w.id DESC");
                        if(mysqli_num_rows($res) > 0){
                            while($r = mysqli_fetch_assoc($res)) {
                                $fee = $r['amount'] * 0.15; // 15% Withdrawal Charge
                                $to_pay = $r['amount'] - $fee;
                                echo "<tr>
                                        <td><small>{$r['phone']}</small></td>
                                        <td>
                                            <strong class='text-primary'>{$r['account_number']}</strong><br>
                                            <small class='text-muted'>{$r['bank_name']}</small>
                                        </td>
                                        <td><small class='text-danger'>-₦".number_format($fee)."</small></td>
                                        <td class='fw-bold text-success'>₦".number_format($to_pay)."</td>
                                        <td>
                                            <a href='?approve_id={$r['id']}' class='btn btn-success btn-sm mb-1 w-100' onclick=\"return confirm('Did you pay ₦".number_format($to_pay)." to this account?')\">Paid</a>
                                            <a href='?reject_id={$r['id']}' class='btn btn-outline-danger btn-sm w-100'>Reject</a>
                                        </td>
                                      </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-4'>No pending withdrawals!</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
