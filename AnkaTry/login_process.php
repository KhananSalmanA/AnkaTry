<?php
session_start();
include 'koneksi.php'; // ganti sesuai nama file koneksi kamu

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
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];

    // Arahkan ke dashboard berdasarkan role
    if ($user['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: user/dashboard.php");
    }
    exit();
} else {
    echo "<script>alert('Username atau password salah!'); window.location.href='login.php';</script>";
}
?>
