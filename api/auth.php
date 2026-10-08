<?php
session_start();
header("Content-Type: application/json");
require_once "../config/database.php";

$action = $_GET['action'] ?? '';

// 1. REGISTRASI MEMBER BIASA
if ($action === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $name     = trim($input['name'] ?? '');
    $email    = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    if (empty($name) || empty($email) || empty($password)) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Semua field wajib diisi!"]);
        exit();
    }

    // Cek apakah email sudah terdaftar
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Email sudah terdaftar!"]);
        exit();
    }

    // Hash password & simpan user baru dengan role 'member'
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (:name, :email, :password, 'member')");
    $stmt->execute(['name' => htmlspecialchars($name), 'email' => $email, 'password' => $hashedPassword]);

    echo json_encode(["status" => "success", "message" => "Registrasi berhasil! Silakan login."]);
    exit();
}

// 2. LOGIN USER
if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents("php://input"), true);
    
    $email    = trim($input['email'] ?? '');
    $password = trim($input['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name']    = $user['name'];
        $_SESSION['role']    = $user['role'];

        echo json_encode([
            "status" => "success",
            "message" => "Login berhasil!",
            "role" => $user['role']
        ]);
        exit();
    }

    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Email atau password salah!"]);
    exit();
}

// 3. LOGOUT
if ($action === 'logout') {
    session_destroy();
    echo json_encode(["status" => "success", "message" => "Berhasil logout."]);
    exit();
}