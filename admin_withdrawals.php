<?php
session_start();
include 'api/db.php';

// --- APPROVAL LOGIC ---
if(isset($_GET['approve_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['approve_id']);
    // Update status to completed
    mysqli_query($conn, "UPDATE withdrawals SET status = 'completed' WHERE id = '$id'");
    header("Location: admin_withdrawals.php?msg=Withdrawal Marked as Paid");
    exit();
}

// --- REJECTION LOGIC (Refunds user balance) ---
if(isset($_GET['reject_id'])){
    $id = mysqli_real_escape_string($conn, $_GET['reject_id']);
    
    // Fetch withdrawal details to know who and how much to refund
    $w_query = mysqli_query($conn, "SELECT user_id, amount FROM withdrawals WHERE id = '$id'");
    $w = mysqli_fetch_assoc($w_query);
    
    if($w) {
        $uid = $w['user_id']; 
        $amt = $w['amount'];
        
        // Return money to user balance
        mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = '$uid'");
        // Mark withdrawal as rejected
        mysqli_query($conn, "UPDATE withdrawals SET status = 'rejected' WHERE id = '$id'");
        header("Location: admin_withdrawals.php?msg=Rejected and Refunded");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ella's Pay Center</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f4f7f6; font-family: sans-serif; }
        .table-card { border-radius: 15px; overflow: hidden; border: none; }
        .btn-paid { background: #28a745; color: white; border: none; font-weight: bold; }
        .btn-paid:hover { background: #218838; }
        .alert-msg { background: var(--navy); color: var(--gold); border: none; border-radius: 10px; }
    </style>
</head>
<body class="p-2">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-3 px-2">
            <div>
                <h4 class="fw-bold mb-0">Withdrawal Portal</h4>
                <small class="text-muted">Pay users their earnings</small>
            </div>
            <a href="admin.php" class="btn btn-outline-dark btn-sm fw-bold">Deposit Admin</a>
        </div>

        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-msg small py-2 text-center shadow-sm mb-4">
                ✅ <?php echo $_GET['msg']; ?>
            </div>
        <?php endif; ?>

        <div class="card table-card shadow-sm">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-dark">
                        <tr style="font-size: 12px;">
                            <th>USER</th>
                            <th>BANK DETAILS</th>
                            <th>NET TO PAY (15% OFF)</th>
                            <th>ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $res = mysqli_query($conn, "SELECT w.*, u.phone FROM withdrawals w JOIN users u ON w.user_id = u.id WHERE w.status = 'pending' ORDER BY w.id DESC");
                        if(mysqli_num_rows($res) > 0){
                            while($r = mysqli_fetch_assoc($res)) {
                                $fee = $r['amount'] * 0.15; 
                                $to_pay = $r['amount'] - $fee;
                                ?>
                                <tr>
                                    <td><span class="fw-bold"><?php echo substr($r['phone'], 0, 4); ?>...</span></td>
                                    <td style="line-height: 1.2;">
                                        <div class="fw-bold text-primary"><?php echo $r['account_number']; ?></div>
                                        <div class="small text-muted text-uppercase"><?php echo $r['bank_name']; ?></div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">₦<?php echo number_format($to_pay); ?></div>
                                        <div style="font-size: 10px;" class="text-muted">Gross: ₦<?php echo number_format($r['amount']); ?></div>
                                    </td>
                                    <td>
                                        <div class="d-grid gap-1">
                                            <a href="?approve_id=<?php echo $r['id']; ?>" class="btn btn-paid btn-sm" onclick="return confirm('Confirm payment of ₦<?php echo number_format($to_pay); ?>?')">MARK PAID</a>
                                            <a href="?reject_id=<?php echo $r['id']; ?>" class="btn btn-outline-danger btn-sm" style="font-size: 10px;">REJECT/REFUND</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-5 text-muted'>No pending cashouts for now.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
