<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) { header("Location: index.php"); exit(); }

$uid = $_SESSION['user_id'];
// Fetch current user data to show existing bank info
$u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT bank_name, account_number, account_name FROM users WHERE id = '$uid'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank Settings | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; }
        body { background:#f4f7f6; font-family: sans-serif; padding-bottom:30px; }
        .top-nav { background: var(--navy); color: white; padding: 15px; border-bottom: 3px solid var(--gold); }
        .card { border-radius: 15px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .btn-gold { background: var(--gold); color: white; font-weight: bold; border-radius: 30px; }
        .current-info { background: #eef2f7; border-radius: 10px; padding: 15px; font-size: 14px; }
    </style>
</head>
<body>

<div class="top-nav">
    <div class="container d-flex align-items-center">
        <a href="settings.php" class="text-white me-3"><i class="bi bi-chevron-left fs-4"></i></a>
        <h6 class="mb-0 fw-bold">Bank Settings</h6>
    </div>
</div>

<div class="container mt-4">
    <?php if(isset($_GET['success'])): ?>
        <div class="alert alert-success py-2 small"><?php echo $_GET['success']; ?></div>
    <?php endif; ?>

    <div class="card p-3 mb-4">
        <h6 class="fw-bold mb-3">Current Withdrawal Info</h6>
        <div class="current-info">
            <div class="mb-1 text-muted small">Bank Name:</div>
            <div class="fw-bold mb-2"><?php echo $u['bank_name'] ?: 'Not Set'; ?></div>
            
            <div class="mb-1 text-muted small">Account Number:</div>
            <div class="fw-bold mb-2"><?php echo $u['account_number'] ?: 'Not Set'; ?></div>
            
            <div class="mb-1 text-muted small">Account Name:</div>
            <div class="fw-bold"><?php echo $u['account_name'] ?: 'Not Set'; ?></div>
        </div>
    </div>

    <div class="card p-4">
        <h6 class="fw-bold mb-3">Update Bank Details</h6>
        <form action="api/save_bank.php" method="POST">
            <div class="mb-3">
                <label class="small text-muted fw-bold">Bank Name</label>
                <input type="text" name="bank_name" class="form-control" placeholder="OPay, PalmPay, Kuda..." required>
            </div>
            <div class="mb-3">
                <label class="small text-muted fw-bold">Account Number</label>
                <input type="number" name="account_number" class="form-control" placeholder="0123456789" required>
            </div>
            <div class="mb-4">
                <label class="small text-muted fw-bold">Account Name</label>
                <input type="text" name="account_name" class="form-control" placeholder="Full name on account" required>
            </div>
            <button type="submit" class="btn btn-gold w-100 p-2 shadow-sm">Save Bank Information</button>
        </form>
    </div>
    
    <p class="text-center text-muted small mt-4">
        <i class="bi bi-shield-lock"></i> Secured payment gateway encryption
    </p>
</div>

</body>
</html>
