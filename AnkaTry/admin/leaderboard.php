<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../connsoal.php';

// Ambil data leaderboard
$leaderboard_query = "
    SELECT 
        u.username, 
        u.asal_sekolah,
        COUNT(qh.id) as total_quiz,
        AVG(qh.percentage) as avg_percentage,
        MAX(qh.score) as best_score,
        SUM(qh.score) as total_score
    FROM users u 
    LEFT JOIN quiz_history qh ON u.id = qh.user_id 
    WHERE u.role = 'user'
    GROUP BY u.id, u.username, u.asal_sekolah
    ORDER BY avg_percentage DESC, total_score DESC
";
$leaderboard = $koneksi->query($leaderboard_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
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
        }
        
        .navbar-title {
            font-size: 1.5rem;
            font-weight: bold;
            color: #333;
        }
        
        .navbar-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }
        
        .navbar-link a:hover {
            color: #764ba2;
        }
        
        .page-container {
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .leaderboard-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .leaderboard-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .leaderboard-header h1 {
            font-size: 3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .leaderboard-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .leaderboard-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 2rem;
        }
        
        .leaderboard-table th,
        .leaderboard-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .leaderboard-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .leaderboard-table tr:hover {
            background: #f8f9fa;
        }
        
        .rank {
            font-size: 1.5rem;
            font-weight: bold;
            text-align: center;
            width: 80px;
        }
        
        .rank.gold {
            color: #ffd700;
        }
        
        .rank.silver {
            color: #c0c0c0;
        }
        
        .rank.bronze {
            color: #cd7f32;
        }
        
        .username {
            font-weight: 600;
            color: #333;
        }
        
        .school {
            color: #666;
            font-size: 0.9rem;
        }
        
        .score {
            font-weight: bold;
            color: #667eea;
        }
        
        .percentage {
            font-weight: bold;
        }
        
        .percentage.excellent {
            color: #2ed573;
        }
        
        .percentage.good {
            color: #ffa502;
        }
        
        .percentage.average {
            color: #ff6348;
        }
        
        .no-data {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-size: 1.2rem;
        }
        
        /* Sidebar styles */
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
        .sidebar.open { left: 0; }
        .sidebar-header {
            padding: 2rem;
            border-bottom: 1px solid rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-title { font-size: 1.3rem; font-weight: bold; color: #333; }
        .sidebar-close {
            background: none; border: none; font-size: 2rem; cursor: pointer; color: #666;
            padding: 0.5rem; border-radius: 50%; transition: all 0.3s;
        }
        .sidebar-close:hover { background: rgba(0,0,0,0.1); color: #333; }
        .sidebar-menu { list-style: none; padding: 1rem 0; }
        .sidebar-menu li { margin: 0.5rem 0; }
        .sidebar-menu a {
            display: block; padding: 1rem 2rem; color: #333; text-decoration: none;
            transition: all 0.3s; border-left: 4px solid transparent;
        }
        .sidebar-menu a:hover {
            background: rgba(102, 126, 234, 0.1); border-left-color: #667eea; color: #667eea;
        }
        .sidebar-logout {
            position: absolute; bottom: 2rem; left: 2rem; right: 2rem;
        }
        .sidebar-logout a {
            display: block; padding: 1rem; background: #ff4757; color: white;
            text-decoration: none; border-radius: 8px; text-align: center; transition: background 0.3s;
        }
        .sidebar-logout a:hover { background: #ff3742; }
        .sidebar-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5); z-index: 1000; opacity: 0; visibility: hidden; transition: all 0.3s;
        }
        .sidebar-overlay.active { opacity: 1; visibility: visible; }
        .sidebar-toggle {
            background: none; border: none; cursor: pointer; padding: 0.5rem; border-radius: 8px; transition: background 0.3s;
            margin-right: 1rem; vertical-align: middle;
        }
        .sidebar-toggle:hover { background: rgba(0,0,0,0.1); }
        .hamburger {
            display: block; width: 25px; height: 3px; background: #333; margin: 5px 0; transition: 0.3s; border-radius: 2px;
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }
            
            .leaderboard-container {
                padding: 2rem;
            }
            
            .leaderboard-header h1 {
                font-size: 2rem;
            }
            
            .leaderboard-table {
                font-size: 0.9rem;
            }
            
            .leaderboard-table th,
            .leaderboard-table td {
                padding: 0.5rem;
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
            🏆 Leaderboard
        </div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Menu Admin</span>
            <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">&times;</button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="tambahsoal.php">📝 Buat Soal</a></li>
            <li><a href="daftarsoal.php">📋 Daftar Soal</a></li>
            <li><a href="tambahmateri.php">📚 Tambah Materi</a></li>
            <li><a href="materi.php">📖 Daftar Materi</a></li>
            <li><a href="leaderboard.php">🏆 Leaderboard</a></li>
        </ul>
        <div class="sidebar-logout">
            <a href="../logout.php">Logout</a>
        </div>
    </div>
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>
    <!-- End Sidebar -->

    <div class="page-container">
        <div class="leaderboard-container">
            <div class="leaderboard-header">
                <h1>🏆 Leaderboard</h1>
                <p>Ranking peserta berdasarkan performa quiz</p>
            </div>
            
            <?php if ($leaderboard && $leaderboard->num_rows > 0): ?>
                <table class="leaderboard-table">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Username</th>
                            <th>Asal Sekolah</th>
                            <th>Total Quiz</th>
                            <th>Rata-rata (%)</th>
                            <th>Best Score</th>
                            <th>Total Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $rank = 1;
                        while ($row = $leaderboard->fetch_assoc()): 
                            $rankClass = '';
                            if ($rank == 1) $rankClass = 'gold';
                            elseif ($rank == 2) $rankClass = 'silver';
                            elseif ($rank == 3) $rankClass = 'bronze';
                            
                            $percentageClass = '';
                            $avg_percentage = round($row['avg_percentage'], 1);
                            if ($avg_percentage >= 80) $percentageClass = 'excellent';
                            elseif ($avg_percentage >= 60) $percentageClass = 'good';
                            else $percentageClass = 'average';
                        ?>
                            <tr>
                                <td class="rank <?= $rankClass ?>">
                                    <?php
                                    if ($rank == 1) echo '🥇';
                                    elseif ($rank == 2) echo '🥈';
                                    elseif ($rank == 3) echo '🥉';
                                    else echo $rank;
                                    ?>
                                </td>
                                <td class="username"><?= htmlspecialchars($row['username']) ?></td>
                                <td class="school"><?= htmlspecialchars($row['asal_sekolah']) ?></td>
                                <td><?= $row['total_quiz'] ?></td>
                                <td class="percentage <?= $percentageClass ?>">
                                    <?= $row['avg_percentage'] ? $avg_percentage . '%' : '-' ?>
                                </td>
                                <td class="score"><?= $row['best_score'] ?: '-' ?></td>
                                <td class="score"><?= $row['total_score'] ?: '-' ?></td>
                            </tr>
                        <?php 
                        $rank++;
                        endwhile; 
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="no-data">
                    <p>Belum ada data quiz yang tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Sidebar toggle
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

        // Animasi untuk tabel
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.leaderboard-table tbody tr');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
