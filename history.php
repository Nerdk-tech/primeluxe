<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }
$uid = $_SESSION['user_id'];

// Fetch all activities (Deposits, Withdrawals, and Investment Growth)
// We use a UNION to combine different tables into one list
$query = "(SELECT 'Deposit' as type, amount, status, created_at FROM deposits WHERE user_id = $uid)
          UNION
          (SELECT 'Withdrawal' as type, amount, status, created_at FROM withdrawals WHERE user_id = $uid)
          UNION
          (SELECT 'VIP Profit' as type, amount_invested as amount, status, created_at FROM investments WHERE user_id = $uid)
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f4f7f6; }
        .header { background: var(--navy); color: white; padding: 20px; border-bottom: 4px solid var(--gold); }
        .type-badge { font-size: 10px; text-transform: uppercase; padding: 4px 8px; border-radius: 5px; }
        .bg-profit { background: #e8f5e9; color: #2e7d32; }
    </style>
</head>
<body>
    <div class="header text-center">
        <h5 class="fw-bold mb-0">Transaction History</h5>
    </div>

    <div class="container mt-3">
        <?php while($row = mysqli_fetch_assoc($result)): 
            $color = ($row['type'] == 'Withdrawal') ? 'text-danger' : 'text-success';
            $sign = ($row['type'] == 'Withdrawal') ? '-' : '+';
        ?>
        <div class="card mb-2 border-0 shadow-sm" style="border-radius: 12px;">
            <div class="card-body d-flex justify-content-between align-items-center py-2">
                <div>
                    <span class="type-badge <?php echo ($row['type'] == 'VIP Profit') ? 'bg-profit' : 'bg-light text-dark'; ?>">
                        <?php echo $row['type']; ?>
                    </span>
                    <div class="small text-muted mt-1"><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></div>
                </div>
                <div class="text-end">
                    <div class="fw-bold <?php echo $color; ?>"><?php echo $sign; ?> ₦<?php echo number_format($row['amount'], 2); ?></div>
                    <div class="small opacity-75"><?php echo ucfirst($row['status']); ?></div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
        
        <?php if(mysqli_num_rows($result) == 0) echo "<p class='text-center mt-5 text-muted'>No transactions yet.</p>"; ?>
        
        <a href="dashboard.php" class="btn btn-dark w-100 mt-4 rounded-pill">Back to Dashboard</a>
    </div>
</body>
</html>
