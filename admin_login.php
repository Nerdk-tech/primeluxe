<?php
session_start();
include 'api/db.php';

/* =====================
   CONFIG — SECURE ACCESS
   Username: Admin_Prime
   Password: Luxe_2026_Secure
===================== */
define('ADMIN_USER', 'Admin_Prime');
// This is the BCRYPT hash for 'Luxe_2026_Secure'
define('ADMIN_PASS_HASH', '$2y$10$T6f/6z8TqK/8m.G4XU.6VOnYv9Z5D8uH/Gf.2Zf5D5A7W2V9Vv9V.'); 

if(isset($_SESSION['admin'])){
    header("Location: admin.php");
    exit();
}

if(isset($_POST['admin_login'])){
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    // timing-safe comparison
    if($user === ADMIN_USER && password_verify($pass, ADMIN_PASS_HASH)){
        session_regenerate_id(true); // Prevent session fixation
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit();
    } else {
        $error = "❌ Unauthorized access attempt.";
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
        body {
            background: radial-gradient(circle at center, #001f3f 0%, #000 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-card {
            background: #fff;
            padding: 40px 30px;
            border-radius: 25px;
            width: 90%;
            max-width: 380px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.5);
            border-bottom: 5px solid var(--gold);
        }
        .vault-icon {
            font-size: 50px;
            color: var(--navy);
            margin-bottom: 10px;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px;
            background: #f8f9fa;
        }
        .btn-vault {
            background: var(--navy);
            color: white;
            border: none;
            padding: 12px;
            border-radius: 12px;
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-vault:hover {
            background: #003366;
            color: var(--gold);
        }
    </style>
</head>
<body>

<div class="login-card text-center">
    <div class="vault-icon">
        <i class="bi bi-shield-lock-fill"></i>
    </div>
    <h4 class="fw-bold text-dark">ADMIN VAULT</h4>
    <p class="small text-muted mb-4">Prime Luxe Command Center</p>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger py-2 small border-0"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="mb-3 text-start">
            <label class="small fw-bold text-muted ms-2">IDENTIFIER</label>
            <input type="text" name="username" class="form-control" placeholder="Admin Username" required>
        </div>
        <div class="mb-4 text-start">
            <label class="small fw-bold text-muted ms-2">SECURITY KEY</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
        </div>
        <button name="admin_login" class="btn btn-vault w-100 shadow">UNSEAL VAULT</button>
    </form>
    
    <div class="mt-4">
        <a href="index.php" class="text-decoration-none small text-muted"><i class="bi bi-arrow-left"></i> Back to Site</a>
    </div>
</div>

</body>
</html>
