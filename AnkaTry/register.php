<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Step form simple hide/show */
        .step-form { display: none; }
        .step-form.active { display: block; }
        .step-buttons {
            display: flex;
            justify-content: space-between;
            gap: 24px;
            margin-top: 10px;
        }
        .btn-kecil {
            width: 48% !important;
            padding: 8px 0 !important;
            font-size: 0.97rem !important;
            border-radius: 6px !important;
        }
        /* Samakan ukuran SEMUA input di form */
        .form-container input[type="text"],
        .form-container input[type="email"],
        .form-container input[type="password"],
        .form-container input[type="date"],
        .form-container input[type="tel"] {
            width: 100%;
            padding: 12px;
            margin: 12px 0 18px 0;
            border: 1px solid #bdbdbd;
            border-radius: 8px;
            font-size: 1rem;
            background: #f3e5f5;
            transition: border-color 0.2s, box-shadow 0.2s;
            box-sizing: border-box;
            appearance: none;
            -webkit-appearance: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Tyty</div>
        <div class="navbar-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </nav>
    <div class="page-container">
        <div class="side-content">
            <h2>Gabung Bersama Kami!</h2>
            <p>
                Daftarkan dirimu untuk mendapatkan akses ke fitur-fitur menarik, update terbaru, dan pengalaman terbaik bersama komunitas kami.<br><br>
                Proses pendaftaran mudah dan cepat!
            </p>
        </div>
        <div class="form-container">
            <h1>Register</h1>
            <form action="register_process.php" method="POST" id="registerForm" autocomplete="off">
                <!-- Step 1 -->
                <div class="step-form step1 active">
                    <input type="text" name="username" placeholder="Username" required>
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <div class="step-buttons">
                        <span></span>
                        <button type="button" class="btn-kecil" onclick="nextStep()">Next</button>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="step-form step2">
                    <input type="text" name="alamat" placeholder="Alamat" required>
                    <input type="date" name="tanggal_lahir" placeholder="Tanggal Lahir" required>
                    <input type="tel" name="no_telp" placeholder="No. Telepon" pattern="[0-9]{10,15}" required>
                    <input type="text" name="asal_sekolah" placeholder="Asal Sekolah" required>
                    <div class="step-buttons">
                        <button type="button" class="btn-kecil" onclick="prevStep()">Back</button>
                        <button type="submit" class="btn-kecil">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Simple JS stepper
        function nextStep() {
            document.querySelector('.step1').classList.remove('active');
            document.querySelector('.step2').classList.add('active');
        }
        function prevStep() {
            document.querySelector('.step2').classList.remove('active');
            document.querySelector('.step1').classList.add('active');
        }
    </script>
</body>
</html>
