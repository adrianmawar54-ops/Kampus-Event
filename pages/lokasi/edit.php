<?php
require_once '../../config/database.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = clean_input($_GET['id']);

// Ambil data lokasi
$query = "SELECT * FROM lokasi WHERE id_lokasi = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lokasi = clean_input($_POST['nama_lokasi']);
    $kapasitas = clean_input($_POST['kapasitas']);
    $fasilitas = clean_input($_POST['fasilitas']);
    
    // Validasi
    if (empty($nama_lokasi) || empty($kapasitas)) {
        $error = "Nama lokasi dan kapasitas wajib diisi!";
    } else {
        $query = "UPDATE lokasi SET 
                  nama_lokasi = '$nama_lokasi',
                  kapasitas = '$kapasitas',
                  fasilitas = '$fasilitas'
                  WHERE id_lokasi = '$id'";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal mengupdate lokasi: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Lokasi - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Lokasi</h5>
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
                                       value="<?= $data['nama_lokasi'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Kapasitas <span class="text-danger">*</span></label>
                                <input type="number" name="kapasitas" class="form-control" 
                                       value="<?= $data['kapasitas'] ?>" min="1" required>
                                <small class="text-muted">Jumlah orang yang dapat ditampung</small>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Fasilitas</label>
                                <textarea name="fasilitas" class="form-control" rows="3"><?= $data['fasilitas'] ?></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bi bi-save"></i> Update
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