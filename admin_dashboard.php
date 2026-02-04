<?php
include 'api/db.php';
session_start();

// Simple Admin Check (Change these for real security!)
$admin_user = "Admin_Prime";
$admin_pass = "Luxe_2026_Secure";

// Check if all tables exist by pulling data
$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
$withdrawals = mysqli_query($conn, "SELECT w.*, u.phone FROM withdrawals w JOIN users u ON w.user_id = u.id ORDER BY w.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>PrimeLuxe Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>body { background: #121212; color: white; }</style>
</head>
<body class="p-4">
    <h2 class="text-warning">Admin Command Center</h2>
    <hr class="bg-warning">

    <div class="row">
        <div class="col-md-6">
            <h3>Registered Users</h3>
            <table class="table table-dark table-striped">
                <thead><tr><th>Phone</th><th>Balance</th><th>Joined</th></tr></thead>
                <tbody>
                    <?php while($u = mysqli_fetch_assoc($users)): ?>
                    <tr><td><?php echo $u['phone']; ?></td><td>₦<?php echo $u['balance']; ?></td><td><?php echo $u['created_at']; ?></td></tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <div class="col-md-6">
            <h3>Withdrawal Requests</h3>
            <table class="table table-dark table-hover">
                <thead><tr><th>User</th><th>Amount</th><th>Bank</th><th>Acc No.</th><th>Status</th></tr></thead>
                <tbody>
                    <?php while($w = mysqli_fetch_assoc($withdrawals)): ?>
                    <tr>
                        <td><?php echo $w['phone']; ?></td>
                        <td class="text-danger">₦<?php echo $w['amount']; ?></td>
                        <td><?php echo $w['bank_name']; ?></td>
                        <td><?php echo $w['account_number']; ?></td>
                        <td><span class="badge bg-warning"><?php echo $w['status']; ?></span></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
