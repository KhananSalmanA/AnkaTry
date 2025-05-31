<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - AnkaTry</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
            font-size: 1.8rem;
            font-weight: bold;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .navbar-link {
            color: #666;
        }
        
        .navbar-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .navbar-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }
        
        .page-container {
            display: flex;
            flex: 1;
            padding: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            width: 100%;
        }
        
        @media (max-width: 768px) {
            .page-container {
                flex-direction: column;
                padding: 1rem;
            }
        }
        
        .side-content {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: white;
        }
        
        .side-content h2 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
            text-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .side-content p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
        }
        
        .form-container {
            flex: 1;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            display: flex;
            flex-direction: column;
            justify-content: center;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-container h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .form-container form {
            display: flex;
            flex-direction: column;
        }
        
        .step-form {
            display: none;
            animation: fadeIn 0.5s ease-in-out;
        }
        
        .step-form.active {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
            font-size: 1.2rem;
        }
        
        .form-container input {
            width: 100%;
            padding: 1rem 1rem 1rem 3rem;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
        }
        
        .form-container input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .step-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 1rem;
        }
        
        .btn-kecil {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .btn-kecil:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }
        
        .progress-container {
            margin-bottom: 2rem;
        }
        
        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }
        
        .step-label {
            font-size: 0.9rem;
            color: #666;
        }
        
        .step-label.active {
            color: #667eea;
            font-weight: 600;
        }
        
        .progress-bar {
            width: 100%;
            height: 8px;
            background: #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 4px;
            transition: width 0.3s ease;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
            text-align: center;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">AnkaTry</div>
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
            
            <?php if(isset($_GET['error'])): ?>
                <div class="error-message">
                    <?php 
                    $error = $_GET['error'];
                    if($error == 'exists') echo "Username atau email sudah terdaftar!";
                    elseif($error == 'empty') echo "Silakan isi semua field!";
                    else echo "Terjadi kesalahan. Silakan coba lagi.";
                    ?>
                </div>
            <?php endif; ?>
            
            <div class="progress-container">
                <div class="progress-steps">
                    <span class="step-label active" id="step1-label">Informasi Akun</span>
                    <span class="step-label" id="step2-label">Data Pribadi</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progress-fill" style="width: 50%;"></div>
                </div>
            </div>
            
            <form action="register_process.php" method="POST" id="registerForm" autocomplete="off">
                <!-- Step 1 -->
                <div class="step-form step1 active">
                    <div class="input-group">
                        <div class="input-icon">👤</div>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">✉️</div>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">🔒</div>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="step-buttons">
                        <span></span>
                        <button type="button" class="btn-kecil" onclick="nextStep()">Selanjutnya</button>
                    </div>
                </div>
                <!-- Step 2 -->
                <div class="step-form step2">
                    <div class="input-group">
                        <div class="input-icon">📍</div>
                        <input type="text" name="alamat" placeholder="Alamat" required>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">📅</div>
                        <input type="date" name="tanggal_lahir" placeholder="Tanggal Lahir" required>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">📱</div>
                        <input type="tel" name="no_telp" placeholder="No. Telepon" pattern="[0-9]{10,15}" required>
                    </div>
                    <div class="input-group">
                        <div class="input-icon">🏫</div>
                        <input type="text" name="asal_sekolah" placeholder="Asal Sekolah" required>
                    </div>
                    <div class="step-buttons">
                        <button type="button" class="btn-kecil" onclick="prevStep()">Kembali</button>
                        <button type="submit" class="btn-kecil">Register</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Form stepper
        function nextStep() {
            document.querySelector('.step1').classList.remove('active');
            document.querySelector('.step2').classList.add('active');
            document.getElementById('step1-label').classList.remove('active');
            document.getElementById('step2-label').classList.add('active');
            document.getElementById('progress-fill').style.width = '100%';
            
            // Smooth scroll to top of form if needed
            document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
        }
        
        function prevStep() {
            document.querySelector('.step2').classList.remove('active');
            document.querySelector('.step1').classList.add('active');
            document.getElementById('step2-label').classList.remove('active');
            document.getElementById('step1-label').classList.add('active');
            document.getElementById('progress-fill').style.width = '50%';
        }
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.querySelector('input[name="password"]').value;
            if (password.length < 6) {
                e.preventDefault();
                alert('Password harus minimal 6 karakter!');
                return;
            }
        });
        
        // Animasi untuk form elements
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.step1 input');
            inputs.forEach((input, index) => {
                input.style.opacity = '0';
                input.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    input.style.transition = 'all 0.5s ease';
                    input.style.opacity = '1';
                    input.style.transform = 'translateY(0)';
                }, index * 100 + 300);
            });
            
            const button = document.querySelector('.step1 .btn-kecil');
            button.style.opacity = '0';
            button.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                button.style.transition = 'all 0.5s ease';
                button.style.opacity = '1';
                button.style.transform = 'translateY(0)';
            }, inputs.length * 100 + 400);
        });
    </script>
</body>
</html>
