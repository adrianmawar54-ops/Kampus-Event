<?php
require_once '../../config/database.php';

// Cek Login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$id_admin = $_SESSION['user_id'];
$success = "";
$error = "";

// Handle Update Profil
if (isset($_POST['update_profil'])) {
    $nama = clean_input($_POST['nama']);
    $username = clean_input($_POST['username']);

    // Upload Foto
    $foto = $_SESSION['foto_profil'];
    if ($_FILES['foto']['name']) {
        $target_dir = "../../assets/img/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = pathinfo($_FILES["foto"]["name"], PATHINFO_EXTENSION);
        $new_filename = "profile_" . time() . "." . $file_extension;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
            $foto = $new_filename;
            $_SESSION['foto_profil'] = $foto; // Update session
        } else {
            $error = "Gagal mengupload foto.";
        }
    }

    if (empty($error)) {
        $query = "UPDATE admin_settings SET nama_admin = '$nama', username = '$username', foto_profil = '$foto' WHERE id = '$id_admin'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['nama_admin'] = $nama; // Update session
            $success = "Profil berhasil diperbarui!";
        } else {
            $error = "Gagal update profil: " . mysqli_error($conn);
        }
    }
}

// Handle Ganti Password
if (isset($_POST['ganti_password'])) {
    $pass_lama = $_POST['pass_lama'];
    $pass_baru = $_POST['pass_baru'];
    $konfirmasi = $_POST['konfirmasi_pass'];

    // Ambil password saat ini
    $q = mysqli_query($conn, "SELECT password FROM admin_settings WHERE id = '$id_admin'");
    $r = mysqli_fetch_assoc($q);

    if (password_verify($pass_lama, $r['password'])) {
        if ($pass_baru === $konfirmasi) {
            $pass_hash = password_hash($pass_baru, PASSWORD_DEFAULT);
            $query = "UPDATE admin_settings SET password = '$pass_hash' WHERE id = '$id_admin'";
            if (mysqli_query($conn, $query)) {
                $success = "Password berhasil diubah!";
            } else {
                $error = "Gagal update password.";
            }
        } else {
            $error = "Konfirmasi password baru tidak cocok.";
        }
    } else {
        $error = "Password lama salah.";
    }
}

// Ambil data terbaru
$query = "SELECT * FROM admin_settings WHERE id = '$id_admin'";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .profile-pic-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <h2><i class="bi bi-gear-fill"></i> Pengaturan Akun</h2>
        <hr>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i>
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-circle"></i>
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Form Profil -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-person"></i> Edit Profil</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="text-center mb-4">
                                <?php
                                $img_src = "../../assets/img/" . ($data['foto_profil'] ? $data['foto_profil'] : 'default.png');
                                ?>
                                <img src="<?= $img_src ?>" alt="Profil" class="profile-pic-preview mb-3">
                                <div class="small text-muted">Format: JPG, PNG (Max 2MB)</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control" value="<?= $data['nama_admin'] ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" value="<?= $data['username'] ?>"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Ganti Foto Profil</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" name="update_profil" class="btn btn-primary w-100">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Form Ganti Password -->
            <div class="col-md-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="bi bi-lock"></i> Ganti Password</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Password Lama</label>
                                <input type="password" name="pass_lama" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="pass_baru" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="konfirmasi_pass" class="form-control" required>
                            </div>

                            <button type="submit" name="ganti_password" class="btn btn-danger w-100">
                                <i class="bi bi-shield-lock"></i> Update Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>