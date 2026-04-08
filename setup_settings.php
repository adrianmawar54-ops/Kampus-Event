<?php
require_once 'config/database.php';

// Buat tabel admin_settings
$query = "CREATE TABLE IF NOT EXISTS admin_settings (
    id INT(11) PRIMARY KEY,
    nama_admin VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    foto_profil VARCHAR(255) DEFAULT 'default.png'
)";

if (mysqli_query($conn, $query)) {
    echo "Tabel admin_settings berhasil dibuat/sudah ada.<br>";
} else {
    echo "Error creating table: " . mysqli_error($conn) . "<br>";
}

// Cek apakah data admin sudah ada
$check = mysqli_query($conn, "SELECT * FROM admin_settings WHERE id = 1");
if (mysqli_num_rows($check) == 0) {
    // Insert default admin
    // Password default: admin123
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $insert = "INSERT INTO admin_settings (id, nama_admin, username, password, foto_profil) 
               VALUES (1, 'Admin Kampus', 'admin', '$password', 'default.png')";

    if (mysqli_query($conn, $insert)) {
        echo "Data default admin berhasil ditambahkan.<br>";
        echo "Username: admin<br>";
        echo "Password: admin123<br>";
    } else {
        echo "Error inserting data: " . mysqli_error($conn) . "<br>";
    }
} else {
    echo "Data admin sudah ada.<br>";
}
?>