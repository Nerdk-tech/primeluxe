<?php
session_start();
include 'api/db.php';
// We assume user_id is stored in session after login
$user_id = $_SESSION['user_id'] ?? 1; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f8f9fa; }
        .withdraw-card { border-radius: 20px; border-top: 5px solid var(--gold); }
        .btn-gold { background: var(--gold); color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="card withdraw-card shadow p-4">
            <h4 class="fw-bold mb-3">Withdraw Funds</h4>
            <p class="small text-muted">Minimum withdrawal is ₦1,000</p>
            
            <form action="api/process_withdraw.php" method="POST">
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                
                <div class="mb-3">
                    <label class="small fw-bold">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. OPay, Kuda, Zenith" required>
                </div>
                
                <div class="mb-3">
                    <label class="small fw-bold">Account Number</label>
                    <input type="number" name="account_number" class="form-control" placeholder="10 Digits" required>
                </div>
                
                <div class="mb-3">
                    <label class="small fw-bold">Amount (₦)</label>
                    <input type="number" name="amount" class="form-control" min="1000" placeholder="0.00" required>
                </div>
                
                <button type="submit" class="btn btn-gold w-100 py-2">SUBMIT REQUEST</button>
                <a href="dashboard.php" class="btn btn-link w-100 mt-2 text-decoration-none text-muted">Go Back</a>
            </form>
        </div>
    </div>
</body>
</html>
