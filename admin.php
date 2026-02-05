<?php
session_start();
include 'api/db.php';
if(isset($_GET['approve'])){
    $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM deposits WHERE id = '".$_GET['approve']."'"));
    $uid = $d['user_id']; $amt = $d['amount'];
    mysqli_query($conn, "UPDATE users SET balance = balance + $amt WHERE id = $uid");
    mysqli_query($conn, "UPDATE deposits SET status = 'done' WHERE id = '".$_GET['approve']."'");
    
    // Pay 15% Referral Bonus
    $u = mysqli_fetch_assoc(mysqli_query($conn, "SELECT referred_by FROM users WHERE id = $uid"));
    if($u['referred_by']){
        $ref = $u['referred_by']; $bonus = $amt * 0.15;
        mysqli_query($conn, "UPDATE users SET balance = balance + $bonus WHERE id = $ref");
    }
    header("Location: admin.php");
}
?>
<!DOCTYPE html>
<html>
<head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
    <h3>Prime Luxe Admin</h3>
    <table class="table table-striped mt-4">
        <tr class="table-dark"><th>User ID</th><th>Amount</th><th>Action</th></tr>
        <?php $res = mysqli_query($conn, "SELECT * FROM deposits WHERE status = 'pending'");
        while($r = mysqli_fetch_assoc($res)) echo "<tr><td>#{$r['user_id']}</td><td>₦{$r['amount']}</td><td><a href='?approve={$r['id']}' class='btn btn-success'>Approve</a></td></tr>"; ?>
    </table>
</body>
</html>
