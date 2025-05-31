<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Open sidebar">
                <span class="hamburger"></span>
                <span class="hamburger"></span>
                <span class="hamburger"></span>
            </button>
            Admin Dashboard
        </div>
        <div class="navbar-link"></div>
    </nav>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Menu</span>
            <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">&times;</button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="tambahsoal.php">Buat Soal</a></li>
            <li><a href="daftarsoal.php">Daftar Soal</a></li>
            <li><a href="tambahmateri.php">Tambah Materi</a></li>
            <li><a href="#" onclick="showSection('daftar-materi')">Daftar Materi</a></li>
        </ul>
        <div class="sidebar-logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="dashboard-content">
        <div id="tambah-materi-section" style="display:none;">
            <h2>Tambah Materi</h2>
            <p>Form tambah materi akan di sini.</p>
        </div>
        <div id="daftar-materi-section" style="display:none;">
            <h2>Daftar Materi</h2>
            <p>Daftar materi akan di sini.</p>
        </div>
        <div id="welcome-section">
            <h2>Selamat Datang Admin</h2>
            <p>Silakan pilih menu di sidebar untuk mengelola soal dan materi.</p>
        </div>
    </div>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === "Escape") {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('active');
            }
        });

        function showSection(section) {
            document.getElementById('buat-soal-section').style.display = (section === 'buat-soal') ? '' : 'none';
            document.getElementById('daftar-soal-section').style.display = (section === 'daftar-soal') ? '' : 'none';
            document.getElementById('tambah-materi-section').style.display = (section === 'tambah-materi') ? '' : 'none';
            document.getElementById('daftar-materi-section').style.display = (section === 'daftar-materi') ? '' : 'none';
            document.getElementById('welcome-section').style.display = 'none';
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
    </script>
</body>
</html>
