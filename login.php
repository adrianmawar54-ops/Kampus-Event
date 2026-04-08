<?php
require_once 'config/database.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle Login
if (isset($_POST['login'])) {
    $username = clean_input($_POST['username']); // Bisa Username Admin atau NIM Peserta
    $password = $_POST['password'];
    
    // 1. Cek Login sebagai Admin
    $query_admin = "SELECT * FROM admin_settings WHERE username = '$username'";
    $result_admin = mysqli_query($conn, $query_admin);

    if (mysqli_num_rows($result_admin) === 1) {
        $row = mysqli_fetch_assoc($result_admin);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['nama'] = $row['nama_admin'];
            $_SESSION['role'] = 'admin';
            $_SESSION['foto_profil'] = $row['foto_profil'];
            
            header("Location: index.php");
            exit();
        }
    }
    
    // 2. Jika bukan Admin, Cek Login sebagai Peserta
    // Note: Satu NIM bisa punya banyak row (banyak kegiatan). Ambil satu saja untuk login.
    $query_peserta = "SELECT * FROM peserta WHERE nim = '$username' LIMIT 1";
    $result_peserta = mysqli_query($conn, $query_peserta);
    
    if (mysqli_num_rows($result_peserta) > 0) {
        $row = mysqli_fetch_assoc($result_peserta);
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id_peserta']; // ID unik baris ini
            $_SESSION['nim'] = $row['nim'];           // Identitas utama
            $_SESSION['nama'] = $row['nama_peserta'];
            $_SESSION['role'] = 'peserta';
            $_SESSION['foto_profil'] = 'default.png'; 
            
            header("Location: index.php");
            exit();
        }
    }
    
    $error = "Username/NIM atau Password salah!";
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container d-flex justify-content-center">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-person-circle"></i>
                <h2>Selamat Datang</h2>
                <p class="text-muted">Silakan masuk untuk melanjutkan</p>
            </div>

            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Registrasi berhasil! Silakan login.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Username / NIM</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Masukkan Username atau NIM" required>
                    </div>
                </div>
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    </div>
                </div>
                <button type="submit" name="login" class="btn-login">
                    MASUK <i class="bi bi-box-arrow-in-right"></i>
                </button>
            </form>

            <div class="mt-4">
                <p>Belum punya akun? <a href="register.php" class="text-decoration-none fw-bold"
                        style="color: #764ba2;">Daftar Admin</a></p>
            </div>

            <div class="mt-4 text-muted small">
                &copy; <?= date('Y') ?> Sistem Kegiatan Kampus
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>