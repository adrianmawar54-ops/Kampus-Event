<?php
require_once '../../config/database.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = clean_input($_GET['id']);

// Ambil data kegiatan
$query = "SELECT * FROM kegiatan WHERE id_kegiatan = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Ambil data lokasi untuk dropdown
$query_lokasi = "SELECT * FROM lokasi ORDER BY nama_lokasi ASC";
$lokasi_result = mysqli_query($conn, $query_lokasi);

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_kegiatan = clean_input($_POST['nama_kegiatan']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $tanggal_mulai = clean_input($_POST['tanggal_mulai']);
    $tanggal_selesai = clean_input($_POST['tanggal_selesai']);
    $id_lokasi = clean_input($_POST['id_lokasi']);
    $kuota_peserta = clean_input($_POST['kuota_peserta']);
    $status = clean_input($_POST['status']);
    
    // Validasi
    if (empty($nama_kegiatan) || empty($tanggal_mulai) || empty($tanggal_selesai)) {
        $error = "Nama kegiatan, tanggal mulai, dan tanggal selesai wajib diisi!";
    } else {
        $query = "UPDATE kegiatan SET 
                  nama_kegiatan = '$nama_kegiatan',
                  deskripsi = '$deskripsi',
                  tanggal_mulai = '$tanggal_mulai',
                  tanggal_selesai = '$tanggal_selesai',
                  id_lokasi = '$id_lokasi',
                  kuota_peserta = '$kuota_peserta',
                  status = '$status'
                  WHERE id_kegiatan = '$id'";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal mengupdate kegiatan: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kegiatan - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Nama Kegiatan <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_kegiatan" class="form-control" 
                                           value="<?= $data['nama_kegiatan'] ?>" required>
                                </div>

                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="deskripsi" class="form-control" rows="3"><?= $data['deskripsi'] ?></textarea>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_mulai" class="form-control" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_mulai'])) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="tanggal_selesai" class="form-control" 
                                           value="<?= date('Y-m-d\TH:i', strtotime($data['tanggal_selesai'])) ?>" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi</label>
                                    <select name="id_lokasi" class="form-select">
                                        <option value="">-- Pilih Lokasi --</option>
                                        <?php while ($lokasi = mysqli_fetch_assoc($lokasi_result)): ?>
                                            <option value="<?= $lokasi['id_lokasi'] ?>" 
                                                    <?= ($lokasi['id_lokasi'] == $data['id_lokasi']) ? 'selected' : '' ?>>
                                                <?= $lokasi['nama_lokasi'] ?> (Kapasitas: <?= $lokasi['kapasitas'] ?>)
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Kuota Peserta</label>
                                    <input type="number" name="kuota_peserta" class="form-control" 
                                           value="<?= $data['kuota_peserta'] ?>" min="1">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="Pendaftaran" <?= ($data['status'] == 'Pendaftaran') ? 'selected' : '' ?>>Pendaftaran</option>
                                        <option value="Berlangsung" <?= ($data['status'] == 'Berlangsung') ? 'selected' : '' ?>>Berlangsung</option>
                                        <option value="Selesai" <?= ($data['status'] == 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex gap-2 mt-3">
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