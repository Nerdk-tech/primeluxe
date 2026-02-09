<?php
session_start();
include 'api/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* Block non-investors - Ella's rule: must have an active investment to withdraw */
$inv_check = mysqli_query($conn, "SELECT id FROM investments WHERE user_id = '$uid' AND status = 'active' LIMIT 1");
if(mysqli_num_rows($inv_check) == 0) {
    header("Location: dashboard.php?error=You must have an active investment before withdrawing.");
    exit();
}

/* Fetch balance */
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT balance FROM users WHERE id = '$uid'"));
$current_balance = $user['balance'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Withdraw | Prime Luxe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
:root { --navy:#001f3f; --gold:#D4AF37; }
body { background:#f8f9fa; font-family:sans-serif; }

.top-bar {
  background:linear-gradient(135deg,var(--navy),#003366);
  color:white;
  padding:25px 20px 45px;
  border-radius:0 0 30px 30px;
}

.withdraw-card {
  border-radius:20px;
  border:none;
  margin-top:-30px;
}

.btn-gold {
  background:linear-gradient(135deg,#d4af37,#8f6b1f);
  color:#000;
  font-weight:800;
  border-radius:15px;
  border:none;
}

.balance-display {
  background:#fff;
  border:1px solid #e0e0e0;
  border-radius:18px;
  padding:20px;
  margin-bottom:20px;
}
</style>
</head>
<body>

<div class="top-bar text-center">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <a href="dashboard.php" class="text-white fs-4"><i class="bi bi-arrow-left"></i></a>
        <h5 class="fw-bold m-0">WITHDRAWAL</h5>
        <div style="width:24px;"></div>
    </div>
</div>

<div class="container mt-2">
  <div class="card withdraw-card shadow-lg p-4 mb-5">

    <div class="balance-display text-center shadow-sm">
      <small class="text-muted fw-bold text-uppercase" style="font-size:11px; letter-spacing:1px;">Withdrawable Balance</small>
      <h2 class="fw-bold mb-0 text-navy">₦<?php echo number_format($current_balance, 2); ?></h2>
    </div>

    <?php if(isset($_GET['error'])): ?>
      <div class="alert alert-danger small text-center border-0 shadow-sm"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
      <div class="alert alert-success small text-center border-0 shadow-sm"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <form action="api/process_withdraw.php" method="POST">
      <div class="mb-3">
        <label class="small fw-bold text-muted">Bank Name</label>
        <input type="text" name="bank_name" class="form-control form-control-lg fs-6" placeholder="e.g. OPay, PalmPay, UBA" required>
      </div>

      <div class="mb-3">
        <label class="small fw-bold text-muted">Account Name</label>
        <input type="text" name="account_name" class="form-control form-control-lg fs-6" placeholder="Full name on account" required>
      </div>

      <div class="mb-3">
        <label class="small fw-bold text-muted">Account Number</label>
        <input type="number" name="account_number" class="form-control form-control-lg fs-6" placeholder="0123456789" required>
      </div>

      <div class="mb-4">
        <label class="small fw-bold text-muted">Amount (Minimum ₦400)</label>
        <input type="number" name="amount" class="form-control form-control-lg fs-6" min="400" max="<?php echo floor($current_balance); ?>" required>
        <div class="form-text" style="font-size:11px;">Max you can withdraw: ₦<?php echo floor($current_balance); ?></div>
      </div>

      <div class="alert alert-info py-2 px-3 small rounded-3 border-0">
        <i class="bi bi-info-circle-fill me-1"></i> Processing time: 1 - 24 hours.
      </div>

      <button type="submit" class="btn btn-gold w-100 py-3 mb-3 shadow">SUBMIT WITHDRAWAL</button>
    </form>

  </div>
</div>

</body>
</html>
