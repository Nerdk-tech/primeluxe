<?php
session_start();
include 'api/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$u_id = $_SESSION['user_id'];
$query = mysqli_query($conn, "SELECT balance FROM users WHERE id = '$u_id'");
$user = mysqli_fetch_assoc($query);
$current_balance = $user['balance'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Withdraw - Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; font-family: sans-serif; }
        .top-bar { background: var(--navy); color: white; padding: 20px; border-bottom: 4px solid var(--gold); border-radius: 0 0 25px 25px; }
        .withdraw-card { border-radius: 20px; border: none; margin-top: -20px; }
        .btn-gold { background: var(--gold); color: white; font-weight: bold; border-radius: 10px; border: none; }
        .btn-gold:hover { background: #b8962d; color: white; }
        .balance-display { background: #fff; border: 1px solid #eee; border-radius: 15px; padding: 15px; margin-bottom: 20px; }
    </style>
</head>
<body>

    <div class="top-bar text-center">
        <h4 class="fw-bold m-0">WITHDRAWAL</h4>
    </div>

    <div class="container mt-4">
        <div class="card withdraw-card shadow-lg p-4">
            
            <div class="balance-display text-center shadow-sm">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Withdrawable Balance</small>
                <h2 class="fw-bold text-dark mb-0">₦<?php echo number_format($current_balance, 2); ?></h2>
            </div>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger p-2 small text-center"><?php echo $_GET['error']; ?></div>
            <?php endif; ?>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert alert-success p-2 small text-center"><?php echo $_GET['success']; ?></div>
            <?php endif; ?>

            <form action="api/process_withdraw.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $u_id; ?>">
                
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Select Bank</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. OPay, PalmPay, Zenith" required>
                </div>
                
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Account Number</label>
                    <input type="number" name="account_number" class="form-control" placeholder="10 Digits" required>
                </div>
                
                <div class="mb-3">
                    <label class="small fw-bold text-muted">Amount (Min ₦1,000)</label>
                    <input type="number" name="amount" class="form-control" min="1000" placeholder="0.00" required>
                </div>
                
                <div class="alert alert-warning py-2 px-3" style="font-size: 11px; border-radius: 10px;">
                    <strong>Note:</strong> Withdrawals are processed within 24 hours. Ensure your bank details are correct.
                </div>
                
                <button type="submit" class="btn btn-gold w-100 py-3 mb-3 shadow">SUBMIT REQUEST</button>
                <a href="dashboard.php" class="btn btn-light w-100 text-muted fw-bold">CANCEL</a>
            </form>
        </div>
    </div>

</body>
</html>
