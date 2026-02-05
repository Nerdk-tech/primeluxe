<?php
session_start();
include 'api/db.php';
if(isset($_POST['pay'])){
    mysqli_query($conn, "INSERT INTO deposits (user_id, amount) VALUES ('".$_SESSION['user_id']."', '".$_POST['amt']."')");
    $m = "Sent! Wait for Admin approval.";
}
?>
<!DOCTYPE html>
<html>
<head><meta name="viewport" content="width=device-width, initial-scale=1.0"><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="bg-light p-4">
    <h4 class="fw-bold">Transfer to Bank</h4>
    <div class="card p-3 bg-dark text-warning mb-3 shadow">
        <h6>Bank: Moniepoint</h6>
        <h5>Acc No: 9030046245</h5>
        <h6>Name: Mercy Nnena Patrick</h6>
        <h7>Only send money once till Former one is approved. Contact support if deposit is not approved in 24hrs</h7>
    </div>
    <?php if(isset($m)) echo "<div class='alert alert-success'>$m</div>"; ?>
    <form method="POST">
        <input type="number" name="amt" class="form-control mb-3" placeholder="Enter Amount Sent" required>
        <button name="pay" class="btn btn-primary w-100 fw-bold">I HAVE PAID</button>
    </form>
    <a href="dashboard.php" class="btn btn-link w-100 text-dark">Back</a>
</body>
</html>
