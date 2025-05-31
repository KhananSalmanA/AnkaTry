<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

// Ambil user ID
$username = $_SESSION['username'];
$user_query = $conn->query("SELECT id FROM users WHERE username = '$username'");
$user_data = $user_query->fetch_assoc();
$user_id = $user_data['id'];

// Ambil history quiz user
$history_query = "
    SELECT 
        qh.*,
        gs.judul_grup
    FROM quiz_history qh
    JOIN grup_soal gs ON qh.id_grup = gs.id_grup
    WHERE qh.user_id = ?
    ORDER BY qh.completed_at DESC
";
$stmt = $conn->prepare($history_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>History Quiz</title>
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
        
        .history-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .history-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .history-header h1 {
            font-size: 3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .history-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .history-grid {
            display: grid;
            gap: 1.5rem;
        }
        
        .history-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border-left: 5px solid #667eea;
        }
        
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .history-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }
        
        .quiz-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .quiz-date {
            color: #666;
            font-size: 0.9rem;
        }
        
        .score-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .score-excellent {
            background: #d4edda;
            color: #155724;
        }
        
        .score-good {
            background: #fff3cd;
            color: #856404;
        }
        
        .score-average {
            background: #f8d7da;
            color: #721c24;
        }
        
        .history-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .stat-item {
            text-align: center;
            padding: 1rem;
            background: #f8f9fa;
            border-radius: 10px;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .no-history {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-history h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .no-history p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        
        .start-quiz-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        
        .start-quiz-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }
            
            .history-container {
                padding: 2rem;
            }
            
            .history-header h1 {
                font-size: 2rem;
            }
            
            .history-card-header {
                flex-direction: column;
                gap: 1rem;
            }
            
            .history-stats {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">📊 History Quiz</div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>
    
    <div class="page-container">
        <div class="history-container">
            <div class="history-header">
                <h1>📊 Riwayat Quiz Anda</h1>
                <p>Lihat semua quiz yang pernah Anda kerjakan beserta skornya</p>
            </div>
            
            <?php if ($history && $history->num_rows > 0): ?>
                <div class="history-grid">
                    <?php while ($row = $history->fetch_assoc()): 
                        $percentage = round($row['percentage'], 1);
                        $scoreClass = '';
                        if ($percentage >= 80) $scoreClass = 'score-excellent';
                        elseif ($percentage >= 60) $scoreClass = 'score-good';
                        else $scoreClass = 'score-average';
                        
                        $date = new DateTime($row['completed_at']);
                        $formatted_date = $date->format('d M Y, H:i');
                    ?>
                        <div class="history-card">
                            <div class="history-card-header">
                                <div>
                                    <div class="quiz-title">📚 <?= htmlspecialchars($row['judul_grup']) ?></div>
                                    <div class="quiz-date">🕒 <?= $formatted_date ?></div>
                                </div>
                                <div class="score-badge <?= $scoreClass ?>">
                                    <?= $percentage ?>%
                                </div>
                            </div>
                            
                            <div class="history-stats">
                                <div class="stat-item">
                                    <div class="stat-number"><?= $row['score'] ?></div>
                                    <div class="stat-label">Score</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $row['max_score'] ?></div>
                                    <div class="stat-label">Max Score</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-number"><?= $percentage ?>%</div>
                                    <div class="stat-label">Persentase</div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-history">
                    <h3>📭 Belum Ada Riwayat Quiz</h3>
                    <p>Anda belum pernah mengerjakan quiz. Mulai sekarang untuk melihat riwayat di sini!</p>
                    <a href="latihansoal.php" class="start-quiz-btn">🚀 Mulai Quiz</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Animasi untuk history cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.history-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
