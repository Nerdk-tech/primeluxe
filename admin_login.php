<?php
session_start();
if(isset($_POST['admin_login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // username and password here
    if($user == "Admin_Prime" && $pass == "Luxe_2026_Secure") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin.php");
    } else {
        $error = "Wrong Access Key!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #001f3f; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: white; padding: 30px; border-radius: 20px; width: 100%; max-width: 350px; border-top: 5px solid #D4AF37; }
    </style>
</head>
<body>
    <div class="login-card shadow-lg">
        <div class="text-center mb-4">
            <h4 class="fw-bold">Admin Login</h4>
            <p class="small text-muted">Enter master credentials</p>
        </div>
        <?php if(isset($error)) echo "<div class='alert alert-danger p-2 small'>$error</div>"; ?>
        <form method="POST">
            <input type="text" name="username" class="form-control mb-3" placeholder="Username" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button name="admin_login" class="btn btn-dark w-100 py-2 fw-bold">ENTER VAULT</button>
        </form>
    </div>
</body>
</html>
