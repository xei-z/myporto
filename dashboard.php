<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Member — ZeriKo</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <a href="index.php" class="logo">ZeriKo.</a>
        <nav>
            <a href="#" id="logoutBtn">Keluar</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-tag">Member Area</div>
        <h1>Selamat datang, <?= htmlspecialchars($_SESSION['name']); ?>!</h1>
        <p>Akses akun kamu berhasil. Kamu masuk sebagai <strong><?= htmlspecialchars($_SESSION['role']); ?></strong>.</p>
    </section>

    <script>
        document.getElementById('logoutBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            await fetch('api/auth.php?action=logout');
            window.location.href = 'login.php';
        });
    </script>
</body>
</html>