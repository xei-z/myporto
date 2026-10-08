<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Masuk — ZeriKo</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .auth-container {
            max-width: 400px;
            margin: 2rem auto;
            background-color: var(--card-bg);
            padding: 2rem;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }
        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }
        .auth-form input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: #ffffff;
            font-family: var(--font-sans);
            font-size: 0.9rem;
            outline: none;
        }
        .auth-form input:focus {
            border-color: var(--accent-color);
        }
        .auth-form button {
            padding: 0.75rem;
            background-color: var(--text-main);
            color: #ffffff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.9rem;
            transition: background-color 0.2s ease;
        }
        .auth-form button:hover {
            background-color: var(--accent-color);
        }
        .auth-toggle {
            font-size: 0.85rem;
            margin-top: 1rem;
            color: var(--text-muted);
            text-align: center;
        }
        .auth-toggle a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo">ZeriKo.</a>
        <nav><a href="index.php">← Kembali ke Beranda</a></nav>
    </header>

    <div class="auth-container">
        <h2 class="section-title" id="formTitle" style="margin-bottom: 1.5rem;">Masuk Akun</h2>
        
        <!-- FORM LOGIN -->
        <form id="loginForm" class="auth-form">
            <input type="email" id="loginEmail" placeholder="Email Anda" required>
            <input type="password" id="loginPassword" placeholder="Password" required>
            <button type="submit">Login</button>
            <p class="auth-toggle">
                Belum punya akun? <a href="#" id="toRegister">Daftar di sini</a>
            </p>
        </form>

        <!-- FORM REGISTRASI -->
        <form id="registerForm" class="auth-form" style="display: none;">
            <input type="text" id="regName" placeholder="Nama Lengkap" required>
            <input type="email" id="regEmail" placeholder="Email Anda" required>
            <input type="password" id="regPassword" placeholder="Password" required>
            <button type="submit">Daftar Akun</button>
            <p class="auth-toggle">
                Sudah punya akun? <a href="#" id="toLogin">Login di sini</a>
            </p>
        </form>
    </div>

    <script>
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const formTitle = document.getElementById('formTitle');

        document.getElementById('toRegister').addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.style.display = 'none';
            registerForm.style.display = 'flex';
            formTitle.textContent = 'Daftar Member';
        });

        document.getElementById('toLogin').addEventListener('click', (e) => {
            e.preventDefault();
            registerForm.style.display = 'none';
            loginForm.style.display = 'flex';
            formTitle.textContent = 'Masuk Akun';
        });

        // Handle Login
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const res = await fetch('api/auth.php?action=login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        email: document.getElementById('loginEmail').value,
                        password: document.getElementById('loginPassword').value
                    })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    if (data.role === 'super_admin' || data.role === 'admin') {
                        window.location.href = 'admin/index.php';
                    } else {
                        window.location.href = 'dashboard.php';
                    }
                } else {
                    alert(data.message);
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi!');
            }
        });

        // Handle Register
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            try {
                const res = await fetch('api/auth.php?action=register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        name: document.getElementById('regName').value,
                        email: document.getElementById('regEmail').value,
                        password: document.getElementById('regPassword').value
                    })
                });
                const data = await res.json();
                alert(data.message);
                if (data.status === 'success') {
                    document.getElementById('toLogin').click();
                }
            } catch (err) {
                alert('Terjadi kesalahan koneksi!');
            }
        });
    </script>
</body>
</html>