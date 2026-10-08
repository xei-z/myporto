<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen User — Super Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .table-box { background: var(--card-bg); padding: 1.5rem; border-radius: 6px; border: 1px solid var(--border-color); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; border-bottom: 1px solid var(--border-color); text-align: left; }
        select { padding: 0.4rem; border-radius: 4px; border: 1px solid var(--border-color); }
        .btn-delete { background: #e74c3c; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; }
        .nav-links { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
        .nav-links a { color: var(--accent-color); text-decoration: none; font-weight: 600; }
    </style>
</head>
<body>
    <header>
        <a href="../index.php" class="logo">ZeriKo Admin.</a>
        <nav><a href="#" id="logoutBtn">Keluar</a></nav>
    </header>

    <div class="admin-container">
        <div class="nav-links">
            <a href="index.php">← Kelola Proyek</a>
        </div>

        <h2>Kelola Pengguna (Users)</h2>
        <p>Akses Khusus: <strong>Super Admin</strong></p>

        <div class="table-box" style="margin-top: 1rem;">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="userList"></tbody>
            </table>
        </div>
    </div>

    <script>
    // Ambil ID User yang sedang login dari Session PHP (dikondisikan sebagai String)
    const currentUserId = "<?= (string)$_SESSION['user_id']; ?>";

    async function loadUsers() {
        try {
            const res = await fetch('../api/users.php');
            const data = await res.json();
            const tbody = document.getElementById('userList');
            tbody.innerHTML = '';

            if (data.status === 'success') {
                data.data.forEach(u => {
                    // Bandingkan ID sebagai String
                    const isSelf = (String(u.id) === String(currentUserId));
                    
                    tbody.innerHTML += `
                        <tr>
                            <td>
                                <strong>${u.name}</strong> 
                                ${isSelf ? '<span style="font-size: 0.75rem; background: #27ae60; color: #fff; padding: 2px 8px; border-radius: 4px; margin-left: 6px; font-weight: 600;">Kamu</span>' : ''}
                            </td>
                            <td>${u.email}</td>
                            <td>
                                ${isSelf ? `<strong>Super Admin</strong>` : `
                                    <select onchange="updateRole(${u.id}, this.value)">
                                        <option value="member" ${u.role === 'member' ? 'selected' : ''}>Member</option>
                                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                                        <option value="super_admin" ${u.role === 'super_admin' ? 'selected' : ''}>Super Admin</option>
                                    </select>
                                `}
                            </td>
                            <td style="text-align: right;">
                                ${isSelf ? '<em style="color: #888; font-size: 0.85rem;">Aktif (Sesi Ini)</em>' : `
                                    <button class="btn-delete" onclick="deleteUser(${u.id})">Hapus</button>
                                `}
                            </td>
                        </tr>
                    `;
                });
            }
        } catch (err) {
            console.error('Gagal memuat data user:', err);
        }
    }

    async function updateRole(id, role) {
        const res = await fetch(`../api/users.php?action=update_role&id=${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ role })
        });
        const data = await res.json();
        alert(data.message);
        loadUsers();
    }

    async function deleteUser(id) {
        if (confirm('Yakin ingin menghapus user ini?')) {
            const res = await fetch(`../api/users.php?action=delete&id=${id}`, { method: 'DELETE' });
            const data = await res.json();
            alert(data.message);
            loadUsers();
        }
    }

    document.getElementById('logoutBtn').addEventListener('click', async (e) => {
        e.preventDefault();
        await fetch('../api/auth.php?action=logout');
        window.location.href = '../login.php';
    });

    loadUsers();
</script>
</body>
</html>