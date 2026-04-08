<?php
require_once '../../config/database.php';

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
        $query = "INSERT INTO panitia 
                  (id_kegiatan, nama_panitia, nim, jabatan, no_hp, email) 
                  VALUES ('$id_kegiatan', '$nama_panitia', '$nim', '$jabatan', '$no_hp', '$email')";
        
        if (mysqli_query($conn, $query)) {
            header("Location: index.php");
            exit();
        } else {
            $error = "Gagal menambah panitia: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Panitia - Sistem Kegiatan Kampus</title>
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
                        <h5 class="mb-0"><i class="bi bi-plus-circle"></i> Tambah Panitia Baru</h5>
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
                                    <label class="form-label">Nama Panitia <span class="text-danger">*</span></label>
                                    <input type="text" name="nama_panitia" class="form-control" 
                                           placeholder="Contoh: Jane Doe" required>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">NIM</label>
                                    <input type="text" name="nim" class="form-control" 
                                           placeholder="123456789">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                                <select name="jabatan" class="form-select" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <option value="Ketua">Ketua</option>
                                    <option value="Wakil Ketua">Wakil Ketua</option>
                                    <option value="Sekretaris">Sekretaris</option>
                                    <option value="Bendahara">Bendahara</option>
                                    <option value="Koordinator Acara">Koordinator Acara</option>
                                    <option value="Koordinator Konsumsi">Koordinator Konsumsi</option>
                                    <option value="Koordinator Dokumentasi">Koordinator Dokumentasi</option>
                                    <option value="Koordinator Publikasi">Koordinator Publikasi</option>
                                    <option value="Anggota">Anggota</option>
                                </select>
                                <small class="text-muted">Atau ketik jabatan sendiri:</small>
                                <input type="text" id="jabatan_custom" class="form-control mt-1" 
                                       placeholder="Ketik jabatan lain...">
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

                            <div class="d-flex gap-2 mt-3">
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
    <script>
        // Custom jabatan input
        document.getElementById('jabatan_custom').addEventListener('input', function() {
            if (this.value) {
                document.querySelector('select[name="jabatan"]').value = '';
                // Create new option if doesn't exist
                let select = document.querySelector('select[name="jabatan"]');
                let existingOption = Array.from(select.options).find(opt => opt.value === this.value);
                if (!existingOption) {
                    let newOption = document.createElement('option');
                    newOption.value = this.value;
                    newOption.text = this.value;
                    newOption.selected = true;
                    select.add(newOption);
                } else {
                    existingOption.selected = true;
                }
            }
        });
    </script>
</body>
</html>