<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}
include '../koneksi.php';

$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = trim($_POST['deskripsi']);
    $link = trim($_POST['link']);
    
    // Handle file upload
    $file_name = null;
    if (isset($_FILES['file']) && $_FILES['file']['name']) {
        $allowed = ['pdf','doc','docx','ppt','pptx','txt'];
        $ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = '../uploads/materi/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = uniqid('materi_').'.'.$ext;
            move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir.$file_name);
        }
    }
    
    // Handle image upload
    $image_name = null;
    if (isset($_FILES['image']) && $_FILES['image']['name']) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $upload_dir = '../uploads/materi/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $image_name = uniqid('img_').'.'.$ext;
            move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir.$image_name);
        }
    }
    
    if ($deskripsi) {
        $sql = "INSERT INTO materi (deskripsi, file, image, link) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $deskripsi, $file_name, $image_name, $link);
        if ($stmt->execute()) {
            $message = "Materi berhasil ditambahkan!";
        } else {
            $message = "Gagal menambahkan materi.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Materi</title>
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
            max-width: 800px;
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
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
        }
        
        .form-group textarea,
        .form-group input {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group textarea:focus,
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .form-group textarea {
            height: 120px;
            resize: vertical;
        }
        
        .file-upload {
            position: relative;
            display: inline-block;
            cursor: pointer;
            background: #f8f9fa;
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            width: 100%;
        }
        
        .file-upload:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        
        .file-upload input {
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
            width: 100%;
            margin-top: 1rem;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .message {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            border: 1px solid #c3e6cb;
            text-align: center;
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
        <div class="navbar-title">📚 Tambah Materi</div>
        <div class="navbar-link"><a href="dashboard.php">← Kembali ke Dashboard</a></div>
    </nav>
    
    <div class="page-container">
        <div class="form-container">
            <h1>📚 Tambah Materi Baru</h1>
            
            <?php if ($message): ?>
                <div class="message"><?= $message ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Deskripsi Materi:</label>
                    <textarea name="deskripsi" placeholder="Masukkan deskripsi materi..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label>Upload File (PDF, DOC, PPT):</label>
                    <div class="file-upload">
                        <input type="file" name="file" accept=".pdf,.doc,.docx,.ppt,.pptx,.txt" onchange="previewFile(this, 'file-preview')">
                        <span id="file-preview">📄 Klik untuk upload file</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Upload Gambar:</label>
                    <div class="file-upload">
                        <input type="file" name="image" accept="image/*" onchange="previewFile(this, 'image-preview')">
                        <span id="image-preview">🖼️ Klik untuk upload gambar</span>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Link Eksternal (Opsional):</label>
                    <input type="url" name="link" placeholder="https://example.com">
                </div>
                
                <button type="submit" class="btn">🚀 Tambah Materi</button>
            </form>
        </div>
    </div>

    <script>
        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            
            if (input.files && input.files[0]) {
                const file = input.files[0];
                const fileName = file.name;
                
                if (input.accept.includes('image')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" style="max-width: 200px; max-height: 100px; border-radius: 5px;">`;
                    };
                    reader.readAsDataURL(file);
                } else {
                    preview.innerHTML = `📄 ${fileName}`;
                }
            } else {
                if (input.accept.includes('image')) {
                    preview.innerHTML = '🖼️ Klik untuk upload gambar';
                } else {
                    preview.innerHTML = '📄 Klik untuk upload file';
                }
            }
        }
    </script>
</body>
</html>
