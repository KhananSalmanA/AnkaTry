<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

// Ambil daftar judul kumpulan soal (grup_soal)
$judulList = [];
$res = $conn->query("SELECT id_grup, judul_grup FROM grup_soal ORDER BY id_grup DESC");
while ($row = $res->fetch_assoc()) {
    $judulList[] = $row;
}

// Proses submit jawaban
$score = null;
$max_score = null;
$feedback = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_grup']) && isset($_POST['jawaban'])) {
    $id_grup = intval($_POST['id_grup']);
    $jawaban = $_POST['jawaban'];
    $score = 0;
    $max_score = 0;
    // Ambil semua soal pada grup ini
    $soalQ = $conn->query("SELECT id, jawaban_benar, tingkat_kesulitan FROM soal WHERE id_grup=$id_grup");
    $soalMap = [];
    while ($s = $soalQ->fetch_assoc()) {
        $soalMap[$s['id']] = $s;
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
    $feedback = "<p><b>Skor Anda: $score / $max_score</b></p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Latihan Soal</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .judul-grup-select { width: 100%; padding: 10px; margin-bottom: 18px; border-radius: 6px; border: 1px solid #bdbdbd; font-size: 1.1rem;}
        .soal-group { border:1px solid #e0e0e0; padding:16px; margin-bottom:18px; border-radius:8px; background:#fafafa;}
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Latihan Soal</div>
        <div class="navbar-link"><a href="dashboard.php">Kembali ke Dashboard</a></div>
    </nav>
    <div class="page-container">
        <div class="form-container">
            <h1>Pilih soal yang ingin dikerjakan</h1>
            <?php if ($feedback) echo $feedback; ?>
            <?php
            // Tampilkan semua judul soal dari database
            foreach ($judulList as $j) {
                // Jika user sudah memilih judul soal, jangan tampilkan tombol pilih lagi
                if (isset($_GET['id_grup']) && $_GET['id_grup'] == $j['id_grup']) continue;
                echo "<div style='margin-bottom:24px;'>";
                echo "<form method='GET' action='' style='display:inline;'>";
                echo "<input type='hidden' name='id_grup' value='".htmlspecialchars($j['id_grup'])."'>";
                echo "<button type='submit' style='font-size:1.1em;padding:8px 18px;border-radius:6px;margin-bottom:6px;cursor:pointer;'>" . htmlspecialchars($j['judul_grup']) . "</button>";
                echo "</form>";
                echo "</div>";
            }
            // Jika judul/grup dipilih, tampilkan soal-soalnya
            if (isset($_GET['id_grup']) && $_GET['id_grup']) {
                $id_grup = intval($_GET['id_grup']);
                $soalQ = $conn->query("SELECT * FROM soal WHERE id_grup=$id_grup ORDER BY id ASC");
                if ($soalQ && $soalQ->num_rows > 0) {
                    echo '<form method="POST" action="">';
                    $no = 1;
                    while ($row = $soalQ->fetch_assoc()) {
                        echo "<div class='soal-group'>";
                        echo "<b>$no. ".htmlspecialchars($row['pertanyaan'])."</b><br>";
                        foreach (['A','B','C','D'] as $opt) {
                            $pilihan = $row['pilihan_'.strtolower($opt)];
                            echo "<label><input type='radio' name='jawaban[{$row['id']}]' value='$opt' required> $opt. ".htmlspecialchars($pilihan)."</label><br>";
                        }
                        echo "<span style='font-size:0.95em;color:#888;'>Tingkat: {$row['tingkat_kesulitan']}</span>";
                        echo "</div>";
                        $no++;
                    }
                    echo "<input type='hidden' name='id_grup' value='$id_grup'>";
                    echo '<button type="submit">Kumpulkan Jawaban</button>';
                    echo '</form>';
                } else {
                    echo "<p>Tidak ada soal pada judul ini.</p>";
                }
            }
            ?>
        </div>
    </div>
</body>
</html>
