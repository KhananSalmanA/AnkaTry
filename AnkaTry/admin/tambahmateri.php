<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = $_POST['deskripsi'];
    $link = trim($_POST['link']);
    $file_name = null;

    // Proses upload file jika ada
    if (isset($_FILES['file']) && $_FILES['file']['name']) {
        $allowed = ['pdf','doc','docx','ppt','pptx','jpg','jpeg','png','mp4','zip','rar'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = '../uploads/materi/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('materi_').'.'.$ext;
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.$file_name)) {
                $file_name = null;
                $message = "Gagal upload file!";
            }
        } else {
            $message = "Tipe file tidak didukung!";
        }
    }

    if ($deskripsi && ($file_name || $link)) {
        $sql = "INSERT INTO materi (deskripsi, file, link, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $deskripsi, $file_name, $link);
        if ($stmt->execute()) {
            $message = "Materi berhasil ditambahkan!";
        } else {
            $message = "Gagal menambah materi: " . $stmt->error;
        }
        $stmt->close();
    } elseif (!$message) {
        $message = "Deskripsi dan file/link wajib diisi!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Materi</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .form-container textarea { width:100%; min-height:80px; margin-bottom:14px; }
        .form-container input[type="text"], .form-container input[type="url"] { width:100%; margin-bottom:14px; }
        .custom-file-label {
            display: inline-block;
            background: #ede7f6;
            color: #333;
            border: 1px solid #bdbdbd;
            border-radius: 6px;
            padding: 8px 16px;
            margin-bottom: 12px;
            cursor: pointer;
            font-size: 0.97rem;
        }
        .custom-file-label.selected { background: #d1c4e9; color: #222; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Tambah Materi</div>
        <div class="navbar-link"><a href="dashboard.php">Kembali ke Dashboard</a></div>
    </nav>
    <div class="page-container">
        <div class="form-container">
            <h1>Tambah Materi Baru</h1>
            <?php if ($message) echo "<p style='color:green;'>$message</p>"; ?>
            <form method="POST" action="" enctype="multipart/form-data" id="materiForm">
                <textarea name="deskripsi" placeholder="Deskripsi materi..." required></textarea>
                <label class="custom-file-label">
                    <input type="file" name="file" style="display:none;" onchange="updateFileLabel(this)">
                    <span>Upload File Materi</span>
                </label>
                <input type="url" name="link" placeholder="Atau masukkan link materi (opsional)">
                <button type="submit">Upload Materi</button>
            </form>
        </div>
    </div>
    <script>
        function updateFileLabel(input) {
            const label = input.parentElement;
            const span = label.querySelector('span');
            if (input.files && input.files[0]) {
                span.textContent = input.files[0].name;
                label.classList.add('selected');
            } else {
                span.textContent = 'Upload File Materi';
                label.classList.remove('selected');
            }
        }
    </script>
</body>
</html>
