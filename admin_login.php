<?php
session_start();
include 'api/db.php';

/* =====================
   PLAIN TEXT CREDENTIALS
   Username: Ella
   Password: Ella_2026
===================== */
$correct_user = "Ella";
$correct_pass = "Ella_2026";

// If already logged in, skip to dashboard
if(isset($_SESSION['admin'])){
    header("Location: admin.php");
    exit();
}

if(isset($_POST['admin_login'])){
    $input_user = trim($_POST['username']);
    $input_pass = trim($_POST['password']);

    // Direct comparison (No hashing)
    if($input_user === $correct_user && $input_pass === $correct_pass){
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "❌ Incorrect identifier or security key.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Vault | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: radial-gradient(circle at center, #001f3f 0%, #000 100%); height: 100vh; display: flex; align-items: center; justify-content: center; font-family: sans-serif; margin:0; }
        .login-card { background: #fff; padding: 40px 30px; border-radius: 25px; width: 90%; max-width: 380px; box-shadow: 0 15px 35px rgba(0,0,0,0.5); border-bottom: 5px solid var(--gold); }
        .vault-icon { font-size: 50px; color: var(--navy); margin-bottom: 10px; }
        .btn-vault { background: var(--navy); color: white; border: none; padding: 12px; border-radius: 12px; font-weight: bold; width: 100%; }
        .form-control { border-radius: 10px; background: #f8f9fa; }
    </style>
</head>
<body>
<div class="login-card text-center">
    <div class="vault-icon"><i class="bi bi-shield-lock-fill"></i></div>
    <h4 class="fw-bold text-dark">ADMIN VAULT</h4>
    <p class="small text-muted mb-4">Ella's Command Center</p>
    
    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2 small"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3 text-start">
            <label class="small fw-bold text-muted ms-1">IDENTIFIER</label>
            <input type="text" name="username" class="form-control" placeholder="Ella" required>
        </div>
        <div class="mb-4 text-start">
            <label class="small fw-bold text-muted ms-1">SECURITY KEY</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button name="admin_login" class="btn btn-vault shadow">UNSEAL VAULT</button>
    </form>
</div>
</body>
</html>
