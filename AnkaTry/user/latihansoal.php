<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
include '../connsoal.php';

// Ambil daftar judul kumpulan soal (grup_soal)
$judulList = [];
$res = $koneksi->query("SELECT id_grup, judul_grup FROM grup_soal ORDER BY id_grup DESC");
while ($row = $res->fetch_assoc()) {
    $judulList[] = $row;
}

// Proses submit jawaban
$score = null;
$max_score = null;
$feedback = "";
$user_id = null;

// Ambil user ID
$username = $_SESSION['username'];
$user_query = $koneksi->query("SELECT id FROM users WHERE username = '$username'");
$user_data = $user_query->fetch_assoc();
$user_id = $user_data['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_grup']) && isset($_POST['jawaban'])) {
    $id_grup = intval($_POST['id_grup']);
    $jawaban = $_POST['jawaban'];
    $score = 0;
    $max_score = 0;
    
    // Ambil semua soal pada grup ini
    $soalQ = $koneksi->query("SELECT id_soal, jawaban_benar, tingkat_kesulitan FROM soal WHERE id_grup=$id_grup");
    $soalMap = [];
    while ($s = $soalQ->fetch_assoc()) {
        $soalMap[$s['id_soal']] = $s;
        // Hitung skor maksimal
        if ($s['tingkat_kesulitan'] === 'Mudah') $max_score += 10;
        elseif ($s['tingkat_kesulitan'] === 'Sedang') $max_score += 15;
        elseif ($s['tingkat_kesulitan'] === 'Sulit') $max_score += 20;
    }
    
    foreach ($jawaban as $id => $jawab) {
        if (isset($soalMap[$id]) && $jawab === $soalMap[$id]['jawaban_benar']) {
            if ($soalMap[$id]['tingkat_kesulitan'] === 'Mudah') $score += 10;
            elseif ($soalMap[$id]['tingkat_kesulitan'] === 'Sedang') $score += 15;
            elseif ($soalMap[$id]['tingkat_kesulitan'] === 'Sulit') $score += 20;
        }
    }
    
    $percentage = ($max_score > 0) ? ($score / $max_score) * 100 : 0;
    
    // Simpan ke history
    $history_sql = "INSERT INTO quiz_history (user_id, id_grup, score, max_score, percentage) VALUES (?, ?, ?, ?, ?)";
    $stmt = $koneksi->prepare($history_sql);
    $stmt->bind_param("iiiii", $user_id, $id_grup, $score, $max_score, $percentage);
    $stmt->execute();
    $stmt->close();
    
    // Simpan hasil ke session untuk halaman hasil
    $_SESSION['quiz_result'] = [
        'score' => $score,
        'max_score' => $max_score,
        'percentage' => $percentage,
        'judul_grup' => $grup_data['judul_grup'],
        'jawaban_detail' => [
            'benar' => [],
            'salah' => []
        ]
    ];

    // Ambil detail jawaban untuk review
    foreach ($jawaban as $id => $jawab) {
        if (isset($soalMap[$id])) {
            $soal_detail = $koneksi->query("SELECT pertanyaan FROM soal WHERE id_soal = $id")->fetch_assoc();
            if ($jawab === $soalMap[$id]['jawaban_benar']) {
                $_SESSION['quiz_result']['jawaban_detail']['benar'][] = [
                    'pertanyaan' => $soal_detail['pertanyaan'],
                    'jawaban_user' => $jawab,
                    'jawaban_benar' => $soalMap[$id]['jawaban_benar']
                ];
            } else {
                $_SESSION['quiz_result']['jawaban_detail']['salah'][] = [
                    'pertanyaan' => $soal_detail['pertanyaan'],
                    'jawaban_user' => $jawab,
                    'jawaban_benar' => $soalMap[$id]['jawaban_benar']
                ];
            }
        }
    }

    // Redirect ke halaman hasil
    header("Location: hasil-quiz.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Latihan Soal</title>
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
        
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .form-container h1 {
            text-align: center;
            color: #333;
            margin-bottom: 2rem;
            font-size: 2.5rem;
        }
        
        .quiz-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .quiz-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .quiz-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }
        
        .soal-group {
            border: 2px solid #e0e0e0;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 15px;
            background: #fafafa;
            transition: all 0.3s;
        }
        
        .soal-group:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }
        
        .soal-question {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .soal-image {
            max-width: 100%;
            height: auto;
            border-radius: 10px;
            margin: 1rem 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .options {
            display: grid;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        
        .option {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .option:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .option input[type="radio"] {
            margin-right: 1rem;
            transform: scale(1.2);
        }
        
        .option label {
            cursor: pointer;
            font-size: 1rem;
            flex: 1;
        }
        
        .difficulty-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .difficulty-mudah {
            background: #d4edda;
            color: #155724;
        }
        
        .difficulty-sedang {
            background: #fff3cd;
            color: #856404;
        }
        
        .difficulty-sulit {
            background: #f8d7da;
            color: #721c24;
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: block;
            margin: 2rem auto;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(46, 213, 115, 0.3);
        }
        
        .result-card {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
            color: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
            animation: slideIn 0.5s ease;
        }
        
        .result-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .result-card p {
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .timer {
            position: fixed;
            top: 100px;
            right: 20px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            font-weight: 600;
            color: #333;
            z-index: 1000;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }
            
            .form-container {
                padding: 2rem;
            }
            
            .form-container h1 {
                font-size: 2rem;
            }
            
            .quiz-selector {
                grid-template-columns: 1fr;
            }
            
            .timer {
                position: static;
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">📝 Latihan Soal</div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>
    
    <div class="page-container">
        <div class="form-container">
            <h1>🎯 Pilih Quiz yang Ingin Dikerjakan</h1>
            
            <?php if ($feedback): ?>
                <?= $feedback ?>
            <?php endif; ?>
            
            <?php if (!isset($_GET['id_grup'])): ?>
                <div class="quiz-selector">
                    <?php foreach ($judulList as $j): ?>
                        <form method="GET" action="">
                            <input type="hidden" name="id_grup" value="<?= htmlspecialchars($j['id_grup']) ?>">
                            <button type="submit" class="quiz-card">
                                📚 <?= htmlspecialchars($j['judul_grup']) ?>
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <?php
                $id_grup = intval($_GET['id_grup']);
                $soalQ = $koneksi->query("SELECT * FROM soal WHERE id_grup=$id_grup ORDER BY id_soal ASC");
                
                // Ambil judul grup
                $grup_query = $koneksi->query("SELECT judul_grup FROM grup_soal WHERE id_grup=$id_grup");
                $grup_data = $grup_query->fetch_assoc();
                $judul_grup = $grup_data['judul_grup'];
                ?>
                
                <div style="text-align: center; margin-bottom: 2rem;">
                    <h2 style="color: #333; margin-bottom: 1rem;">📖 <?= htmlspecialchars($judul_grup) ?></h2>
                    <a href="latihansoal.php" style="color: #667eea; text-decoration: none; font-weight: 500;">← Kembali ke daftar quiz</a>
                </div>
                
                <div class="timer" id="timer">
                    ⏱️ Waktu: <span id="time">00:00</span>
                </div>
                
                <?php if ($soalQ && $soalQ->num_rows > 0): ?>
                    <form method="POST" action="" id="quizForm">
                        <?php 
                        $no = 1;
                        while ($row = $soalQ->fetch_assoc()): 
                        ?>
                            <div class="soal-group">
                                <div class="soal-question">
                                    <?= $no ?>. <?= htmlspecialchars($row['pertanyaan']) ?>
                                </div>
                                
                                <?php if ($row['image']): ?>
                                    <img src="../uploads/soal/<?= htmlspecialchars($row['image']) ?>" alt="Gambar soal" class="soal-image">
                                <?php endif; ?>
                                
                                <div class="options">
                                    <?php foreach (['A','B','C','D'] as $opt): ?>
                                        <div class="option">
                                            <input type="radio" name="jawaban[<?= $row['id_soal'] ?>]" value="<?= $opt ?>" id="soal_<?= $row['id_soal'] ?>_<?= $opt ?>" required>
                                            <label for="soal_<?= $row['id_soal'] ?>_<?= $opt ?>">
                                                <?= $opt ?>. <?= htmlspecialchars($row['pilihan_'.strtolower($opt)]) ?>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                
                                <span class="difficulty-badge difficulty-<?= strtolower($row['tingkat_kesulitan']) ?>">
                                    <?= $row['tingkat_kesulitan'] ?> 
                                    (<?= $row['tingkat_kesulitan'] === 'Mudah' ? '10' : ($row['tingkat_kesulitan'] === 'Sedang' ? '15' : '20') ?> poin)
                                </span>
                            </div>
                        <?php 
                        $no++;
                        endwhile; 
                        ?>
                        
                        <input type="hidden" name="id_grup" value="<?= $id_grup ?>">
                        <button type="submit" class="submit-btn" onclick="return confirmSubmit()">
                            🚀 Kumpulkan Jawaban
                        </button>
                    </form>
                <?php else: ?>
                    <div style="text-align: center; padding: 3rem; color: #666;">
                        <h3>📭 Tidak ada soal pada quiz ini</h3>
                        <p>Silakan pilih quiz lain atau hubungi admin.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Timer functionality
        let startTime = Date.now();
        let timerInterval;
        
        function updateTimer() {
            const elapsed = Date.now() - startTime;
            const minutes = Math.floor(elapsed / 60000);
            const seconds = Math.floor((elapsed % 60000) / 1000);
            document.getElementById('time').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }
        
        if (document.getElementById('timer')) {
            timerInterval = setInterval(updateTimer, 1000);
        }
        
        // Konfirmasi submit
        function confirmSubmit() {
            const form = document.getElementById('quizForm');
            const radios = form.querySelectorAll('input[type="radio"]');
            const questions = form.querySelectorAll('.soal-group');
            
            let answered = 0;
            questions.forEach(question => {
                const questionRadios = question.querySelectorAll('input[type="radio"]');
                const isAnswered = Array.from(questionRadios).some(radio => radio.checked);
                if (isAnswered) answered++;
            });
            
            if (answered < questions.length) {
                return confirm(`Anda baru menjawab ${answered} dari ${questions.length} soal. Yakin ingin mengumpulkan?`);
            }
            
            clearInterval(timerInterval);
            return confirm('Yakin ingin mengumpulkan jawaban? Pastikan semua jawaban sudah benar.');
        }
        
        // Auto-save progress (optional)
        function saveProgress() {
            const form = document.getElementById('quizForm');
            if (form) {
                const formData = new FormData(form);
                const answers = {};
                for (let [key, value] of formData.entries()) {
                    if (key.startsWith('jawaban[')) {
                        answers[key] = value;
                    }
                }
                localStorage.setItem('quiz_progress_' + <?= isset($_GET['id_grup']) ? $_GET['id_grup'] : 0 ?>, JSON.stringify(answers));
            }
        }
        
        // Load saved progress
        function loadProgress() {
            const saved = localStorage.getItem('quiz_progress_' + <?= isset($_GET['id_grup']) ? $_GET['id_grup'] : 0 ?>);
            if (saved) {
                const answers = JSON.parse(saved);
                for (let [key, value] of Object.entries(answers)) {
                    const input = document.querySelector(`input[name="${key}"][value="${value}"]`);
                    if (input) input.checked = true;
                }
            }
        }
        
        // Event listeners
        document.addEventListener('DOMContentLoaded', function() {
            loadProgress();
            
            // Save progress on radio change
            document.querySelectorAll('input[type="radio"]').forEach(radio => {
                radio.addEventListener('change', saveProgress);
            });
            
            // Smooth scroll animation for quiz cards
            document.querySelectorAll('.quiz-card').forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
        
        // Clear saved progress on successful submit
        window.addEventListener('beforeunload', function() {
            if (document.querySelector('.result-card')) {
                localStorage.removeItem('quiz_progress_' + <?= isset($_GET['id_grup']) ? $_GET['id_grup'] : 0 ?>);
            }
        });
    </script>
</body>
</html>
