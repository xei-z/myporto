<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeriKo — Personal Portfolio</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..700;1,400..700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* CSS TAMBAHAN UNTUK FILTER & GAMBAR PROYEK */
        .filter-container { display: flex; gap: 0.5rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .filter-btn { padding: 0.4rem 1rem; border: 1px solid var(--border-color); background: transparent; cursor: pointer; border-radius: 20px; font-size: 0.85rem; transition: all 0.2s; }
        .filter-btn.active, .filter-btn:hover { background: var(--text-main); color: #fff; }
        .project-img { width: 100%; height: 200px; object-fit: cover; border-radius: 6px; margin-bottom: 0.8rem; }
    </style>
</head>
<body>

    <header>
        <a href="#" class="logo">ZeriKo.</a>
        <nav>
            <a href="#projects">Proyek</a>
            <a href="#about">Tentang</a>
            <a href="login.php">Masuk / Member</a>
        </nav>
    </header>

    <section class="hero">
        <div class="hero-tag">Developer & Tech Enthusiast</div>
        <h1>Membangun web yang fungsional dengan estetika timeless.</h1>
        <p>Halo! Saya ZeriKo. Fokus eksplorasi pengembangan web modern, antarmuka minimalis, dan arsitektur kode yang bersih.</p>
    </section>

    <section id="projects">
        <h2 class="section-title">Karya & Proyek</h2>
        
        <!-- TOMBOL FILTER KATEGORI -->
        <div class="filter-container" id="filterButtons">
            <button class="filter-btn active" onclick="filterProjects('all')">Semua</button>
        </div>

        <div class="project-list" id="projectContainer">
            <p style="color: var(--text-muted);">Memuat proyek...</p>
        </div>
    </section>

    <section id="about">
        <h2 class="section-title">Teknologi</h2>
        <div class="skills-grid">
            <span class="skill-item">HTML5 & CSS3</span>
            <span class="skill-item">JavaScript</span>
            <span class="skill-item">PHP (Laragon)</span>
            <span class="skill-item">MySQL</span>
            <span class="skill-item">REST API</span>
        </div>
    </section>

    <footer>
        <p>&copy; 2026 ZeriKo. Designed with classic aesthetic.</p>
        <div class="social-links">
            <a href="https://github.com/xei-z" target="_blank">GitHub</a>
        </div>
    </footer>

    <script>
    let allProjects = [];

    async function fetchPublicProjects() {
        try {
            const res = await fetch('api/projects.php');
            const result = await res.json();
            
            if (result.status === 'success') {
                allProjects = result.data || [];
                renderCategories();
                renderProjects(allProjects);
            }
        } catch (err) {
            console.error('Gagal memuat proyek:', err);
        }
    }

    // Fungsi Render Tombol Kategori Dinamis
    function renderCategories() {
        const filterBox = document.getElementById('filterButtons');
        const categories = ['all', ...new Set(allProjects.map(p => p.category))];
        
        filterBox.innerHTML = '';
        categories.forEach(cat => {
            const label = cat === 'all' ? 'Semua' : cat;
            filterBox.innerHTML += `
                <button class="filter-btn ${cat === 'all' ? 'active' : ''}" onclick="filterProjects('${cat}', this)">${label}</button>
            `;
        });
    }

    // Fungsi Filter Data
    function filterProjects(category, btnElement) {
        if (btnElement) {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            btnElement.classList.add('active');
        }

        if (category === 'all') {
            renderProjects(allProjects);
        } else {
            const filtered = allProjects.filter(p => p.category === category);
            renderProjects(filtered);
        }
    }

    // Fungsi Render Kartu Proyek
    function renderProjects(projects) {
        const container = document.getElementById('projectContainer');
        container.innerHTML = '';

        if (projects.length === 0) {
            container.innerHTML = '<p style="color: var(--text-muted);">Tidak ada proyek dalam kategori ini.</p>';
            return;
        }

        projects.forEach(p => {
            const year = new Date(p.created_at || Date.now()).getFullYear();
            const imageSrc = p.image_url ? p.image_url : 'https://via.placeholder.com/400x250';
            
            container.innerHTML += `
                <div class="project-card">
                    <img src="${imageSrc}" alt="${p.title}" class="project-img" onerror="this.src='https://via.placeholder.com/400x250'">
                    <div class="project-header">
                        <span class="project-title">${p.title}</span>
                        <span class="project-year">${year}</span>
                    </div>
                    <p class="project-desc">${p.description}</p>
                    <div class="project-tags">
                        <span class="tag">${p.category}</span>
                    </div>
                </div>
            `;
        });
    }

    document.addEventListener('DOMContentLoaded', fetchPublicProjects);
    </script>
</body>
</html>