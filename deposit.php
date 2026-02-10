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

    // ELLA'S SPECIFIC BANK DETAILS
    $bank_name = "Moniepoint";
    $account_number = "5072969609";
    $account_name = "Mercy Nnena Patrick";

    if($amt < 400) {
        $m = "❌ Minimum deposit is ₦400.";
    } else {
        // FIXED SQL: Matches the columns we added in Step 1
        $sql = "INSERT INTO deposits 
            (user_id, amount, bank_name, account_number, account_name, sender_name, status) 
            VALUES 
            ('$uid', '$amt', '$bank_name', '$account_number', '$account_name', '$sender_name', 'pending')";

        if(mysqli_query($conn, $sql)){
            $m = "✅ Proof submitted! Admin will confirm within 2-24 hours.";
        } else {
            // This will show exactly what is wrong if it still fails
            $m = "❌ System Error: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deposit | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy:#001f3f; --gold:#D4AF37; }
        body { background: #f8f9fa; font-family: sans-serif; padding-bottom: 50px; }
        .bank-card {
            background: linear-gradient(135deg, #1a1a1a, #000);
            color: #ffc107;
            border-radius: 18px;
            border-left: 6px solid var(--gold);
            padding: 25px;
        }
        .form-control { border-radius: 10px; padding: 12px; }
        .btn-pay { background: var(--navy); color: white; border: none; border-radius: 10px; padding: 12px; font-weight: bold; }
        .support-box {
            background: #e7f3ff;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #b3d7ff;
        }
    </style>
</head>
<body class="p-3">

<div class="d-flex align-items-center mb-4">
    <a href="dashboard.php" class="text-dark me-3"><i class="bi bi-chevron-left fs-4"></i></a>
    <h4 class="fw-bold m-0">Recharge Account</h4>
</div>

<div class="bank-card mb-4 shadow-lg">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <small class="text-white opacity-75 text-uppercase fw-bold" style="font-size: 10px; letter-spacing: 1px;">Official Payment Account</small>
        <img src="https://img.icons8.com/color/48/visa.png" width="30">
    </div>
    <h6 class="text-white mb-1" style="font-size: 14px;">Bank: Moniepoint</h6>
    <h2 class="fw-bold mb-2" style="letter-spacing: 1.5px;">5072969609</h2>
    <h6 class="text-white opacity-75 mb-3">Name: Mercy Nnena Patrick</h6>
    <hr style="background: rgba(255,255,255,0.1);">
    <div class="small text-white opacity-50 text-center">
        <i class="bi bi-info-circle-fill me-1"></i> Send once and provide proof below.
    </div>
</div>

<?php if(isset($m)): ?>
    <div class="alert <?php echo strpos($m, '✅') !== false ? 'alert-success' : 'alert-danger'; ?> py-3 small fw-bold text-center shadow-sm">
        <?php echo $m; ?>
    </div>
<?php endif; ?>

<form method="POST" class="bg-white p-4 rounded-4 shadow-sm mb-4">
    <div class="mb-3">
        <label class="small fw-bold text-muted mb-2">Amount Sent (₦)</label>
        <input type="number" name="amt" class="form-control" placeholder="Min 400" min="400" required>
    </div>
    <div class="mb-4">
        <label class="small fw-bold text-muted mb-2">Sender's Account Name</label>
        <input type="text" name="sender_name" class="form-control" placeholder="Name on your bank app" required>
    </div>
    <button name="pay" class="btn btn-primary w-100 btn-pay shadow-sm">I HAVE TRANSFERRED</button>
</form>

<div class="support-box text-center mb-4">
    <p class="small text-muted mb-2">Having trouble with your deposit?</p>
    <a href="https://wa.me/2348077502802" class="btn btn-outline-primary btn-sm rounded-pill px-4 fw-bold">
        <i class="bi bi-whatsapp me-1"></i> Contact Support
    </a>
</div>

</body>
</html>
