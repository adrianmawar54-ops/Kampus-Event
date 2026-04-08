<?php
require_once '../../config/database.php';

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lokasi = clean_input($_POST['nama_lokasi']);
    $kapasitas = clean_input($_POST['kapasitas']);
    $fasilitas = clean_input($_POST['fasilitas']);
    
    // Validasi
    if (empty($nama_lokasi) || empty($kapasitas)) {
        $error = "Nama lokasi dan kapasitas wajib diisi!";
    } else {
        $query = "INSERT INTO lokasi (nama_lokasi, kapasitas, fasilitas) 
                  VALUES ('$nama_lokasi', '$kapasitas', '$fasilitas')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal menambah lokasi: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lokasi - Sistem Kegiatan Kampus</title>
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
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Lokasi Baru</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="nama_lokasi" class="form-control" 
                                       placeholder="Contoh: Aula Utama" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                <input type="number" name="kapasitas" class="form-control" 
                                       placeholder="Contoh: 500" min="1" required>
                                <small class="text-muted">Jumlah orang yang dapat ditampung</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fasilitas</label>
                                <textarea name="fasilitas" class="form-control" rows="3" 
                                          placeholder="Contoh: Proyektor, Sound System, AC"></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Simpan
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