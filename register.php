<?php
require_once 'config/database.php';

// Redirect jika sudah login
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// Handle Register
if (isset($_POST['register'])) {
    $nama = clean_input($_POST['nama']);
    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    $konfirmasi = $_POST['konfirmasi_password'];

    // Validasi
    if ($password !== $konfirmasi) {
        $error = "Konfirmasi password tidak sesuai!";
    } else {
        // Cek username sudah ada atau belum
        $check = mysqli_query($conn, "SELECT * FROM admin_settings WHERE username = '$username'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Insert data
            $pass_hash = password_hash($password, PASSWORD_DEFAULT);
            $foto_default = 'default.png';

            // Generate ID baru (manual auto increment logic if needed for specific ID handling, or let DB handle it if AI)
            // Asumsi table created with manual ID or no AUTO_INCREMENT based on previous setup_settings.php which used INT(11) PRIMARY KEY without AUTO_INCREMENT explicitly stated in create but insert used specific ID. 
            // Let's modify to use max ID + 1 to be safe given the previous create table syntax.
            $max_q = mysqli_query($conn, "SELECT MAX(id) as max_id FROM admin_settings");
            $max_row = mysqli_fetch_assoc($max_q);
            $new_id = $max_row['max_id'] + 1;

            $query = "INSERT INTO admin_settings (id, nama_admin, username, password, foto_profil) 
                      VALUES ('$new_id', '$nama', '$username', '$pass_hash', '$foto_default')";

            if (mysqli_query($conn, $query)) {
                header("Location: login.php?registered=1");
                exit();
            } else {
                $error = "Gagal registrasi: " . mysqli_error($conn);
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/login.css">
</head>

<body>
    <div class="container d-flex justify-content-center">
        <div class="login-card">
            <div class="login-header">
                <i class="bi bi-person-plus-fill"></i>
                <h2>Daftar Akun</h2>
                <p class="text-muted">Buat akun untuk mengakses sistem</p>
            </div>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <?= $success ?>
                    <br>
                    <a href="login.php" class="alert-link">Klik disini untuk Login</a>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Nama Lengkap</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-card-text"></i></span>
                        <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                    </div>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="username" class="form-control" placeholder="Username" required>
                    </div>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                    </div>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label fw-bold">Konfirmasi Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="konfirmasi_password" class="form-control"
                            placeholder="Ulangi Password" required>
                    </div>
                </div>

                <button type="submit" name="register" class="btn-login">
                    DAFTAR <i class="bi bi-arrow-right-circle"></i>
                </button>
            </form>

            <div class="mt-4">
                <p>Sudah punya akun? <a href="login.php" class="text-decoration-none fw-bold"
                        style="color: #764ba2;">Login disini</a></p>
            </div>

            <div class="mt-2 text-muted small">
                &copy;
                <?= date('Y') ?> Sistem Kegiatan Kampus
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>