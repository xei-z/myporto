<?php
session_start();
header("Content-Type: application/json");
require_once "../config/database.php";

$action = $_GET['action'] ?? '';

// 1. GET ALL PROJECTS (Publik)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
    $projects = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $projects]);
    exit();
}

// PROTEKSI ADMIN & SUPER ADMIN
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'super_admin')) {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Akses ditolak!"]);
    exit();
}

// Helper untuk Upload Gambar
function handleFileUpload($oldImage = null) {
    if (!isset($_FILES['image_file']) || $_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
        return $oldImage; // Kembalikan gambar lama jika tidak ada file baru yang diunggah
    }

    $file = $_FILES['image_file'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $maxSize = 2 * 1024 * 1024; // Maksimal 2MB

    if (!in_array($file['type'], $allowedTypes)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Format file harus JPG, PNG, WEBP, atau GIF!"]);
        exit();
    }

    if ($file['size'] > $maxSize) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Ukuran file maksimal 2MB!"]);
        exit();
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $newFileName = uniqid('proj_', true) . '.' . strtolower($ext);
    $uploadDir = '../assets/uploads/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($file['tmp_name'], $uploadDir . $newFileName)) {
        // Hapus file lama jika ada dan bukan URL eksternal
        if ($oldImage && strpos($oldImage, 'assets/uploads/') !== false && file_exists('../' . $oldImage)) {
            unlink('../' . $oldImage);
        }
        return 'assets/uploads/' . $newFileName;
    }

    return $oldImage;
}

// 2. TAMBAH PROYEK (POST)
if ($action === 'create' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');
    
    // Cek upload file atau fallback ke text URL jika diisi
    $imageUrl = handleFileUpload();
    if (!$imageUrl && !empty($_POST['image_url'])) {
        $imageUrl = trim($_POST['image_url']);
    }

    if (empty($title) || empty($description) || empty($category)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Judul, Deskripsi, dan Kategori wajib diisi!"]);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO projects (title, description, category, image_url) VALUES (:title, :description, :category, :image_url)");
    $stmt->execute([
        'title' => htmlspecialchars($title),
        'description' => htmlspecialchars($description),
        'category' => htmlspecialchars($category),
        'image_url' => htmlspecialchars($imageUrl)
    ]);

    echo json_encode(["status" => "success", "message" => "Proyek berhasil ditambahkan!"]);
    exit();
}

// 3. UPDATE PROYEK (POST)
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_GET['id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category = trim($_POST['category'] ?? '');

    if (!$id || empty($title) || empty($description) || empty($category)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Data tidak lengkap!"]);
        exit();
    }

    // Ambil data lama untuk hapus file lama jika diganti
    $stmtOld = $pdo->prepare("SELECT image_url FROM projects WHERE id = :id");
    $stmtOld->execute(['id' => $id]);
    $oldData = $stmtOld->fetch();
    $oldImage = $oldData['image_url'] ?? null;

    $imageUrl = handleFileUpload($oldImage);
    if (empty($_FILES['image_file']['name']) && !empty($_POST['image_url'])) {
        $imageUrl = trim($_POST['image_url']);
    }

    $stmt = $pdo->prepare("UPDATE projects SET title = :title, description = :description, category = :category, image_url = :image_url WHERE id = :id");
    $stmt->execute([
        'id' => $id,
        'title' => htmlspecialchars($title),
        'description' => htmlspecialchars($description),
        'category' => htmlspecialchars($category),
        'image_url' => htmlspecialchars($imageUrl),
    ]);

    echo json_encode(["status" => "success", "message" => "Proyek berhasil diperbarui!"]);
    exit();
}

// 4. HAPUS PROYEK (DELETE)
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if (!$id) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "ID Proyek tidak valid!"]);
        exit();
    }

    // Hapus file gambar di folder uploads jika ada
    $stmtOld = $pdo->prepare("SELECT image_url FROM projects WHERE id = :id");
    $stmtOld->execute(['id' => $id]);
    $oldData = $stmtOld->fetch();
    if (!empty($oldData['image_url']) && strpos($oldData['image_url'], 'assets/uploads/') !== false && file_exists('../' . $oldData['image_url'])) {
        unlink('../' . $oldData['image_url']);
    }

    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode(["status" => "success", "message" => "Proyek berhasil dihapus!"]);
    exit();
}