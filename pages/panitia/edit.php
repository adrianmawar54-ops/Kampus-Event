<?php
require_once '../../config/database.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = clean_input($_GET['id']);

// Ambil data panitia
$query = "SELECT * FROM panitia WHERE id_panitia = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Ambil data kegiatan untuk dropdown
$query_kegiatan = "SELECT * FROM kegiatan ORDER BY nama_kegiatan ASC";
$kegiatan_result = mysqli_query($conn, $query_kegiatan);

// Handle Form Submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_kegiatan = clean_input($_POST['id_kegiatan']);
    $nama_panitia = clean_input($_POST['nama_panitia']);
    $nim = clean_input($_POST['nim']);
    $jabatan = clean_input($_POST['jabatan']);
    $no_hp = clean_input($_POST['no_hp']);
    $email = clean_input($_POST['email']);
    
    // Validasi
    if (empty($id_kegiatan) || empty($nama_panitia) || empty($jabatan)) {
        $error = "Kegiatan, nama panitia, dan jabatan wajib diisi!";
    } else {
        $query = "UPDATE panitia SET 
                  id_kegiatan = '$id_kegiatan',
                  nama_panitia = '$nama_panitia',
                  nim = '$nim',
                  jabatan = '$jabatan',
                  no_hp = '$no_hp',
                  email = '$email'
                  WHERE id_panitia = '$id'";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal mengupdate panitia: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Panitia - Sistem Kegiatan Kampus</title>
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
                        <h5 class="mb-0"><i class="bi bi-pencil"></i> Edit Panitia</h5>
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
                                        <option value="<?= $kegiatan['id_kegiatan'] ?>"
                                                <?= ($kegiatan['id_kegiatan'] == $data['id_kegiatan']) ? 'selected' : '' ?>>
                                            <?= $kegiatan['nama_kegiatan'] ?> 
                                            (<?= date('d M Y', strtotime($kegiatan['tanggal_mulai'])) ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label">Nama Panitia <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_panitia" class="form-control" 
                                           value="<?= $data['nama_panitia'] ?>" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NIM</label>
                                    <input type="text" name="nim" class="form-control" 
                                           value="<?= $data['nim'] ?>">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Ketua" <?= ($data['jabatan'] == 'Ketua') ? 'selected' : '' ?>>Ketua</option>
                                    <option value="Wakil Ketua" <?= ($data['jabatan'] == 'Wakil Ketua') ? 'selected' : '' ?>>Wakil Ketua</option>
                                    <option value="Sekretaris" <?= ($data['jabatan'] == 'Sekretaris') ? 'selected' : '' ?>>Sekretaris</option>
                                    <option value="Bendahara" <?= ($data['jabatan'] == 'Bendahara') ? 'selected' : '' ?>>Bendahara</option>
                                    <option value="Koordinator Acara" <?= ($data['jabatan'] == 'Koordinator Acara') ? 'selected' : '' ?>>Koordinator Acara</option>
                                    <option value="Koordinator Konsumsi" <?= ($data['jabatan'] == 'Koordinator Konsumsi') ? 'selected' : '' ?>>Koordinator Konsumsi</option>
                                    <option value="Koordinator Dokumentasi" <?= ($data['jabatan'] == 'Koordinator Dokumentasi') ? 'selected' : '' ?>>Koordinator Dokumentasi</option>
                                    <option value="Koordinator Publikasi" <?= ($data['jabatan'] == 'Koordinator Publikasi') ? 'selected' : '' ?>>Koordinator Publikasi</option>
                                    <option value="Anggota" <?= ($data['jabatan'] == 'Anggota') ? 'selected' : '' ?>>Anggota</option>
                                    <?php
                                    // Add current jabatan if not in list
                                    $default_jabatan = ['Ketua', 'Wakil Ketua', 'Sekretaris', 'Bendahara', 'Koordinator Acara', 'Koordinator Konsumsi', 'Koordinator Dokumentasi', 'Koordinator Publikasi', 'Anggota'];
                                    if (!in_array($data['jabatan'], $default_jabatan)):
                                    ?>
                                        <option value="<?= $data['jabatan'] ?>" selected><?= $data['jabatan'] ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" 
                                           value="<?= $data['email'] ?>">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">No. HP</label>
                                    <input type="text" name="no_hp" class="form-control" 
                                           value="<?= $data['no_hp'] ?>">
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