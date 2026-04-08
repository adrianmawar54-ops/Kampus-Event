<?php
require_once 'config/database.php';

// Auth Check (handled in navbar inclusion basically, but good practice)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$role = $_SESSION['role'] ?? 'admin';

// Query Counts only for Admin
$count_kegiatan = 0;
$count_peserta = 0;
$count_panitia = 0;
$count_lokasi = 0;

if ($role == 'admin') {
    $c_kegiatan = mysqli_query($conn, "SELECT COUNT(*) as total FROM kegiatan");
    $count_kegiatan = mysqli_fetch_assoc($c_kegiatan)['total'];

    $c_peserta = mysqli_query($conn, "SELECT COUNT(*) as total FROM peserta");
    $count_peserta = mysqli_fetch_assoc($c_peserta)['total'];

    $c_panitia = mysqli_query($conn, "SELECT COUNT(*) as total FROM panitia");
    $count_panitia = mysqli_fetch_assoc($c_panitia)['total'];

    $c_lokasi = mysqli_query($conn, "SELECT COUNT(*) as total FROM lokasi");
    $count_lokasi = mysqli_fetch_assoc($c_lokasi)['total'];
} else {
    // Queries for Peserta (My Activities)
    $nim = $_SESSION['nim'];
    // Ambil kegiatan yang diikuti peserta ini
    // Karena logic password kita pakai 1 row mewakili user, tapi user bisa punya banyak row peserta (per event).
    // Maka kita SELECT WHERE nim = $nim
    $my_activities_q = "SELECT p.*, k.nama_kegiatan, k.tanggal_mulai as tanggal, l.nama_lokasi 
                        FROM peserta p
                        JOIN kegiatan k ON p.id_kegiatan = k.id_kegiatan
                        JOIN lokasi l ON k.id_lokasi = l.id_lokasi
                        WHERE p.nim = '$nim'
                        ORDER BY k.tanggal_mulai DESC";
    $my_activities = mysqli_query($conn, $my_activities_q);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Kegiatan Kampus</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .hero-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            border-radius: 15px;
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        .hero-bg::after {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            background: url('https://source.unsplash.com/random/1200x400/?campus,event') center/cover;
            opacity: 0.1;
            z-index: 0;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .card-counter {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: 0.3s;
        }

        .card-counter:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }

        .icon-box {
            font-size: 3rem;
            opacity: 0.2;
            position: absolute;
            right: 20px;
            bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>

    <div class="container">
        <!-- Welcoming Hero -->
        <div class="hero-bg">
            <div class="hero-content">
                <h1>Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! 👋</h1>
                <p class="lead mb-0">Selamat datang di Panel <?= ucfirst($role) ?> Sistem Kegiatan Kampus.</p>
                <small><?= date('l, d F Y') ?></small>
            </div>
        </div>

        <?php if ($role == 'admin'): ?>
            <!-- DASHBOARD ADMIN -->
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="card card-counter bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Kegiatan</h5>
                            <h2 class="display-4 fw-bold"><?= $count_kegiatan ?></h2>
                            <i class="bi bi-calendar-event icon-box"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-counter bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Peserta</h5>
                            <h2 class="display-4 fw-bold"><?= $count_peserta ?></h2>
                            <i class="bi bi-people icon-box"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-counter bg-warning text-dark h-100">
                        <div class="card-body">
                            <h5 class="card-title">Panitia</h5>
                            <h2 class="display-4 fw-bold"><?= $count_panitia ?></h2>
                            <i class="bi bi-person-badge icon-box"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-counter bg-info text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Lokasi</h5>
                            <h2 class="display-4 fw-bold"><?= $count_lokasi ?></h2>
                            <i class="bi bi-geo-alt icon-box"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-5">
                <div class="col-md-12">
                    <div class="card shadow border-0">
                        <div class="card-header bg-white">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-activity"></i> Akses Cepat</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex gap-3 flex-wrap">
                                <a href="pages/kegiatan/tambah.php" class="btn btn-outline-primary"><i
                                        class="bi bi-plus"></i> Tambah Kegiatan</a>
                                <a href="pages/peserta/tambah.php" class="btn btn-outline-success"><i
                                        class="bi bi-person-plus"></i> Tambah Peserta</a>
                                <a href="pages/panitia/tambah.php" class="btn btn-outline-warning"><i
                                        class="bi bi-person-plus-fill"></i> Tambah Panitia</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <!-- DASHBOARD PESERTA -->
            <h4 class="mb-3"><i class="bi bi-journal-check"></i> Kegiatan Saya</h4>
            <div class="card shadow border-0">
                <div class="card-body p-0">
                    <?php if (mysqli_num_rows($my_activities) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama Kegiatan</th>
                                        <th>Tanggal</th>
                                        <th>Lokasi</th>
                                        <th>Status Kehadiran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($act = mysqli_fetch_assoc($my_activities)): ?>
                                        <tr>
                                            <td class="fw-bold text-primary"><?= $act['nama_kegiatan'] ?></td>
                                            <td><?= date('d M Y', strtotime($act['tanggal'])) ?></td>
                                            <td><i class="bi bi-geo-alt-fill text-danger"></i> <?= $act['nama_lokasi'] ?></td>
                                            <td>
                                                <?php
                                                $badge = 'bg-secondary';
                                                if ($act['status_kehadiran'] == 'Hadir')
                                                    $badge = 'bg-success';
                                                if ($act['status_kehadiran'] == 'Terdaftar')
                                                    $badge = 'bg-info';
                                                ?>
                                                <span class="badge <?= $badge ?>"><?= $act['status_kehadiran'] ?></span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="p-5 text-center">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486777.png" alt="Empty" width="100"
                                class="mb-3 opacity-50">
                            <h5>Belum ada kegiatan yang diikuti.</h5>
                            <p class="text-muted">Daftarkan dirimu di kegiatan kampus segera!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle-fill"></i> Ingin mendaftar kegiatan baru? Silakan hubungi panitia atau
                        pantau pengumuman mading kampus. (Fitur pendaftaran mandiri coming soon!)
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>