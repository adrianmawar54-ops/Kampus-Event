<?php
require_once '../../config/database.php';

// Ambil data kegiatan untuk dropdown
$query_kegiatan = "SELECT * FROM kegiatan WHERE status != 'Selesai' ORDER BY nama_kegiatan ASC";
$kegiatan_result = mysqli_query($conn, $query_kegiatan);

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kegiatan = clean_input($_POST['id_kegiatan']);
    $nama_peserta = clean_input($_POST['nama_peserta']);
    $nim = clean_input($_POST['nim']);
    $email = clean_input($_POST['email']);
    $no_hp = clean_input($_POST['no_hp']);
    $jurusan = clean_input($_POST['jurusan']);
    $status_kehadiran = clean_input($_POST['status_kehadiran']);
    
    // Validasi
    if (empty($id_kegiatan) || empty($nama_peserta) || empty($nim)) {
        $error = "Kegiatan, nama peserta, dan NIM wajib diisi!";
    } else {
        // Cek apakah NIM sudah terdaftar di kegiatan yang sama
        $check_query = "SELECT * FROM peserta WHERE id_kegiatan = '$id_kegiatan' AND nim = '$nim'";
        $check_result = mysqli_query($conn, $check_query);
        
        if (mysqli_num_rows($check_result) > 0) {
            $error = "NIM ini sudah terdaftar di kegiatan yang sama!";
        } else {
            $query = "INSERT INTO peserta 
                      (id_kegiatan, nama_peserta, nim, email, no_hp, jurusan, status_kehadiran) 
                      VALUES ('$id_kegiatan', '$nama_peserta', '$nim', '$email', '$no_hp', '$jurusan', '$status_kehadiran')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: index.php");
                exit();
            } else {
                $error = "Gagal menambah peserta: " . mysqli_error($conn);
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
    <title>Tambah Peserta - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Peserta Baru</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Pilih Kegiatan <span class="text-danger">*</span></label>
                                <select name="id_kegiatan" class="form-select" required>
                                    <option value="">-- Pilih Kegiatan --</option>
                                    <?php while ($kegiatan = mysqli_fetch_assoc($kegiatan_result)): ?>
                                        <option value="<?= $kegiatan['id_kegiatan'] ?>">
                                            <?= $kegiatan['nama_kegiatan'] ?> 
                                            (<?= date('d M Y', strtotime($kegiatan['tanggal_mulai'])) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_peserta" class="form-control" 
                                           placeholder="Contoh: John Doe" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NIM <span class="text-danger">*</span></label>
                                    <input type="text" name="nim" class="form-control" 
                                           placeholder="Contoh: 123456789" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jurusan</label>
                                <input type="text" name="jurusan" class="form-control" 
                                       placeholder="Contoh: Teknik Informatika">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           placeholder="contoh@email.com">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" name="no_hp" class="form-control" 
                                           placeholder="08123456789">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Status Kehadiran</label>
                                <select name="status_kehadiran" class="form-select">
                                    <option value="Terdaftar">Terdaftar</option>
                                    <option value="Hadir">Hadir</option>
                                    <option value="Tidak Hadir">Tidak Hadir</option>
                                </select>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Daftarkan
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>