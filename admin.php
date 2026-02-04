<?php
session_start();
// Security Check: Only allow logged-in Admin
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
include 'api/db.php';

// Handle Payout Approval
if (isset($_GET['approve_id'])) {
    $id = $_GET['approve_id'];
    mysqli_query($conn, "UPDATE withdrawals SET status = 'completed' WHERE id = '$id'");
    header("Location: admin.php?msg=Paid Successfully");
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
$withdrawals = mysqli_query($conn, "SELECT withdrawals.*, users.phone FROM withdrawals JOIN users ON withdrawals.user_id = users.id WHERE withdrawals.status = 'pending'");
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: #f4f7f6; font-family: sans-serif; }
        .admin-header { background: var(--navy); color: var(--gold); padding: 20px; border-bottom: 4px solid var(--gold); }
    </style>
</head>
<body>
    <div class="admin-header d-flex justify-content-between align-items-center px-4">
        <h4 class="m-0 fw-bold">PRIME LUXE ADMIN</h4>
        <a href="logout_admin.php" class="btn btn-outline-danger btn-sm">LOGOUT</a>
    </div>

    <div class="container mt-4">
        <h5 class="fw-bold">Pending Withdrawals</h5>
        <table class="table table-dark table-striped">
            <thead><tr><th>Phone</th><th>Amount</th><th>Bank Info</th><th>Action</th></tr></thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($withdrawals)): ?>
                <tr>
                    <td><?php echo $row['phone']; ?></td>
                    <td class="text-warning">₦<?php echo number_format($row['amount'], 2); ?></td>
                    <td><small><?php echo $row['bank_name']; ?> (<?php echo $row['account_number']; ?>)</small></td>
                    <td><a href="admin.php?approve_id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm">APPROVE</a></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h5 class="mt-5 fw-bold">User Database</h5>
        <div class="list-group">
            <?php while($u = mysqli_fetch_assoc($users)): ?>
            <div class="list-group-item d-flex justify-content-between">
                <span><?php echo $u['phone']; ?></span>
                <span class="badge bg-primary">₦<?php echo number_format($u['balance'], 2); ?></span>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</body>
</html>
