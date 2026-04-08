<!-- Auth Check & Styles -->
<?php
// Ensure session is started (handled in database.php, but good to be safe if this file is included standalone)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Redirect to login if user_id is not set
if (!isset($_SESSION['user_id'])) {
    $login_path = defined('BASE_URL') ? BASE_URL . 'login.php' : 'login.php';
    header("Location: " . $login_path);
    exit();
}

// Get user data common
$nama_user = $_SESSION['nama'] ?? 'User';
$role = $_SESSION['role'] ?? 'admin';
$foto_profil = $_SESSION['foto_profil'] ?? 'default.png';
$display_foto = defined('BASE_URL') ? BASE_URL . 'assets/img/' . $foto_profil : 'assets/img/' . $foto_profil;

// Fallback image path logic (simplified)
?>

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- CSS Path -->
<?php
$css_path = defined('BASE_URL') ? BASE_URL . 'css/sidebar.css' : 'css/sidebar.css';
?>
<link rel="stylesheet" href="<?= $css_path ?>">

<button class="mobile-toggle" onclick="toggleSidebar()">
    <i class="bi bi-list"></i>
</button>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="user-profile">
            <div class="user-avatar">
                <?php if ($foto_profil == 'default.png' || empty($foto_profil)): ?>
                    <i class="bi bi-person"></i>
                <?php else: ?>
                    <img src="<?= $display_foto ?>" alt="Profil">
                <?php endif; ?>
            </div>
            <div class="user-name"><?= htmlspecialchars($nama_user) ?></div>
            <div class="user-role"><?= ucfirst($role) ?></div>
        </div>
    </div>
    
    <div class="sidebar-menu">
        <div class="sidebar-menu-header">Menu Utama</div>
        
        <?php if ($role == 'admin'): ?>
            <!-- MENU ADMIN -->
            <a href="<?= BASE_URL ?>index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' && !strpos($_SERVER['PHP_SELF'], 'pages') ? 'active' : '' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard Admin
            </a>
            <a href="<?= BASE_URL ?>pages/kegiatan/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'kegiatan') !== false ? 'active' : '' ?>">
                <i class="bi bi-calendar-check"></i> Data Kegiatan
            </a>
            <a href="<?= BASE_URL ?>pages/lokasi/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'lokasi') !== false ? 'active' : '' ?>">
                <i class="bi bi-geo-alt"></i> Data Lokasi
            </a>
            <a href="<?= BASE_URL ?>pages/peserta/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'peserta') !== false ? 'active' : '' ?>">
                <i class="bi bi-people"></i> Data Peserta
            </a>
            <a href="<?= BASE_URL ?>pages/panitia/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'panitia') !== false ? 'active' : '' ?>">
                <i class="bi bi-person-badge"></i> Data Panitia
            </a>
            <div class="sidebar-menu-header" style="margin-top: 10px;">Lainnya</div>
            <a href="<?= BASE_URL ?>pages/pengaturan/index.php" class="<?= strpos($_SERVER['PHP_SELF'], 'pengaturan') !== false ? 'active' : '' ?>">
                <i class="bi bi-gear"></i> Pengaturan
            </a>
            
        <?php else: ?>
            <!-- MENU PESERTA -->
            <a href="<?= BASE_URL ?>index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                 <i class="bi bi-grid-fill"></i> Dashboard Peserta
            </a>
            <a href="#" onclick="alert('Fitur Pendaftaran Baru segera hadir!')">
                 <i class="bi bi-plus-circle-dotted"></i> Daftar Kegiatan
            </a>
             <a href="#" onclick="alert('Fitur Profil Segera Hadir')">
                 <i class="bi bi-person-lines-fill"></i> Profil Saya
            </a>
        <?php endif; ?>

        <a href="<?= BASE_URL ?>logout.php" class="logout-link" onclick="return confirm('Yakin ingin keluar?')">
            <i class="bi bi-box-arrow-right"></i> Logout
        </a>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('active');
    }
    document.addEventListener("DOMContentLoaded", function() {
        var container = document.querySelector('.container');
        if (container && !container.classList.contains('main-content')) {
            container.classList.add('main-content');
            container.classList.remove('mt-4'); 
            container.style.marginTop = '0';
        }
        var hero = document.querySelector('.hero-section');
        if (hero) {
             hero.style.marginLeft = '280px';
             hero.style.width = 'calc(100% - 280px)';
        }
    });
</script>

<style>
    @media (min-width: 769px) {
        body > .container, 
        body > .container-fluid { 
            margin-left: 280px !important; 
            max-width: calc(100% - 280px) !important;
            padding: 30px !important;
        }
        .hero-section {
            margin-left: 280px !important;
            width: calc(100% - 280px) !important;
        }
    }
    @media (max-width: 768px) {
        .hero-section {
            width: 100% !important;
            margin-left: 0 !important;
        }
    }
</style>