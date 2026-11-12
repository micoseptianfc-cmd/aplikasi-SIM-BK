<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_bk"; // Pastikan nama database-nya db_bk

$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>