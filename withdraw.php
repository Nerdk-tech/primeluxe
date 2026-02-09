<?php
session_start();
include 'api/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$uid = $_SESSION['user_id'];

/* Block non-investors */
$inv_check = mysqli_query($conn, "SELECT id FROM investments WHERE user_id = '$uid' AND status = 'active' LIMIT 1");
if(mysqli_num_rows($inv_check) == 0) {
    header("Location: dashboard.php?error=You must invest before withdrawing.");
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
<style>
:root { --navy:#001f3f; --gold:#D4AF37; }
body { background:#f8f9fa; font-family:sans-serif; }

.top-bar {
  background:linear-gradient(135deg,var(--navy),#003366);
  color:white;
  padding:20px;
  border-radius:0 0 25px 25px;
}

.withdraw-card {
  border-radius:20px;
  border:none;
  margin-top:-20px;
}

.btn-gold {
  background:linear-gradient(135deg,#d4af37,#8f6b1f);
  color:#000;
  font-weight:800;
  border-radius:12px;
  border:none;
}

.balance-display {
  background:#fff;
  border:1px solid #eee;
  border-radius:15px;
  padding:15px;
  margin-bottom:18px;
}
</style>
</head>
<body>

<div class="top-bar text-center">
  <h4 class="fw-bold m-0">WITHDRAWAL</h4>
</div>

<div class="container mt-4">
  <div class="card withdraw-card shadow-lg p-4">

    <div class="balance-display text-center shadow-sm">
      <small class="text-muted fw-bold text-uppercase" style="font-size:11px;">Withdrawable Balance</small>
      <h2 class="fw-bold mb-0">₦<?php echo number_format($current_balance, 2); ?></h2>
    </div>

    <?php if(isset($_GET['error'])): ?>
      <div class="alert alert-danger small text-center"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <?php if(isset($_GET['success'])): ?>
      <div class="alert alert-success small text-center"><?php echo htmlspecialchars($_GET['success']); ?></div>
    <?php endif; ?>

    <form action="api/process_withdraw.php" method="POST">
      <input type="hidden" name="user_id" value="<?php echo $uid; ?>">

      <div class="mb-3">
        <label class="small fw-bold text-muted">Bank Name</label>
        <input type="text" name="bank_name" class="form-control" placeholder="e.g. OPay, PalmPay, Zenith" required>
      </div>

      <div class="mb-3">
        <label class="small fw-bold text-muted">Account Name</label>
        <input type="text" name="account_name" class="form-control" placeholder="Account holder name" required>
      </div>

      <div class="mb-3">
        <label class="small fw-bold text-muted">Account Number</label>
        <input type="number" name="account_number" class="form-control" placeholder="10 digits" required>
      </div>

      <div class="mb-3">
        <label class="small fw-bold text-muted">Amount (Minimum ₦1,000)</label>
        <input type="number" name="amount" class="form-control" min="1000" max="<?php echo floor($current_balance); ?>" required>
      </div>

      <div class="alert alert-warning py-2 px-3 small rounded-3">
        Withdrawals are processed within 24 hours. Ensure your bank details are correct.
      </div>

      <button type="submit" class="btn btn-gold w-100 py-3 mb-3 shadow">SUBMIT REQUEST</button>
      <a href="dashboard.php" class="btn btn-light w-100 fw-bold text-muted">CANCEL</a>
    </form>

  </div>
</div>

</body>
</html>