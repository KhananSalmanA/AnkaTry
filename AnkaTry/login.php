<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="navbar-title">Tyty</div>
        <div class="navbar-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </nav>
    <div class="page-container">
        <div class="side-content">
            <h2>Selamat Datang Kembali!</h2>
            <p>
                Masuk ke akun Anda untuk melanjutkan aktivitas, mengakses fitur eksklusif, dan tetap terhubung dengan komunitas kami.<br><br>
                Jangan lupa, keamanan akun Anda adalah prioritas kami.
            </p>
        </div>
        <div class="form-container">
            <h1>Login</h1>
            <form action="login_process.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
