<?php
include 'connsoal.php';
?>

<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['soal'])) {
    $judul = trim($_POST['judul']);
    $jumlah = count($_POST['soal']);
    $berhasil = 0;

    // Simpan judul ke tabel grup_soal
    $id_grup = null;
    if ($judul) {
        $sql_grup = "INSERT INTO grup_soal (judul_grup) VALUES (?)";
        $stmt_grup = $conn->prepare($sql_grup);
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

        if ($pertanyaan && $pilihan_a && $pilihan_b && $pilihan_c && $pilihan_d && $jawaban_benar && $tingkat_kesulitan && $id_grup) {
            $sql = "INSERT INTO soal (id_grup, pertanyaan, pilihan_a, pilihan_b, pilihan_c, pilihan_d, jawaban_benar, tingkat_kesulitan) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isssssss", $id_grup, $pertanyaan, $pilihan_a, $pilihan_b, $pilihan_c, $pilihan_d, $jawaban_benar, $tingkat_kesulitan);
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
    <title>Tambah Kumpulan Soal</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .soal-group { border:1px solid #e0e0e0; padding:16px; margin-bottom:18px; border-radius:8px; background:#fafafa;}
        .soal-group h3 { margin-top:0; }
        .remove-btn { background:#e57373; color:#fff; border:none; border-radius:4px; padding:4px 10px; cursor:pointer; float:right;}
        .remove-btn:hover { background:#c62828;}
        .judul-grup { width:100%; padding:10px; margin-bottom:18px; border-radius:6px; border:1px solid #bdbdbd; font-size:1.1rem;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Tambah Kumpulan Soal</div>
        <div class="navbar-link"><a href="dashboard.php">Kembali ke Dashboard</a></div>
    </nav>
    <div class="page-container">
        <div class="form-container">
            <h1>Tambah Soal</h1>
            <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
            <form method="POST" action="" id="soalForm">
                <input type="text" name="judul" class="judul-grup" placeholder="Judul" required>
                <div id="soal-list">
                    <div class="soal-group">
                        <h3>Soal <span class="soal-no">1</span>
                            <button type="button" class="remove-btn" onclick="removeSoal(this)" style="display:none;">Hapus</button>
                        </h3>
                        <textarea name="soal[0][pertanyaan]" placeholder="Tulis pertanyaan..." required style="width:100%;height:80px;"></textarea>
                        <input type="text" name="soal[0][pilihan_a]" placeholder="Pilihan A" required>
                        <input type="text" name="soal[0][pilihan_b]" placeholder="Pilihan B" required>
                        <input type="text" name="soal[0][pilihan_c]" placeholder="Pilihan C" required>
                        <input type="text" name="soal[0][pilihan_d]" placeholder="Pilihan D" required>
                        <select name="soal[0][jawaban_benar]" required>
                            <option value="">Pilih Jawaban Benar</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                        <select name="soal[0][tingkat_kesulitan]" required>
                            <option value="">Tingkat Kesulitan</option>
                            <option value="Mudah">Mudah</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Sulit">Sulit</option>
                        </select>
                    </div>
                </div>
                <button type="button" onclick="tambahSoal()" style="margin-bottom:14px;">+ Tambah Soal</button>
                <button type="submit" name="upload">Upload</button>
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
                <h3>Soal <span class="soal-no"></span>
                    <button type="button" class="remove-btn" onclick="removeSoal(this)">Hapus</button>
                </h3>
                <textarea name="soal[${soalIndex}][pertanyaan]" placeholder="Tulis pertanyaan..." required style="width:100%;height:80px;"></textarea>
                <input type="text" name="soal[${soalIndex}][pilihan_a]" placeholder="Pilihan A" required>
                <input type="text" name="soal[${soalIndex}][pilihan_b]" placeholder="Pilihan B" required>
                <input type="text" name="soal[${soalIndex}][pilihan_c]" placeholder="Pilihan C" required>
                <input type="text" name="soal[${soalIndex}][pilihan_d]" placeholder="Pilihan D" required>
                <select name="soal[${soalIndex}][jawaban_benar]" required>
                    <option value="">Pilih Jawaban Benar</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
                <select name="soal[${soalIndex}][tingkat_kesulitan]" required>
                    <option value="">Tingkat Kesulitan</option>
                    <option value="Mudah">Mudah</option>
                    <option value="Sedang">Sedang</option>
                    <option value="Sulit">Sulit</option>
                </select>
            `;
            soalList.appendChild(soalGroup);
            updateNomorSoal();
            soalIndex++;
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
                removeBtn.style.display = (groups.length === 1) ? 'none' : '';
            });
        }
    </script>
</body>
</html>
