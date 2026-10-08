<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' &&$_SESSION['role'] !== 'super_admin')) {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CMS Admin — Kelola Proyek</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .admin-container { max-width: 900px; margin: 2rem auto; padding: 0 1rem; }
        .header-action { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
        .table-box { background: var(--card-bg); padding: 1.5rem; border-radius: 6px; border: 1px solid var(--border-color); }
        table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        th, td { padding: 0.75rem; border-bottom: 1px solid var(--border-color); text-align: left; }
        
        .btn-add { background: var(--text-main); color: #fff; padding: 0.6rem 1.2rem; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
        .btn-add:hover { background: var(--accent-color); }
        .btn-edit { background: #f39c12; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; margin-right: 0.3rem; }
        .btn-delete { background: #e74c3c; color: #fff; border: none; padding: 0.4rem 0.8rem; border-radius: 4px; cursor: pointer; }
        
        /* STYLING MODAL */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); justify-content: center; align-items: center; z-index: 1000; }
        .modal-content { background: #fff; padding: 2rem; border-radius: 8px; width: 100%; max-width: 500px; position: relative; }
        .close-btn { position: absolute; top: 1rem; right: 1rem; font-size: 1.5rem; cursor: pointer; border: none; background: none; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1rem; }
        .form-group input, .form-group textarea { padding: 0.75rem; border: 1px solid var(--border-color); border-radius: 4px; outline: none; }
        .btn-submit { width: 100%; padding: 0.75rem; background: var(--text-main); color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; }
    </style>
</head>
<body>
    <header>
        <a href="../index.php" class="logo">ZeriKo Admin.</a>
        <nav><a href="#" id="logoutBtn">Keluar</a></nav>
    </header>

    <div class="admin-container">
        <?php if ($_SESSION['role'] === 'super_admin'): ?>
            <a href="users.php" style="color: var(--accent-color); font-weight: 600; text-decoration: none; display: inline-block; margin-bottom: 1rem;">→ Kelola User (Super Admin)</a>
        <?php endif; ?>

        <div class="header-action">
            <div>
                <h2>Kelola Proyek Portofolio</h2>
                <p>Level Akses: <strong><?= htmlspecialchars($_SESSION['role']); ?></strong></p>
            </div>
            <button class="btn-add" onclick="openModal()">+ Tambah Proyek</button>
        </div>

        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th style="text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="projectList"></tbody>
            </table>
        </div>
    </div>

    <!-- MODAL FORM (TAMBAH / EDIT) -->
    <div class="modal" id="projectModal">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">&times;</button>
            <h3 id="modalTitle">Tambah Proyek Baru</h3>
            <form id="projectForm" style="margin-top: 1rem;" enctype="multipart/form-data">
                <input type="hidden" id="projectId">
                <div class="form-group">
                    <label>Judul Proyek</label>
                    <input type="text" id="title" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <input type="text" id="category" required placeholder="e.g., Web App, UI/UX">
                </div>
                <div class="form-group">
                    <label>Upload Gambar File (PNG / JPG, max 2MB)</label>
                    <input type="file" id="image_file" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Atau masukan URL Gambar</label>
                    <input type="text" id="image_url" placeholder="https://via.placeholder.com/400x250">
                </div>
                <div class="form-group">
                    <label>Deskripsi Proyek</label>
                    <textarea id="description" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn-submit" id="btnSubmit">Simpan Proyek</button>
            </form>
        </div>
    </div>

    <script>
        let projectsData = [];

        async function loadProjects() {
            const res = await fetch('../api/projects.php');
            const data = await res.json();
            projectsData = data.data || [];
            
            const tbody = document.getElementById('projectList');
            tbody.innerHTML = '';

            projectsData.forEach(p => {
                tbody.innerHTML += `
                    <tr>
                        <td><strong>${p.title}</strong></td>
                        <td>${p.category}</td>
                        <td style="text-align: right;">
                            <button class="btn-edit" onclick="editProject(${p.id})">Edit</button>
                            <button class="btn-delete" onclick="deleteProject(${p.id})">Hapus</button>
                        </td>
                    </tr>
                `;
            });
        }

        function openModal() {
            document.getElementById('projectForm').reset();
            document.getElementById('projectId').value = '';
            document.getElementById('modalTitle').textContent = 'Tambah Proyek Baru';
            document.getElementById('btnSubmit').textContent = 'Simpan Proyek';
            document.getElementById('projectModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('projectModal').style.display = 'none';
        }

        function editProject(id) {
            const p = projectsData.find(item => item.id == id);
            if (!p) return;

            document.getElementById('projectId').value = p.id;
            document.getElementById('title').value = p.title;
            document.getElementById('category').value = p.category;
            document.getElementById('image_url').value = p.image_url || '';
            document.getElementById('description').value = p.description;

            document.getElementById('modalTitle').textContent = 'Edit Proyek';
            document.getElementById('btnSubmit').textContent = 'Update Proyek';
            document.getElementById('projectModal').style.display = 'flex';
        }

        // Handle Submit dengan FormData
        document.getElementById('projectForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('projectId').value;

            const formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('category', document.getElementById('category').value);
            formData.append('image_url', document.getElementById('image_url').value);
            formData.append('description', document.getElementById('description').value);
            
            const fileInput = document.getElementById('image_file');
            if (fileInput.files[0]) {
                formData.append('image_file', fileInput.files[0]);
            }

            const url = id ? `../api/projects.php?action=update&id=${id}` : '../api/projects.php?action=create';

            const res = await fetch(url, {
                method: 'POST',
                body: formData
            });
            const data = await res.json();
            alert(data.message);
            
            if (data.status === 'success') {
                closeModal();
                loadProjects();
            }
        });

        async function deleteProject(id) {
            if (confirm('Yakin ingin menghapus proyek ini?')) {
                const res = await fetch(`../api/projects.php?action=delete&id=${id}`, { method: 'DELETE' });
                const data = await res.json();
                alert(data.message);
                loadProjects();
            }
        }

        document.getElementById('logoutBtn').addEventListener('click', async (e) => {
            e.preventDefault();
            await fetch('../api/auth.php?action=logout');
            window.location.href = '../login.php';
        });

        loadProjects();
    </script>
</body>
</html>