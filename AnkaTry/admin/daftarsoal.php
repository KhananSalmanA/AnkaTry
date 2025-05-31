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
    <title>Daftar Soal</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .judul-grup-btn {
            display: block;
            width: 100%;
            text-align: left;
            background: #ede7f6;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            padding: 12px 18px;
            margin-bottom: 14px;
            font-size: 1.1em;
            cursor: pointer;
            font-weight: bold;
        }
        .soal-group { border:1px solid #e0e0e0; padding:16px; margin-bottom:18px; border-radius:8px; background:#fafafa;}
        .jawaban-kunci { color: #388e3c; font-weight: bold; margin-top: 6px; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Daftar Soal</div>
        <div class="navbar-link"><a href="dashboard.php">Kembali ke Dashboard</a></div>
    </nav>
    <div class="page-container">
        <div class="form-container">
            <h1>Daftar Soal</h1>
            <?php if (!isset($_GET['id_grup'])): ?>
                <?php if (count($grupList) > 0): ?>
                    <?php foreach ($grupList as $g): ?>
                        <form method="GET" action="" style="margin-bottom:0;">
                            <input type="hidden" name="id_grup" value="<?= htmlspecialchars($g['id_grup']) ?>">
                            <button type="submit" class="judul-grup-btn"><?= htmlspecialchars($g['judul_grup']) ?></button>
                        </form>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Belum ada judul soal yang ditambahkan.</p>
                <?php endif; ?>
            <?php else: ?>
                <a href="daftarsoal.php" style="display:inline-block;margin-bottom:18px;">&larr; Kembali ke daftar judul soal</a>
                <h2><?= htmlspecialchars($judul_grup) ?></h2>
                <?php if (count($soalList) > 0): ?>
                    <?php $no = 1; foreach ($soalList as $row): ?>
                        <div class="soal-group">
                            <b><?= $no ?>. <?= htmlspecialchars($row['pertanyaan']) ?></b>
                            <div style="margin-left:18px;">
                                a. <?= htmlspecialchars($row['pilihan_a']) ?><br>
                                b. <?= htmlspecialchars($row['pilihan_b']) ?><br>
                                c. <?= htmlspecialchars($row['pilihan_c']) ?><br>
                                d. <?= htmlspecialchars($row['pilihan_d']) ?><br>
                            </div>
                            <div class="jawaban-kunci">
                                Kunci Jawaban: <?= htmlspecialchars($row['jawaban_benar']) ?>. 
                                <?php
                                    $kunci = '';
                                    if ($row['jawaban_benar'] === 'A') $kunci = $row['pilihan_a'];
                                    elseif ($row['jawaban_benar'] === 'B') $kunci = $row['pilihan_b'];
                                    elseif ($row['jawaban_benar'] === 'C') $kunci = $row['pilihan_c'];
                                    elseif ($row['jawaban_benar'] === 'D') $kunci = $row['pilihan_d'];
                                    echo htmlspecialchars($kunci);
                                ?>
                            </div>
                        </div>
                    <?php $no++; endforeach; ?>
                <?php else: ?>
                    <p>Belum ada soal pada judul ini.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
