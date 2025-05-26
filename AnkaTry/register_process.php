<?php
include 'koneksi.php'; // ini buat koneksi ke database

// ambil data dari form
$username       = $_POST['username'];
$email          = $_POST['email'];
$password       = password_hash($_POST['password'], PASSWORD_DEFAULT);
$alamat         = $_POST['alamat'];
$tanggal_lahir  = $_POST['tanggal_lahir'];
$no_telp        = $_POST['no_telp'];
$asal_sekolah   = $_POST['asal_sekolah'];

// query insert
$sql = "INSERT INTO users (username, email, password, alamat, tanggal_lahir, no_telp, asal_sekolah)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $username, $email, $password, $alamat, $tanggal_lahir, $no_telp, $asal_sekolah);

if ($stmt->execute()) {
    echo "Registrasi berhasil! <a href='login.php'>Login di sini</a>";
} else {
    echo "Gagal registrasi: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
