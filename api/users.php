<?php
session_start();
header("Content-Type: application/json");
require_once "../config/database.php";

// Proteksi Khusus Super Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'super_admin') {
    http_response_code(403);
    echo json_encode(["status" => "error", "message" => "Akses ditolak! Khusus Super Admin."]);
    exit();
}

$action = $_GET['action'] ?? '';

// 1. GET ALL USERS
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
    echo json_encode(["status" => "success", "data" => $users]);
    exit();
}

// 2. UPDATE ROLE USER
if ($action === 'update_role' && $_SERVER['REQUEST_METHOD'] === 'PUT') {
    $input = json_decode(file_get_contents("php://input"), true);
    $id = $_GET['id'] ?? null;
    $role = $input['role'] ?? '';

    // PREVENT SELF-ROLE CHANGE
    if ($id == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Kamu tidak bisa mengubah role akunmu sendiri!"]);
        exit();
    }

    if (!$id || !in_array($role, ['super_admin', 'admin', 'member'])) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Data tidak valid!"]);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
    $stmt->execute(['role' => $role, 'id' => $id]);

    echo json_encode(["status" => "success", "message" => "Role user berhasil diperbarui!"]);
    exit();
}

// 3. DELETE USER
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_GET['id'] ?? null;
    
    // PREVENT SELF-DELETE
    if ($id == $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Kamu tidak bisa menghapus akunmu sendiri!"]);
        exit();
    }

    $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
    $stmt->execute(['id' => $id]);

    echo json_encode(["status" => "success", "message" => "User berhasil dihapus!"]);
    exit();
}