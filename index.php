<?php
session_start();
// Capture referral ID from URL if it exists
$ref = isset($_GET['ref']) ? $_GET['ref'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome | Prime Luxe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: var(--navy); color: white; font-family: sans-serif; min-height: 100vh; }
        .auth-card { background: white; color: #333; border-radius: 20px; padding: 30px; margin-top: 30px; border-bottom: 5px solid var(--gold); }
        .btn-gold { background: var(--gold); color: white; font-weight: bold; width: 100%; border-radius: 10px; border: none; }
        .form-control { border-radius: 10px; background: #f8f9fa; margin-bottom: 15px; }
        .text-gold { color: var(--gold); }
        .bonus-badge { background: #fff3cd; color: #856404; font-size: 12px; padding: 10px; border-radius: 10px; margin-bottom: 20px; border: 1px dashed #ffeeba; }
    </style>
</head>
<body>
    <div class="container">
        <div class="text-center mt-5">
            <h2 class="fw-bold text-gold m-0">PRIME LUXE</h2>
            <p class="small opacity-75 text-white">Invest in Excellence</p>
        </div>

        <div class="card auth-card shadow-lg mx-auto" style="max-width: 400px;">
            <div id="auth-title">
                <h4 class="text-center fw-bold mb-2">Login</h4>
                <p class="text-center text-muted small mb-4">Enter your details to continue</p>
            </div>
            
            <form action="api/auth.php" method="POST">
                <input type="hidden" name="referred_by" value="<?php echo htmlspecialchars($ref); ?>">

                <div id="register-extras" style="display: none;">
                    <div class="bonus-badge text-center">
                        🎁 <strong>Welcome Bonus:</strong> ₦400 will be added to your balance instantly!
                    </div>
                    <label class="small fw-bold">Username</label>
                    <input type="text" name="username" class="form-control" placeholder="Choose a username">
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Phone Number</label>
                    <input type="number" name="phone" class="form-control" placeholder="080XXXXXXXX" required>
                </div>

                <div class="mb-3">
                    <label class="small fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div id="auth-action">
                    <button type="submit" name="login" class="btn btn-gold py-2">LOG IN</button>
                </div>
                
                <div class="text-center mt-3">
                    <small id="toggle-text">Don't have an account? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold">Register</a></small>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleAuth() {
            const title = document.querySelector('#auth-title h4');
            const subTitle = document.querySelector('#auth-title p');
            const actionBtn = document.querySelector('#auth-action');
            const toggleText = document.querySelector('#toggle-text');
            const registerExtras = document.getElementById('register-extras');

            if (title.innerText === 'Login') {
                title.innerText = 'Create Account';
                subTitle.innerText = 'Join Prime Luxe and get ₦400 bonus';
                registerExtras.style.display = 'block';
                actionBtn.innerHTML = '<button type="submit" name="register" class="btn btn-gold py-2">CREATE ACCOUNT</button>';
                toggleText.innerHTML = 'Already have an account? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold">Login</a>';
            } else {
                title.innerText = 'Login';
                subTitle.innerText = 'Enter your details to continue';
                registerExtras.style.display = 'none';
                actionBtn.innerHTML = '<button type="submit" name="login" class="btn btn-gold py-2">LOG IN</button>';
                toggleText.innerHTML = 'Don\'t have an account? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold">Register</a>';
            }
        }

        // Auto-switch to register if ref link is used
        <?php if(!empty($ref)): ?>
            toggleAuth();
        <?php endif; ?>
    </script>
</body>
</html>
