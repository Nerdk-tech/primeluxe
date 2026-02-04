<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy: #001f3f; --gold: #D4AF37; }
        body { background: var(--navy); color: white; font-family: sans-serif; min-height: 100vh; }
        .auth-card { background: white; color: #333; border-radius: 20px; padding: 30px; margin-top: 30px; border-bottom: 5px solid var(--gold); }
        .btn-gold { background: var(--gold); color: white; font-weight: bold; width: 100%; border-radius: 10px; border: none; }
        .form-control { border-radius: 10px; background: #f8f9fa; margin-bottom: 15px; }
        .text-gold { color: var(--gold); }
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
                <h4 class="text-center fw-bold mb-4">Login</h4>
            </div>
            
            <form action="api/auth.php" method="POST">
                <div id="username-field" style="display: none;">
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
            const actionBtn = document.querySelector('#auth-action');
            const toggleText = document.querySelector('#toggle-text');
            const userField = document.getElementById('username-field');

            if (title.innerText === 'Login') {
                title.innerText = 'Create Account';
                userField.style.display = 'block';
                actionBtn.innerHTML = '<button type="submit" name="register" class="btn btn-gold py-2">CREATE ACCOUNT</button>';
                toggleText.innerHTML = 'Already have an account? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold">Login</a>';
            } else {
                title.innerText = 'Login';
                userField.style.display = 'none';
                actionBtn.innerHTML = '<button type="submit" name="login" class="btn btn-gold py-2">LOG IN</button>';
                toggleText.innerHTML = 'Don\'t have an account? <a href="javascript:void(0)" onclick="toggleAuth()" class="text-primary fw-bold">Register</a>';
            }
        }
    </script>
</body>
</html>
