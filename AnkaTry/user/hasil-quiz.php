<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}

// Cek apakah ada data hasil quiz di session
if (!isset($_SESSION['quiz_result'])) {
    header("Location: latihansoal.php");
    exit();
}

include '../koneksi.php';

$result = $_SESSION['quiz_result'];
$score = $result['score'];
$max_score = $result['max_score'];
$percentage = $result['percentage'];
$judul_grup = $result['judul_grup'];
$jawaban_detail = $result['jawaban_detail'];

// Hapus session setelah digunakan
unset($_SESSION['quiz_result']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Quiz</title>
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
            max-width: 1000px;
            margin: 0 auto;
        }
        
        .result-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .result-header {
            margin-bottom: 3rem;
        }
        
        .result-header h1 {
            font-size: 3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .quiz-title {
            font-size: 1.5rem;
            color: #667eea;
            margin-bottom: 2rem;
        }
        
        .score-circle {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            margin: 0 auto 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-size: 3rem;
            font-weight: bold;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .score-excellent {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
        }
        
        .score-good {
            background: linear-gradient(135deg, #ffa502 0%, #ff6348 100%);
        }
        
        .score-average {
            background: linear-gradient(135deg, #ff6348 0%, #ff4757 100%);
        }
        
        .score-percentage {
            font-size: 3rem;
            margin-bottom: 0.5rem;
        }
        
        .score-label {
            font-size: 1rem;
            opacity: 0.9;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 2rem;
            margin: 3rem 0;
        }
        
        .stat-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #667eea;
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            font-size: 0.9rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .performance-message {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 15px;
            margin: 2rem 0;
            border-left: 5px solid #667eea;
        }
        
        .performance-message h3 {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .performance-message p {
            color: #666;
            line-height: 1.6;
        }
        
        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 3rem;
        }
        
        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 2px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
            border-color: #667eea;
        }
        
        .detail-section {
            margin-top: 3rem;
            text-align: left;
        }
        
        .detail-section h3 {
            color: #333;
            margin-bottom: 1rem;
            text-align: center;
        }
        
        .answer-item {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            border-left: 4px solid #ddd;
        }
        
        .answer-item.correct {
            border-left-color: #2ed573;
            background: #d4edda;
        }
        
        .answer-item.incorrect {
            border-left-color: #ff4757;
            background: #f8d7da;
        }
        
        .question-text {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .answer-text {
            font-size: 0.9rem;
            color: #666;
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
            
            .result-container {
                padding: 2rem;
            }
            
            .result-header h1 {
                font-size: 2rem;
            }
            
            .score-circle {
                width: 150px;
                height: 150px;
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                align-items: center;
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
            🎯 Hasil Quiz
        </div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <span class="sidebar-title">Menu</span>
            <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">&times;</button>
        </div>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php">👤 Profile</a></li>
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
    <!-- End Sidebar -->

    <div class="page-container">
        <div class="result-container">
            <div class="result-header">
                <h1>🎉 Quiz Selesai!</h1>
                <div class="quiz-title">📚 <?= htmlspecialchars($judul_grup) ?></div>
            </div>
            
            <?php
            $scoreClass = '';
            $message = '';
            if ($percentage >= 80) {
                $scoreClass = 'score-excellent';
                $message = 'Luar biasa! Anda memiliki pemahaman yang sangat baik.';
            } elseif ($percentage >= 60) {
                $scoreClass = 'score-good';
                $message = 'Bagus! Terus tingkatkan lagi untuk hasil yang lebih baik.';
            } else {
                $scoreClass = 'score-average';
                $message = 'Jangan menyerah! Pelajari materi lebih dalam dan coba lagi.';
            }
            ?>
            
            <div class="score-circle <?= $scoreClass ?>">
                <div class="score-percentage"><?= round($percentage, 1) ?>%</div>
                <div class="score-label">Skor Anda</div>
            </div>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?= $score ?></div>
                    <div class="stat-label">Skor Anda</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= $max_score ?></div>
                    <div class="stat-label">Skor Maksimal</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($jawaban_detail['benar']) ?></div>
                    <div class="stat-label">Jawaban Benar</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?= count($jawaban_detail['salah']) ?></div>
                    <div class="stat-label">Jawaban Salah</div>
                </div>
            </div>
            
            <div class="performance-message">
                <h3>💡 Evaluasi Performa</h3>
                <p><?= $message ?></p>
            </div>
            
            <?php if (!empty($jawaban_detail['salah'])): ?>
                <div class="detail-section">
                    <h3>📝 Review Jawaban Salah</h3>
                    <?php foreach ($jawaban_detail['salah'] as $item): ?>
                        <div class="answer-item incorrect">
                            <div class="question-text"><?= htmlspecialchars($item['pertanyaan']) ?></div>
                            <div class="answer-text">
                                <strong>Jawaban Anda:</strong> <?= $item['jawaban_user'] ?> | 
                                <strong>Jawaban Benar:</strong> <?= $item['jawaban_benar'] ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="action-buttons">
                <a href="latihansoal.php" class="btn btn-primary">🔄 Coba Quiz Lain</a>
                <a href="history.php" class="btn btn-secondary">📊 Lihat History</a>
                <a href="leaderboard.php" class="btn btn-secondary">🏆 Lihat Leaderboard</a>
            </div>
        </div>
    </div>

    <script>
        // Animasi untuk score circle
        document.addEventListener('DOMContentLoaded', function() {
            const scoreCircle = document.querySelector('.score-circle');
            const percentage = <?= $percentage ?>;
            
            // Animasi counter
            let currentPercentage = 0;
            const increment = percentage / 50;
            
            const timer = setInterval(() => {
                currentPercentage += increment;
                if (currentPercentage >= percentage) {
                    document.querySelector('.score-percentage').textContent = Math.round(percentage * 10) / 10 + '%';
                    clearInterval(timer);
                } else {
                    document.querySelector('.score-percentage').textContent = Math.round(currentPercentage * 10) / 10 + '%';
                }
            }, 30);
            
            // Animasi untuk stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100 + 500);
            });
        });
        
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
    </script>
</body>
</html>
