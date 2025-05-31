<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../connsoal.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soal'])) {
    $judul = trim($_POST['judul']);
    $jumlah = count($_POST['soal']);
    $berhasil = 0;

    // Simpan judul ke tabel grup_soal
    $id_grup = null;
    if ($judul) {
        $sql_grup = "INSERT INTO grup_soal (judul_grup) VALUES (?)";
        $stmt_grup = $koneksi->prepare($sql_grup);
        $stmt_grup->bind_param("s", $judul);
        if ($stmt_grup->execute()) {
            $id_grup = $stmt_grup->insert_id;
        }
        $stmt_grup->close();
    }

    foreach ($_POST['soal'] as $idx => $soal) {
        $pertanyaan = $soal['pertanyaan'];
        $pilihan_a = $soal['pilihan_a'];
        $pilihan_b = $soal['pilihan_b'];
        $pilihan_c = $soal['pilihan_c'];
        $pilihan_d = $soal['pilihan_d'];
        $jawaban_benar = $soal['jawaban_benar'];
        $tingkat_kesulitan = $soal['tingkat_kesulitan'];
        
        // Handle image upload
        $image_name = null;
        if (isset($_FILES['soal']['name'][$idx]['image']) && $_FILES['soal']['name'][$idx]['image']) {
            $allowed = ['jpg','jpeg','png','gif'];
            $ext = strtolower(pathinfo($_FILES['soal']['name'][$idx]['image'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed)) {
                $upload_dir = '../uploads/soal/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                $image_name = uniqid('soal_').'.'.$ext;
                move_uploaded_file($_FILES['soal']['tmp_name'][$idx]['image'], $upload_dir.$image_name);
            }
        }

        if ($pertanyaan && $pilihan_a && $pilihan_b && $pilihan_c && $pilihan_d && $jawaban_benar && $tingkat_kesulitan && $id_grup) {
            $sql = "INSERT INTO soal (id_grup, pertanyaan, image, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, tingkat_kesulitan) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $koneksi->prepare($sql);
            $stmt->bind_param("issssssss", $id_grup, $pertanyaan, $image_name, $pilihan_a, $pilihan_b, $pilihan_c, $pilihan_d, $jawaban_benar, $tingkat_kesulitan);
            if ($stmt->execute()) $berhasil++;
            $stmt->close();
        }
    }
    $message = "Soal berhasil dibuat dengan judul <b>$judul</b> ($berhasil dari $jumlah soal diupload)!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kumpulan Soal</title>
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
        
        .judul-grup {
            width: 100%;
            padding: 1rem;
            margin-bottom: 2rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1.2rem;
            transition: border-color 0.3s;
        }
        
        .judul-grup:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .soal-group {
            border: 2px solid #e0e0e0;
            padding: 2rem;
            margin-bottom: 2rem;
            border-radius: 15px;
            background: #fafafa;
            position: relative;
            transition: all 0.3s;
        }
        
        .soal-group:hover {
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }
        
        .soal-group h3 {
            margin-bottom: 1rem;
            color: #333;
            font-size: 1.3rem;
        }
        
        .remove-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        
        .remove-btn:hover {
            background: #ff3742;
            transform: scale(1.1);
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group textarea,
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group textarea:focus,
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .image-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s;
            width: 100%;
        }
        
        .image-upload:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .image-upload input {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 10px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s;
            margin: 0.5rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .btn-add {
            background: linear-gradient(135deg, #2ed573 0%, #1e90ff 100%);
        }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            border: 1px solid #c3e6cb;
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
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Tambah Kumpulan Soal</div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>
    
    <div class="page-container">
        <div class="form-container">
            <h1>📝 Buat Soal Baru</h1>
            <?php if ($message): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" id="soalForm" enctype="multipart/form-data">
                <input type="text" name="judul" class="judul-grup" placeholder="Masukkan Judul Kumpulan Soal..." required>
                
                <div id="soal-list">
                    <div class="soal-group">
                        <h3>Soal <span class="soal-no">1</span></h3>
                        <button type="button" class="remove-btn" onclick="removeSoal(this)" style="display:none;">&times;</button>
                        
                        <div class="form-group">
                            <label>Pertanyaan:</label>
                            <textarea name="soal[0][pertanyaan]" placeholder="Tulis pertanyaan di sini..." required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label>Gambar (Opsional):</label>
                            <div class="image-upload">
                                <input type="file" name="soal[0][image]" accept="image/*" onchange="previewImage(this)">
                                <span>📷 Klik untuk upload gambar</span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilihan A:</label>
                            <input type="text" name="soal[0][pilihan_a]" placeholder="Pilihan A" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilihan B:</label>
                            <input type="text" name="soal[0][pilihan_b]" placeholder="Pilihan B" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilihan C:</label>
                            <input type="text" name="soal[0][pilihan_c]" placeholder="Pilihan C" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Pilihan D:</label>
                            <input type="text" name="soal[0][pilihan_d]" placeholder="Pilihan D" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Jawaban Benar:</label>
                            <select name="soal[0][jawaban_benar]" required>
                                <option value="">Pilih Jawaban Benar</option>
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Tingkat Kesulitan:</label>
                            <select name="soal[0][tingkat_kesulitan]" required>
                                <option value="">Pilih Tingkat Kesulitan</option>
                                <option value="Mudah">Mudah (10 poin)</option>
                                <option value="Sedang">Sedang (15 poin)</option>
                                <option value="Sulit">Sulit (20 poin)</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="button" class="btn btn-add" onclick="tambahSoal()">+ Tambah Soal</button>
                    <button type="submit" class="btn" name="upload">🚀 Upload Semua Soal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let soalIndex = 1;
        
        function tambahSoal() {
            const soalList = document.getElementById('soal-list');
            const soalGroup = document.createElement('div');
            soalGroup.className = 'soal-group';
            soalGroup.innerHTML = `
                <h3>Soal <span class="soal-no"></span></h3>
                <button type="button" class="remove-btn" onclick="removeSoal(this)">&times;</button>
                
                <div class="form-group">
                    <label>Pertanyaan:</label>
                    <textarea name="soal[${soalIndex}][pertanyaan]" placeholder="Tulis pertanyaan di sini..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Gambar (Opsional):</label>
                    <div class="image-upload">
                        <input type="file" name="soal[${soalIndex}][image]" accept="image/*" onchange="previewImage(this)">
                        <span>📷 Klik untuk upload gambar</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Pilihan A:</label>
                    <input type="text" name="soal[${soalIndex}][pilihan_a]" placeholder="Pilihan A" required>
                </div>
                
                <div class="form-group">
                    <label>Pilihan B:</label>
                    <input type="text" name="soal[${soalIndex}][pilihan_b]" placeholder="Pilihan B" required>
                </div>
                
                <div class="form-group">
                    <label>Pilihan C:</label>
                    <input type="text" name="soal[${soalIndex}][pilihan_c]" placeholder="Pilihan C" required>
                </div>
                
                <div class="form-group">
                    <label>Pilihan D:</label>
                    <input type="text" name="soal[${soalIndex}][pilihan_d]" placeholder="Pilihan D" required>
                </div>
                
                <div class="form-group">
                    <label>Jawaban Benar:</label>
                    <select name="soal[${soalIndex}][jawaban_benar]" required>
                        <option value="">Pilih Jawaban Benar</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Tingkat Kesulitan:</label>
                    <select name="soal[${soalIndex}][tingkat_kesulitan]" required>
                        <option value="">Pilih Tingkat Kesulitan</option>
                        <option value="Mudah">Mudah (10 poin)</option>
                        <option value="Sedang">Sedang (15 poin)</option>
                        <option value="Sulit">Sulit (20 poin)</option>
                    </select>
                </div>
            `;
            
            soalList.appendChild(soalGroup);
            updateNomorSoal();
            soalIndex++;
            
            // Smooth scroll to new question
            soalGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
        
        function removeSoal(btn) {
            btn.closest('.soal-group').remove();
            updateNomorSoal();
        }
        
        function updateNomorSoal() {
            const groups = document.querySelectorAll('.soal-group');
            groups.forEach((group, idx) => {
                group.querySelector('.soal-no').textContent = idx + 1;
                const removeBtn = group.querySelector('.remove-btn');
                removeBtn.style.display = (groups.length === 1) ? 'none' : 'block';
            });
        }
        
        function previewImage(input) {
            const uploadDiv = input.parentElement;
            const span = uploadDiv.querySelector('span');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    span.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 100px; border-radius: 5px;">`;
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                span.innerHTML = '📷 Klik untuk upload gambar';
            }
        }
        
        // Form validation
        document.getElementById('soalForm').addEventListener('submit', function(e) {
            const judul = document.querySelector('input[name="judul"]').value.trim();
            if (!judul) {
                e.preventDefault();
                alert('Judul kumpulan soal harus diisi!');
                return;
            }
            
            const soalGroups = document.querySelectorAll('.soal-group');
            if (soalGroups.length === 0) {
                e.preventDefault();
                alert('Minimal harus ada 1 soal!');
                return;
            }
            
            // Show loading
            const submitBtn = document.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '⏳ Mengupload...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>
