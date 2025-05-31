<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

// Ambil semua materi
$materi_query = "SELECT * FROM materi ORDER BY created_at DESC";
$materi = $conn->query($materi_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Materi Pembelajaran</title>
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
        
        .materi-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        
        .materi-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .materi-header h1 {
            font-size: 3rem;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .materi-header p {
            font-size: 1.2rem;
            color: #666;
        }
        
        .materi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
        }
        
        .materi-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s;
            border-left: 5px solid #667eea;
        }
        
        .materi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.15);
        }
        
        .materi-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .materi-content {
            margin-bottom: 1.5rem;
        }
        
        .materi-description {
            font-size: 1rem;
            line-height: 1.6;
            color: #333;
            margin-bottom: 1rem;
        }
        
        .materi-date {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 1rem;
        }
        
        .materi-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
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
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .btn-secondary {
            background: #f8f9fa;
            color: #333;
            border: 1px solid #ddd;
        }
        
        .btn-secondary:hover {
            background: #e9ecef;
        }
        
        .no-materi {
            text-align: center;
            padding: 3rem;
            color: #666;
        }
        
        .no-materi h3 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .no-materi p {
            font-size: 1.1rem;
        }
        
        @media (max-width: 768px) {
            .page-container {
                padding: 1rem;
            }
            
            .materi-container {
                padding: 2rem;
            }
            
            .materi-header h1 {
                font-size: 2rem;
            }
            
            .materi-grid {
                grid-template-columns: 1fr;
            }
            
            .materi-actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">📚 Materi Pembelajaran</div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>
    
    <div class="page-container">
        <div class="materi-container">
            <div class="materi-header">
                <h1>📚 Materi Pembelajaran</h1>
                <p>Akses berbagai materi pembelajaran untuk persiapan UTBK</p>
            </div>
            
            <?php if ($materi && $materi->num_rows > 0): ?>
                <div class="materi-grid">
                    <?php while ($row = $materi->fetch_assoc()): 
                        $date = new DateTime($row['created_at']);
                        $formatted_date = $date->format('d M Y');
                    ?>
                        <div class="materi-card">
                            <?php if ($row['image']): ?>
                                <img src="../uploads/materi/<?= htmlspecialchars($row['image']) ?>" alt="Materi Image" class="materi-image">
                            <?php endif; ?>
                            
                            <div class="materi-content">
                                <div class="materi-description">
                                    <?= nl2br(htmlspecialchars($row['deskripsi'])) ?>
                                </div>
                                <div class="materi-date">
                                    📅 Ditambahkan: <?= $formatted_date ?>
                                </div>
                            </div>
                            
                            <div class="materi-actions">
                                <?php if ($row['file']): ?>
                                    <a href="../uploads/materi/<?= htmlspecialchars($row['file']) ?>" target="_blank" class="btn btn-primary">
                                        📄 Download File
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($row['link']): ?>
                                    <a href="<?= htmlspecialchars($row['link']) ?>" target="_blank" class="btn btn-secondary">
                                        🔗 Buka Link
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="no-materi">
                    <h3>📭 Belum Ada Materi</h3>
                    <p>Materi pembelajaran belum tersedia. Silakan cek kembali nanti.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Animasi untuk materi cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.materi-card');
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
