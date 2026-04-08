<?php
require_once 'config/database.php';

echo "<h3>Setup Otentikasi Peserta...</h3>";

// 1. Cek apakah kolom password sudah ada
$check_col = mysqli_query($conn, "SHOW COLUMNS FROM peserta LIKE 'password'");
if (mysqli_num_rows($check_col) == 0) {
    echo "Menambahkan kolom password ke tabel peserta...<br>";
    $alter = "ALTER TABLE peserta ADD COLUMN password VARCHAR(255) NOT NULL DEFAULT ''";
    if (mysqli_query($conn, $alter)) {
        echo "Berhasil menambahkan kolom password.<br>";

        // 2. Set default password untuk data lama (jika ada)
        // Default: nim123 (jika nim ada) atau '12345'
        echo "Mengupdate password default untuk peserta yang sudah ada...<br>";

        $get_peserta = mysqli_query($conn, "SELECT id_peserta, nim FROM peserta");
        $count = 0;
        while ($row = mysqli_fetch_assoc($get_peserta)) {
            // Password default: 12345
            $pass_hash = password_hash('12345', PASSWORD_DEFAULT);
            $id = $row['id_peserta'];
            mysqli_query($conn, "UPDATE peserta SET password = '$pass_hash' WHERE id_peserta = '$id'");
            $count++;
        }
        echo "Berhasil mengupdate $count data peserta dengan password default '12345'.<br>";

    } else {
        echo "Gagal menambahkan kolom: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Kolom password sudah ada.<br>";
}

// 3. Tambahkan 1 Peserta Dummy untuk Tester (jika tabel kosong)
$check_empty = mysqli_query($conn, "SELECT * FROM peserta LIMIT 1");
if (mysqli_num_rows($check_empty) == 0) {
    echo "Tabel peserta kosong. Menambahkan data dummy...<br>";
    $pass_hash = password_hash('12345', PASSWORD_DEFAULT);
    // Perlu id_kegiatan valid? Cek kegiatan dulu
    $keg_q = mysqli_query($conn, "SELECT id_kegiatan FROM kegiatan LIMIT 1");
    $id_kegiatan = 0;
    if (mysqli_num_rows($keg_q) > 0) {
        $k_row = mysqli_fetch_assoc($keg_q);
        $id_kegiatan = $k_row['id_kegiatan'];
    }

    // Insert
    $dummy_sql = "INSERT INTO peserta (nama_peserta, nim, jurusan, id_kegiatan, status_kehadiran, email, no_hp, password) 
                  VALUES ('Mahasiswa Demo', '101010', 'Teknik Informatika', '$id_kegiatan', 'Terdaftar', 'demo@kampus.ac.id', '08123456789', '$pass_hash')";

    if (mysqli_query($conn, $dummy_sql)) {
        echo "Data dummy berhasil ditambahkan. Login: NIM 101010, Pass 12345<br>";
    } else {
        echo "Gagal insert dummy: " . mysqli_error($conn) . "<br>";
    }
}

echo "<h4>Selesai! Tabel peserta siap untuk login.</h4>";
?>