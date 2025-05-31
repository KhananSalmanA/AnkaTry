<?php
$host = "localhost";
$user = "root";
$pass = ""; // kosongkan kalau belum ada password MySQL
$db   = "db_utbk"; // sesuai dengan database yang kamu buat

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Periksa koneksi
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
