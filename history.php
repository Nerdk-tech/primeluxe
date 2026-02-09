<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['user_id'];

/**
 * We fetch three types of records:
 * 1. Deposits (Money added)
 * 2. Withdrawals (Money taken out)
 * 3. Daily Profit (The actual earnings from their VIP plans)
 */
$query = "(SELECT 'Deposit' as type, amount, status, created_at FROM deposits WHERE user_id = $uid)
          UNION
          (SELECT 'Withdrawal' as type, amount, status, created_at FROM withdrawals WHERE user_id = $uid)
          UNION
          (SELECT 'Daily Profit' as type, daily_income as amount, 'received' as status, created_at FROM investments WHERE user_id = $uid)
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Log | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; --success: #28a745; --danger: #dc3545; }
        body { background: #f8f9fa; font-family: sans-serif; }
        .top-header { background: var(--navy); color: white; padding: 20px; border-bottom: 4px solid var(--gold); border-radius: 0 0 20px 20px; }
        .history-card { border-radius: 15px; border: none; transition: transform 0.2s; }
        .history-card:active { transform: scale(0.98); }
        .icon-box { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        .bg-deposit { background: #e7f3ff; color: #007bff; }
        .bg-withdraw { background: #fff0f0; color: var(--danger); }
        .bg-profit { background: #e8f5e9; color: var(--success); }
        .status-pill { font-size: 10px; padding: 2px 10px; border-radius: 20px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="top-header text-center shadow">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <a href="dashboard.php" class="text-white"><i class="bi bi-chevron-left"></i></a>
            <h6 class="fw-bold m-0">Transaction History</h6>
            <div style="width:20px;"></div>
        </div>
    </div>

    <div class="container mt-4">
        <?php if(mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): 
                // Logic for icons and colors
                if($row['type'] == 'Deposit') {
                    $icon = 'bi-plus-circle'; $bg = 'bg-deposit'; $sign = '+'; $amtColor = 'text-primary';
                } elseif($row['type'] == 'Withdrawal') {
                    $icon = 'bi-dash-circle'; $bg = 'bg-withdraw'; $sign = '-'; $amtColor = 'text-danger';
                } else {
                    $icon = 'bi-graph-up-arrow'; $bg = 'bg-profit'; $sign = '+'; $amtColor = 'text-success';
                }
            ?>
            <div class="card history-card shadow-sm mb-3">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box <?php echo $bg; ?> me-3">
                        <i class="bi <?php echo $icon; ?>"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0 fw-bold"><?php echo $row['type']; ?></h6>
                        <small class="text-muted" style="font-size: 11px;">
                            <?php echo date('M d, Y • h:i A', strtotime($row['created_at'])); ?>
                        </small>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold <?php echo $amtColor; ?>">
                            <?php echo $sign; ?> ₦<?php echo number_format($row['amount'], 2); ?>
                        </div>
                        <span class="status-pill <?php echo ($row['status'] == 'pending') ? 'bg-warning text-dark' : 'bg-light text-muted'; ?>">
                            <?php echo strtoupper($row['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="text-center mt-5">
                <i class="bi bi-clock-history text-muted" style="font-size: 50px;"></i>
                <p class="text-muted mt-2">Your transaction history is empty.</p>
                <a href="dashboard.php" class="btn btn-primary rounded-pill px-4 mt-3">Start Investing</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>
