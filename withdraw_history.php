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
    <title>Withdrawal History | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; font-family: sans-serif; padding-bottom: 50px; }
        
        .top-bar { 
            background: var(--navy); 
            color: white; 
            padding: 30px 15px 50px; 
            border-bottom: 4px solid var(--gold); 
            border-radius: 0 0 30px 30px; 
        }
        
        .history-container { margin-top: -30px; }
        
        .history-card { 
            background: white; 
            border-radius: 18px; 
            border: none; 
            margin-bottom: 15px; 
            border-left: 4px solid var(--navy);
        }

        .status-badge {
            font-size: 10px;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 800;
            text-transform: uppercase;
        }
        
        .st-pending { background: #fff8e1; color: #f57f17; }
        .st-completed { background: #e8f5e9; color: #2e7d32; }
        .st-rejected { background: #ffebee; color: #c62828; }
        
        .amount-text { color: var(--navy); font-size: 18px; font-weight: 800; }
    </style>
</head>
<body>

<div class="top-bar text-center shadow">
    <div class="d-flex justify-content-between align-items-center mb-2 px-2">
        <a href="settings.php" class="text-white fs-4"><i class="bi bi-chevron-left"></i></a>
        <h5 class="fw-bold m-0">Withdrawal Logs</h5>
        <div style="width:24px;"></div>
    </div>
    <p class="small opacity-75 mb-0">Monitor your payment requests</p>
</div>

<div class="container history-container">
    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)):
            $status = strtolower($row['status']);
            $badgeClass = 'st-pending';
            if($status == 'completed' || $status == 'success') $badgeClass = 'st-completed';
            if($status == 'rejected' || $status == 'failed') $badgeClass = 'st-rejected';
        ?>
            <div class="card history-card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <div class="amount-text">₦<?php echo number_format($row['amount'], 2); ?></div>
                            <small class="text-muted fw-bold"><?php echo htmlspecialchars($row['bank_name']); ?></small>
                        </div>
                        <span class="status-badge <?php echo $badgeClass; ?>">
                            <?php echo $status; ?>
                        </span>
                    </div>
                    
                    <div class="pt-2 border-top mt-2">
                        <div class="d-flex justify-content-between">
                            <small class="text-muted" style="font-size:11px;">
                                <i class="bi bi-person me-1"></i><?php echo htmlspecialchars($row['account_name']); ?>
                            </small>
                            <small class="text-muted" style="font-size:11px;">
                                <i class="bi bi-hash me-1"></i><?php echo htmlspecialchars($row['account_number']); ?>
                            </small>
                        </div>
                        <div class="text-muted mt-1" style="font-size:10px;">
                            <i class="bi bi-clock me-1"></i><?php echo date('M d, Y • h:i A', strtotime($row['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center mt-5 bg-white p-5 rounded-4 shadow-sm">
            <i class="bi bi-wallet2 text-muted opacity-25" style="font-size: 60px;"></i>
            <p class="text-muted mt-3">You haven't made any withdrawals yet.</p>
            <a href="withdraw.php" class="btn btn-primary rounded-pill px-4 btn-sm">Make First Withdrawal</a>
        </div>
    <?php endif; ?>

    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-navy w-100 py-3 rounded-pill shadow fw-bold text-white text-decoration-none d-block">
            BACK TO DASHBOARD
        </a>
    </div>
</div>



</body>
</html>
