<?php
session_start();
include 'api/db.php';

if(!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if(isset($_POST['pay'])){
    $amt = mysqli_real_escape_string($conn, $_POST['amt']);
    $sender_name = mysqli_real_escape_string($conn, $_POST['sender_name']);
    $uid = $_SESSION['user_id'];

    $bank_name = "Moniepoint";
    $account_number = "5072969609";
    $account_name = "Mercy Nnena Patrick";

    $sql = "INSERT INTO deposits 
        (user_id, amount, sender_name, bank_name, account_number, account_name, status) 
        VALUES 
        ('$uid', '$amt', '$sender_name', '$bank_name', '$account_number', '$account_name', 'pending')";

    if(mysqli_query($conn, $sql)){
        $m = "✅ Proof submitted! Wait for admin confirmation.";
    } else {
        $m = "❌ Error submitting proof. Try again.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Deposit | Prime Luxe</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root { --navy:#001f3f; --gold:#D4AF37; }
.bank-card {
  background:#111;
  color:#ffc107;
  border-radius:18px;
  border-left:5px solid var(--gold);
}
</style>
</head>
<body class="bg-light p-4">

<h4 class="fw-bold mb-3">Deposit Funds</h4>

<div class="card p-3 bank-card mb-4 shadow">
  <small class="text-white opacity-75">Transfer exactly to:</small>
  <h6 class="mt-2 mb-1">Bank: Moniepoint</h6>
  <h4 class="fw-bold">5072969609</h4>
  <h6 class="mb-2">Name: Mercy Nnena Patrick</h6>
  <div style="font-size:11px;" class="text-white opacity-75">
    ⚠️ Send only once. Contact support if not approved within 24 hours.
  </div>
</div>

<?php if(isset($m)) echo "<div class='alert alert-info py-2 small fw-bold text-center'>$m</div>"; ?>

<form method="POST" class="bg-white p-3 rounded shadow-sm">
  <label class="small fw-bold text-muted">Amount Sent (₦)</label>
  <input type="number" name="amt" class="form-control mb-3" min="1000" required>

  <label class="small fw-bold text-muted">Your Bank Account Name</label>
  <input type="text" name="sender_name" class="form-control mb-4" placeholder="Name on your transfer" required>

  <button name="pay" class="btn btn-primary w-100 fw-bold py-2">I HAVE PAID</button>
</form>

<div class="text-center mt-3">
  <a href="dashboard.php" class="text-decoration-none text-muted small">← Back to Dashboard</a>
</div>

</body>
</html>