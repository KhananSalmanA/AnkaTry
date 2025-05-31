<?php
include 'koneksi.php'; // File koneksi database

// Validasi input
if(empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || 
   empty($_POST['alamat']) || empty($_POST['tanggal_lahir']) || empty($_POST['no_telp']) || 
   empty($_POST['asal_sekolah'])) {
    header("Location: register.php?error=empty");
    exit();
}

// Ambil data dari form
$username       = $_POST['username'];
$email          = $_POST['email'];
$password       = password_hash($_POST['password'], PASSWORD_DEFAULT);
$alamat         = $_POST['alamat'];
$tanggal_lahir  = $_POST['tanggal_lahir'];
$no_telp        = $_POST['no_telp'];
$asal_sekolah   = $_POST['asal_sekolah'];

// Cek apakah username atau email sudah terdaftar
$check_query = "SELECT * FROM users WHERE username = ? OR email = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("ss", $username, $email);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    header("Location: register.php?error=exists");
    exit();
}

// Query insert
$sql = "INSERT INTO users (username, email, password, alamat, tanggal_lahir, no_telp, asal_sekolah, role)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'user')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $username, $email, $password, $alamat, $tanggal_lahir, $no_telp, $asal_sekolah);

if ($stmt->execute()) {
    // Registrasi berhasil
    header("Location: login.php?success=register");
} else {
    // Registrasi gagal
    header("Location: register.php?error=failed");
}

$stmt->close();
$conn->close();
?>
