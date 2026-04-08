<?php
require_once '../../config/database.php';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = clean_input($_GET['delete']);
    $query = "DELETE FROM panitia WHERE id_panitia = '$id'";
    if (mysqli_query($conn, $query)) {
        $success = "Panitia berhasil dihapus!";
    } else {
        $error = "Gagal menghapus panitia: " . mysqli_error($conn);
    }
}

// Filter berdasarkan kegiatan (jika ada)
$filter_kegiatan = isset($_GET['kegiatan']) ? clean_input($_GET['kegiatan']) : '';

// Ambil semua data panitia dengan JOIN ke kegiatan
$query = "SELECT p.*, k.nama_kegiatan 
          FROM panitia p 
          LEFT JOIN kegiatan k ON p.id_kegiatan = k.id_kegiatan";

if ($filter_kegiatan) {
    $query .= " WHERE p.id_kegiatan = '$filter_kegiatan'";
}

$query .= " ORDER BY k.nama_kegiatan ASC, p.jabatan ASC";
$result = mysqli_query($conn, $query);

// Ambil daftar kegiatan untuk filter
$query_kegiatan = "SELECT * FROM kegiatan ORDER BY nama_kegiatan ASC";
$kegiatan_list = mysqli_query($conn, $query_kegiatan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Panitia - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body>
    <?php include '../../navbar.php'; ?>

    <div class="container mt-4 mb-5">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-badge"></i> Kelola Panitia</h2>
            <a href="tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Panitia
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

        <!-- Filter -->
        <div class="card shadow mb-3">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-10">
                        <label class="form-label">Filter berdasarkan Kegiatan:</label>
                        <select name="kegiatan" class="form-select">
                            <option value="">-- Semua Kegiatan --</option>
                            <?php while ($k = mysqli_fetch_assoc($kegiatan_list)): ?>
                                <option value="<?= $k['id_kegiatan'] ?>" 
                                        <?= ($filter_kegiatan == $k['id_kegiatan']) ? 'selected' : '' ?>>
                                    <?= $k['nama_kegiatan'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tabel Panitia -->
        <div class="card shadow">
            <div class="card-body">
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Nama Panitia</th>
                                    <th width="10%">NIM</th>
                                    <th width="15%">Jabatan</th>
                                    <th width="25%">Kegiatan</th>
                                    <th width="15%">Kontak</th>
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
                                        <strong><?= $row['nama_panitia'] ?></strong>
                                    </td>
                                    <td><?= $row['nim'] ?: '-' ?></td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            <?= $row['jabatan'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= $row['nama_kegiatan'] ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php if ($row['email']): ?>
                                                <i class="bi bi-envelope"></i> <?= $row['email'] ?><br>
                                            <?php endif; ?>
                                            <?php if ($row['no_hp']): ?>
                                                <i class="bi bi-phone"></i> <?= $row['no_hp'] ?>
                                            <?php endif; ?>
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="edit.php?id=<?= $row['id_panitia'] ?>" 
                                               class="btn btn-warning" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="index.php?delete=<?= $row['id_panitia'] ?><?= $filter_kegiatan ? '&kegiatan='.$filter_kegiatan : '' ?>" 
                                               class="btn btn-danger"
                                               onclick="return confirm('Yakin ingin menghapus panitia ini?')"
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
                        <i class="bi bi-info-circle"></i> Belum ada data panitia. 
                        <a href="tambah.php">Tambah panitia baru</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>