<?php
session_start();
include 'api/db.php';

/* =====================
   CONFIG — CHANGE ONCE
===================== */
define('ADMIN_USER', 'Admin_Prime');
define('ADMIN_PASS_HASH', '$2y$10$J3y5p7sFvK6H2lH5OZ3r9.5C0qQnYF6u8WqR6z8P0wJcD9KJHqZt6'); 
// Password = Luxe_2026_Secure

/* =====================
   REDIRECT IF LOGGED IN
===================== */
if(isset($_SESSION['admin'])){
    header("Location: admin.php");
    exit();
}

/* =====================
   LOGIN HANDLER
===================== */
if(isset($_POST['admin_login'])){
    $user = trim($_POST['username']);
    $pass = $_POST['password'];

    if(hash_equals(ADMIN_USER, $user) && password_verify($pass, ADMIN_PASS_HASH)){
        session_regenerate_id(true);
        $_SESSION['admin'] = true;
        $_SESSION['admin_user'] = $user;
        header("Location: admin.php");
        exit();
    } else {
        $error = "❌ Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Prime Luxe Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{
  background:linear-gradient(135deg,#001f3f,#000814);
  height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
}
.login-card{
  background:white;
  padding:32px;
  border-radius:22px;
  width:100%;
  max-width:360px;
  border-top:6px solid #D4AF37;
}
</style>
</head>
<body>

<div class="login-card shadow-lg">
  <div class="text-center mb-4">
    <h4 class="fw-bold">Admin Vault</h4>
    <small class="text-muted">Restricted Access</small>
  </div>

  <?php if(isset($error)): ?>
    <div class="alert alert-danger py-2 small text-center"><?php echo $error; ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <input type="text" name="username" class="form-control mb-3" placeholder="Admin Username" required>
    <input type="password" name="password" class="form-control mb-3" placeholder="Admin Password" required>
    <button name="admin_login" class="btn btn-dark w-100 fw-bold py-2 rounded-pill">ENTER VAULT</button>
  </form>
</div>

</body>
</html>