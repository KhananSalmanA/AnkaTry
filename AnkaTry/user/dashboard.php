<?php
session_start(); // ⬅️ WAJIB ADA DI BARIS PALING ATAS

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

include '../koneksi.php'; // ⬅️ Pastikan ini ada agar koneksi database bisa dipakai

// Ambil data user dari database
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Open sidebar">
                <span class="hamburger"></span>
                <span class="hamburger"></span>
                <span class="hamburger"></span>
            </button>
            Tyty Dashboard
        </div>
        <div class="navbar-link"></div>
    </nav>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Menu</span>
            <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">&times;</button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" onclick="showSection('profile')">Profile</a></li>
            <li><a href="latihansoal.php">Latihan Soal</a></li>
            <li><a href="#" onclick="showSection('materi')">Materi Soal</a></li>
            <li><a href="#" onclick="showSection('leaderboard')">Leaderboard</a></li>
        </ul>
        <div class="sidebar-logout">
            <a href="logout.php">Logout</a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <div class="dashboard-content">
        <div id="profile-section" class="profile-section">
            <h2>Profil Saya</h2>
            <table>
                <tr>
                    <td class="label">Username</td>
                    <td>: <?php echo htmlspecialchars($user['username']); ?></td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td>: <?php echo htmlspecialchars($user['email']); ?></td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td>: <?php echo htmlspecialchars($user['alamat']); ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Lahir</td>
                    <td>: <?php echo htmlspecialchars($user['tanggal_lahir']); ?></td>
                </tr>
                <tr>
                    <td class="label">No. Telp</td>
                    <td>: <?php echo htmlspecialchars($user['no_telp']); ?></td>
                </tr>
                <tr>
                    <td class="label">Asal Sekolah</td>
                    <td>: <?php echo htmlspecialchars($user['asal_sekolah']); ?></td>
                </tr>
            </table>
        </div>
        <div id="latihan-section" style="display:none;">
            <!-- Hapus seluruh isi latihan-section, karena sekarang diarahkan ke latihansoal.php -->
        </div>
        <div id="materi-section" style="display:none;">
            <h2>Materi Soal</h2>
            <p>Menu materi soal akan tersedia di sini.</p>
        </div>
        <div id="leaderboard-section" style="display:none;">
            <h2>Leaderboard</h2>
            <p>Menu leaderboard akan tersedia di sini.</p>
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

        // Section switcher
        function showSection(section) {
            document.getElementById('profile-section').style.display = (section === 'profile') ? '' : 'none';
            document.getElementById('latihan-section').style.display = (section === 'latihan') ? '' : 'none';
            document.getElementById('materi-section').style.display = (section === 'materi') ? '' : 'none';
            document.getElementById('leaderboard-section').style.display = (section === 'leaderboard') ? '' : 'none';
            // Tutup sidebar otomatis setelah klik menu (untuk mobile UX)
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
        // Default: tampilkan profil
        showSection('profile');
    </script>
</body>
</html>
