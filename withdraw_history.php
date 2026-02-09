<?php
session_start();
include 'api/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$u_id = $_SESSION['user_id'];

// Fetch only withdrawal records for this user
$query = "SELECT * FROM withdrawals WHERE user_id = '$u_id' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdrawal History - Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f4f7f6; font-family: sans-serif; }
        .top-bar { background: var(--navy); color: white; padding: 25px 15px; border-bottom: 4px solid var(--gold); border-radius: 0 0 25px 25px; }
        .history-card { background: white; border-radius: 15px; border: none; margin-bottom: 12px; transition: 0.3s; }
        .status-pending { background: #fff3cd; color: #856404; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: bold; }
        .status-completed { background: #d4edda; color: #155724; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: bold; }
        .status-rejected { background: #f8d7da; color: #721c24; font-size: 11px; padding: 3px 10px; border-radius: 20px; font-weight: bold; }
    </style>
</head>
<body>

<div class="top-bar text-center shadow">
    <h4 class="fw-bold m-0">WITHDRAWAL LOGS</h4>
    <p class="small opacity-75 mb-0">Track your cashouts</p>
</div>

<div class="container mt-4">
<?php if(mysqli_num_rows($result) > 0): ?>
<?php while($row = mysqli_fetch_assoc($result)):
    $statusClass = 'status-pending';
    if($row['status'] == 'completed') $statusClass = 'status-completed';
    if($row['status'] == 'rejected') $statusClass = 'status-rejected';
?>
    <div class="card history-card shadow-sm">
        <div class="card-body d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-1">₦<?php echo number_format($row['amount'], 2); ?></h6>
                <small class="text-muted d-block" style="font-size:11px;">
                    <?php echo htmlspecialchars($row['bank_name']); ?> • 
                    <?php echo htmlspecialchars($row['account_number']); ?>
                </small>
                <small class="text-muted d-block" style="font-size:11px;">
                    <?php echo htmlspecialchars($row['account_name']); ?>
                </small>
                <small class="text-muted" style="font-size:10px;">
                    <?php echo date('M d, Y - h:i A', strtotime($row['created_at'])); ?>
                </small>
            </div>
            <div class="text-end">
                <span class="<?php echo $statusClass; ?>">
                    <?php echo strtoupper($row['status']); ?>
                </span>
            </div>
        </div>
    </div>
<?php endwhile; ?>
<?php else: ?>
    <div class="text-center mt-5">
        <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" width="80" class="opacity-25 mb-3">
        <p class="text-muted">No withdrawal history found.</p>
        <a href="withdraw.php" class="btn btn-outline-primary rounded-pill px-4">Withdraw Now</a>
    </div>
<?php endif; ?>

    <div class="mt-4 text-center">
        <a href="dashboard.php" class="btn btn-dark w-100 py-2 rounded-pill shadow">BACK TO DASHBOARD</a>
    </div>
</div>

</body>
</html>