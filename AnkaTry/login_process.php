<?php
session_start();
include 'koneksi.php'; // File koneksi database

// Validasi input
if(empty($_POST['username']) || empty($_POST['password'])) {
    header("Location: login.php?error=empty");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

// Ambil data user
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    // Set session
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['user_id'] = $user['id'];

    // Arahkan ke dashboard berdasarkan role
    if ($user['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
} else {
    // Login gagal
    header("Location: login.php?error=invalid");
    exit();
}
?>
