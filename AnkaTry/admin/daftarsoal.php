<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

// Ambil semua judul grup soal
$grupList = [];
$res = $conn->query("SELECT id_grup, judul_grup FROM grup_soal ORDER BY id_grup DESC");
while ($row = $res->fetch_assoc()) {
    $grupList[] = $row;
}

// Jika judul dipilih, ambil semua soal pada grup tersebut
$soalList = [];
if (isset($_GET['id_grup']) && $_GET['id_grup']) {
    $id_grup = intval($_GET['id_grup']);
    $soalQ = $conn->query("SELECT * FROM soal WHERE id_grup=$id_grup ORDER BY id_soal ASC");
    while ($row = $soalQ->fetch_assoc()) {
        $soalList[] = $row;
    }
    // Ambil judul untuk tampilan
    $judul_grup = '';
    foreach ($grupList as $g) {
        if ($g['id_grup'] == $id_grup) {
            $judul_grup = $g['judul_grup'];
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Soal</title>
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
        
        .form-container h2 {
            color: #667eea;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            border-bottom: 2px solid #e0e0e0;
            padding-bottom: 0.5rem;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 1.5rem;
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: #f0f4ff;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            background: #e0e7ff;
            transform: translateX(-5px);
        }
        
        .judul-grup-btn {
            display: block;
            width: 100%;
            text-align: left;
            background: linear-gradient(135deg, #f5f7ff 0%, #e6e9f5 100%);
            border: 1px solid #ddd;
            border-radius: 12px;
            padding: 1.2rem 1.5rem;
            margin-bottom: 1rem;
            font-size: 1.2rem;
            cursor: pointer;
            font-weight: 600;
            color: #333;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .judul-grup-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 15px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
        
        .judul-grup-btn::after {
            content: "→";
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            opacity: 0;
            transition: all 0.3s;
        }
        
        .judul-grup-btn:hover::after {
            opacity: 1;
            right: 1rem;
        }
        
        .soal-group {
            border: 1px solid #e0e0e0;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border-radius: 12px;
            background: #fafafa;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .soal-group:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }
        
        .soal-pertanyaan {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .soal-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .pilihan-list {
            margin-left: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .pilihan-item {
            padding: 0.5rem 0;
            font-size: 1rem;
            color: #555;
        }
        
        .jawaban-kunci {
            background: #e8f5e9;
            color: #2e7d32;
            font-weight: 600;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
            border-left: 4px solid #2e7d32;
        }
        
        .difficulty-badge {
            display: inline-block;
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 1rem;
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
        
        .empty-message {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-size: 1.2rem;
            background: #f8f9fa;
            border-radius: 12px;
            border: 1px dashed #ddd;
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
            
            .form-container h2 {
                font-size: 1.5rem;
            }
            
            .judul-grup-btn {
                padding: 1rem;
                font-size: 1.1rem;
            }
            
            .soal-group {
                padding: 1rem;
            }
            
            .soal-pertanyaan {
                font-size: 1.1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">📋 Daftar Soal</div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>
    
    <div class="page-container">
        <div class="form-container">
            <h1>📋 Daftar Soal</h1>
            
            <?php if (!isset($_GET['id_grup'])): ?>
                <?php if (count($grupList) > 0): ?>
                    <?php foreach ($grupList as $g): ?>
                        <form method="GET" action="" style="margin-bottom:0;">
                            <input type="hidden" name="id_grup" value="<?= htmlspecialchars($g['id_grup']) ?>">
                            <button type="submit" class="judul-grup-btn">📚 <?= htmlspecialchars($g['judul_grup']) ?></button>
                        </form>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <p>📭 Belum ada judul soal yang ditambahkan.</p>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <a href="daftarsoal.php" class="back-link">← Kembali ke daftar judul soal</a>
                <h2>📚 <?= htmlspecialchars($judul_grup) ?></h2>
                
                <?php if (count($soalList) > 0): ?>
                    <?php $no = 1; foreach ($soalList as $row): ?>
                        <div class="soal-group">
                            <div class="soal-pertanyaan">
                                <?= $no ?>. <?= htmlspecialchars($row['pertanyaan']) ?>
                            </div>
                            
                            <?php if (!empty($row['image'])): ?>
                                <img src="../uploads/soal/<?= htmlspecialchars($row['image']) ?>" alt="Gambar soal" class="soal-image">
                            <?php endif; ?>
                            
                            <div class="pilihan-list">
                                <div class="pilihan-item">A. <?= htmlspecialchars($row['pilihan_a']) ?></div>
                                <div class="pilihan-item">B. <?= htmlspecialchars($row['pilihan_b']) ?></div>
                                <div class="pilihan-item">C. <?= htmlspecialchars($row['pilihan_c']) ?></div>
                                <div class="pilihan-item">D. <?= htmlspecialchars($row['pilihan_d']) ?></div>
                            </div>
                            
                            <div class="jawaban-kunci">
                                ✓ Kunci Jawaban: <?= htmlspecialchars($row['jawaban_benar']) ?>. 
                                <?php
                                    $kunci = '';
                                    if ($row['jawaban_benar'] === 'A') $kunci = $row['pilihan_a'];
                                    elseif ($row['jawaban_benar'] === 'B') $kunci = $row['pilihan_b'];
                                    elseif ($row['jawaban_benar'] === 'C') $kunci = $row['pilihan_c'];
                                    elseif ($row['jawaban_benar'] === 'D') $kunci = $row['pilihan_d'];
                                    echo htmlspecialchars($kunci);
                                ?>
                            </div>
                            
                            <span class="difficulty-badge difficulty-<?= strtolower($row['tingkat_kesulitan']) ?>">
                                <?= $row['tingkat_kesulitan'] ?> 
                                (<?= $row['tingkat_kesulitan'] === 'Mudah' ? '10' : ($row['tingkat_kesulitan'] === 'Sedang' ? '15' : '20') ?> poin)
                            </span>
                        </div>
                    <?php $no++; endforeach; ?>
                <?php else: ?>
                    <div class="empty-message">
                        <p>📭 Belum ada soal pada judul ini.</p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Animasi untuk soal groups
        document.addEventListener('DOMContentLoaded', function() {
            const soalGroups = document.querySelectorAll('.soal-group');
            soalGroups.forEach((group, index) => {
                group.style.opacity = '0';
                group.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    group.style.transition = 'all 0.5s ease';
                    group.style.opacity = '1';
                    group.style.transform = 'translateY(0)';
                }, index * 100);
            });
            
            // Animasi untuk judul grup buttons
            const judulButtons = document.querySelectorAll('.judul-grup-btn');
            judulButtons.forEach((button, index) => {
                button.style.opacity = '0';
                button.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    button.style.transition = 'all 0.5s ease';
                    button.style.opacity = '1';
                    button.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>
