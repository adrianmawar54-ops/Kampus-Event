<?php
require_once '../../config/database.php';

// Ambil ID dari URL
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = clean_input($_GET['id']);

// Ambil data kegiatan dengan lokasi
$query = "SELECT k.*, l.nama_lokasi, l.kapasitas, l.fasilitas 
          FROM kegiatan k 
          LEFT JOIN lokasi l ON k.id_lokasi = l.id_lokasi 
          WHERE k.id_kegiatan = '$id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$data = mysqli_fetch_assoc($result);

// Hitung jumlah peserta
$query_peserta = "SELECT COUNT(*) as total FROM peserta WHERE id_kegiatan = '$id'";
$total_peserta = mysqli_fetch_assoc(mysqli_query($conn, $query_peserta))['total'];

// Hitung jumlah panitia
$query_panitia = "SELECT COUNT(*) as total FROM panitia WHERE id_kegiatan = '$id'";
$total_panitia = mysqli_fetch_assoc(mysqli_query($conn, $query_panitia))['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Kegiatan - <?= $data['nama_kegiatan'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <div class="row">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="bi bi-info-circle"></i> Detail Kegiatan</h5>
                    </div>
                    <div class="card-body">
                        <h3 class="mb-3"><?= $data['nama_kegiatan'] ?></h3>
                        
                        <div class="mb-3">
                            <h6 class="text-muted">Deskripsi:</h6>
                            <p><?= nl2br($data['deskripsi']) ?: '<em class="text-muted">Tidak ada deskripsi</em>' ?></p>
                        </div>

                        <hr>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h6 class="text-muted"><i class="bi bi-calendar-event"></i> Tanggal Mulai:</h6>
                                <p><?= date('l, d F Y', strtotime($data['tanggal_mulai'])) ?><br>
                                   <strong><?= date('H:i', strtotime($data['tanggal_mulai'])) ?> WIB</strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted"><i class="bi bi-calendar-check"></i> Tanggal Selesai:</h6>
                                <p><?= date('l, d F Y', strtotime($data['tanggal_selesai'])) ?><br>
                                   <strong><?= date('H:i', strtotime($data['tanggal_selesai'])) ?> WIB</strong></p>
                            </div>
                        </div>

                        <hr>

                        <div class="mb-3">
                            <h6 class="text-muted"><i class="bi bi-geo-alt"></i> Lokasi:</h6>
                            <?php if ($data['nama_lokasi']): ?>
                                <p><strong><?= $data['nama_lokasi'] ?></strong><br>
                                   <small class="text-muted">
                                       Kapasitas: <?= $data['kapasitas'] ?> orang<br>
                                       Fasilitas: <?= $data['fasilitas'] ?>
                                   </small></p>
                            <?php else: ?>
                                <p class="text-muted">Lokasi belum ditentukan</p>
                            <?php endif; ?>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted">Status:</h6>
                                <?php
                                $badge_class = '';
                                switch($data['status']) {
                                    case 'Pendaftaran': $badge_class = 'bg-success'; break;
                                    case 'Berlangsung': $badge_class = 'bg-warning'; break;
                                    case 'Selesai': $badge_class = 'bg-secondary'; break;
                                }
                                ?>
                                <span class="badge <?= $badge_class ?> fs-6"><?= $data['status'] ?></span>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Kuota Peserta:</h6>
                                <p><strong><?= $data['kuota_peserta'] ?></strong> orang</p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Dibuat:</h6>
                                <p><?= date('d M Y', strtotime($data['created_at'])) ?></p>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex gap-2">
                            <a href="edit.php?id=<?= $data['id_kegiatan'] ?>" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Stats -->
            <div class="col-md-4">
                <div class="card shadow mb-3">
                    <div class="card-body text-center">
                        <i class="bi bi-people text-info" style="font-size: 3rem;"></i>
                        <h2 class="mt-2"><?= $total_peserta ?></h2>
                        <p class="text-muted mb-0">Total Peserta Terdaftar</p>
                        <a href="../peserta/index.php?kegiatan=<?= $id ?>" class="btn btn-sm btn-info mt-2">
                            Lihat Peserta
                        </a>
                    </div>
                </div>

                <div class="card shadow">
                    <div class="card-body text-center">
                        <i class="bi bi-person-badge text-warning" style="font-size: 3rem;"></i>
                        <h2 class="mt-2"><?= $total_panitia ?></h2>
                        <p class="text-muted mb-0">Total Panitia</p>
                        <a href="../panitia/index.php?kegiatan=<?= $id ?>" class="btn btn-sm btn-warning mt-2">
                            Lihat Panitia
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>