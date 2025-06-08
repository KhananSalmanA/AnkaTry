<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Perbaikan path file koneksi
include '../koneksi.php';

// Ambil data user dari database
$username = $_SESSION['username'];
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Ambil statistik user
$user_id = $user['id'];

// Cek apakah tabel quiz_history ada
$has_history_table = false;
$result = $conn->query("SHOW TABLES LIKE 'quiz_history'");
if($result->num_rows > 0) {
    $has_history_table = true;
}

// Default stats
$stats = [
    'total_quiz' => 0,
    'avg_percentage' => 0,
    'best_score' => 0
];

// Ambil statistik jika tabel ada
if($has_history_table) {
    $stats_query = "
        SELECT 
            COUNT(*) as total_quiz,
            AVG(percentage) as avg_percentage,
            MAX(score) as best_score
        FROM quiz_history 
        WHERE user_id = ?
    ";
    $stmt_stats = $conn->prepare($stats_query);
    $stmt_stats->bind_param("i", $user_id);
    $stmt_stats->execute();
    $stats = $stmt_stats->get_result()->fetch_assoc();
    $stmt_stats->close();
}

$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .sidebar-toggle {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 8px;
            transition: background 0.3s;
        }
        
        .sidebar-toggle:hover {
            background: rgba(0,0,0,0.1);
        }
        
        .hamburger {
            display: block;
            width: 25px;
            height: 3px;
            background: #333;
            margin: 5px 0;
            transition: 0.3s;
            border-radius: 2px;
        }
        
        .sidebar {
            position: fixed;
            left: -300px;
            top: 0;
            width: 300px;
            height: 100vh;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            transition: left 0.3s ease;
            z-index: 1001;
            box-shadow: 2px 0 20px rgba(0,0,0,0.1);
        }
        
        .sidebar.open {
            left: 0;
        }
        
        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .sidebar-title {
            font-size: 1.3rem;
            font-weight: bold;
            color: #333;
        }
        
        .sidebar-close {
            background: none;
            border: none;
            font-size: 2rem;
            cursor: pointer;
            color: #666;
            padding: 0.5rem;
            border-radius: 50%;
            transition: all 0.3s;
        }
        
        .sidebar-close:hover {
            background: rgba(0,0,0,0.1);
            color: #333;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }
        
        .sidebar-menu li {
            margin: 0.5rem 0;
        }
        
        .sidebar-menu a {
            display: block;
            padding: 1rem 2rem;
            color: #333;
            text-decoration: none;
            transition: all 0.3s;
            border-left: 4px solid transparent;
        }
        
        .sidebar-menu a:hover {
            background: rgba(102, 126, 234, 0.1);
            border-left-color: #667eea;
            color: #667eea;
        }
        
        .sidebar-logout {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
        }
        
        .sidebar-logout a {
            display: block;
            padding: 1rem;
            background: #ff4757;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            transition: background 0.3s;
        }
        
        .sidebar-logout a:hover {
            background: #ff3742;
        }
        
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }
        
        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .dashboard-content {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }
        
        .stat-number {
            font-size: 3rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 1.1rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .profile-section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: none;
        }
        
        .profile-section.active {
            display: block;
        }
        
        .profile-section h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .profile-table {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .profile-table tr {
            border-bottom: 1px solid #eee;
        }
        
        .profile-table td {
            padding: 1rem;
            font-size: 1.1rem;
        }
        
        .profile-table .label {
            font-weight: 600;
            color: #333;
            width: 40%;
        }
        
        .section {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            display: none;
            text-align: center;
        }
        
        .section.active {
            display: block;
        }
        
        .section h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .section p {
            font-size: 1.2rem;
            color: #666;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .dashboard-content {
                padding: 1rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .stat-card {
                padding: 1.5rem;
            }
            
            .profile-section,
            .section {
                padding: 2rem;
            }
            
            .profile-section h2,
            .section h2 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Open sidebar">
                <span class="hamburger"></span>
                <span class="hamburger"></span>
                <span class="hamburger"></span>
            </button>
            🎓 AnkaTry Dashboard
        </div>
        <div class="navbar-link"></div>
    </nav>
    
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Menu</span>
            <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">&times;</button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="#" onclick="showSection('profile')">👤 Profile</a></li>
            <li><a href="latihansoal.php">📝 Latihan Soal</a></li>
            <li><a href="materi.php">📚 Materi Soal</a></li>
            <li><a href="leaderboard.php">🏆 Leaderboard</a></li>
            <li><a href="history.php">📊 History</a></li>
        </ul>
        <div class="sidebar-logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    
    <div class="dashboard-content">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_quiz'] ?: 0 ?></div>
                <div class="stat-label">Total Quiz</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['avg_percentage'] ? round($stats['avg_percentage'], 1) . '%' : '0%' ?></div>
                <div class="stat-label">Rata-rata</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['best_score'] ?: 0 ?></div>
                <div class="stat-label">Best Score</div>
            </div>
        </div>
        
        <div id="profile-section" class="profile-section active">
            <h2>👤 Profil Saya</h2>
            <table class="profile-table">
                <tr>
                    <td class="label">Username</td>
                    <td>: <?= htmlspecialchars($user['username']) ?></td>
                </tr>
                <tr>
                    <td class="label">Email</td>
                    <td>: <?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td>: <?= htmlspecialchars($user['alamat']) ?></td>
                </tr>
                <tr>
                    <td class="label">Tanggal Lahir</td>
                    <td>: <?= htmlspecialchars($user['tanggal_lahir']) ?></td>
                </tr>
                <tr>
                    <td class="label">No. Telp</td>
                    <td>: <?= htmlspecialchars($user['no_telp']) ?></td>
                </tr>
                <tr>
                    <td class="label">Asal Sekolah</td>
                    <td>: <?= htmlspecialchars($user['asal_sekolah']) ?></td>
                </tr>
            </table>
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
            // Hide all sections
            document.querySelectorAll('.profile-section, .section').forEach(el => {
                el.classList.remove('active');
            });
            
            // Show selected section
            document.getElementById(section + '-section').classList.add('active');
            
            // Close sidebar
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('active');
        }
        
        // Animasi loading untuk stat cards
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            statNumbers.forEach(stat => {
                const text = stat.textContent;
                const number = parseInt(text);
                
                if (!isNaN(number)) {
                    let currentNumber = 0;
                    const increment = number / 50;
                    
                    const timer = setInterval(() => {
                        currentNumber += increment;
                        if (currentNumber >= number) {
                            stat.textContent = text;
                            clearInterval(timer);
                        } else {
                            stat.textContent = Math.floor(currentNumber);
                        }
                    }, 30);
                }
            });
        });
    </script>
</body>
</html>
