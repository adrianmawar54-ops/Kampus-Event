<?php
require_once '../../config/database.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $query = "DELETE FROM kegiatan WHERE id_kegiatan = '$id'";
    if (mysqli_query($conn, $query)) {
        $success = "Kegiatan berhasil dihapus!";
    } else {
        $error = "Gagal menghapus kegiatan: " . mysqli_error($conn);
    }
}

// Ambil semua data kegiatan dengan JOIN ke lokasi
$query = "SELECT k.*, l.nama_lokasi 
          FROM kegiatan k 
          LEFT JOIN lokasi l ON k.id_lokasi = l.id_lokasi 
          ORDER BY k.tanggal_mulai DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kegiatan - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar-check"></i> Kelola Kegiatan</h2>
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Kegiatan
            </a>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle"></i> <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle"></i> <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Tabel Kegiatan -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Kegiatan</th>
                                    <th width="15%">Lokasi</th>
                                    <th width="15%">Tanggal Mulai</th>
                                    <th width="15%">Tanggal Selesai</th>
                                    <th width="10%">Status</th>
                                    <th width="10%">Kuota</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                                ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= $row['nama_kegiatan'] ?></strong><br>
                                        <small class="text-muted"><?= substr($row['deskripsi'], 0, 50) ?>...</small>
                                    </td>
                                    <td>
                                        <i class="bi bi-geo-alt text-primary"></i> 
                                        <?= $row['nama_lokasi'] ?? '-' ?>
                                    </td>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_mulai'])) ?></td>
                                    <td><?= date('d M Y, H:i', strtotime($row['tanggal_selesai'])) ?></td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        switch($row['status']) {
                                            case 'Pendaftaran': $badge_class = 'bg-success'; break;
                                            case 'Berlangsung': $badge_class = 'bg-warning'; break;
                                            case 'Selesai': $badge_class = 'bg-secondary'; break;
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>"><?= $row['status'] ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= $row['kuota_peserta'] ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="detail.php?id=<?= $row['id_kegiatan'] ?>" 
                                               class="btn btn-info" title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="edit.php?id=<?= $row['id_kegiatan'] ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="index.php?delete=<?= $row['id_kegiatan'] ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus kegiatan ini? Data peserta dan panitia juga akan terhapus!')"
                                               title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Belum ada data kegiatan. 
                        <a href="tambah.php">Tambah kegiatan baru</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>