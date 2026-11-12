<?php
// Memanggil koneksi database
include '../config/koneksi.php';

// Menangkap data yang dikirim dari form login.php
$username = $_POST['username'];
$password = $_POST['password'];

// Mencari data user di database
$login = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username' AND password='$password'");
$cek = mysqli_num_rows($login);

// Jika data ditemukan (username dan password cocok)
if($cek > 0){
    session_start();
    $data = mysqli_fetch_assoc($login);
    
    // Menyimpan sesi login
    $_SESSION['username'] = $data['username'];
    $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
    $_SESSION['level'] = $data['level'];
    
    // Mengarahkan ke halaman dashboard (index.php)
    header("location:../index.php");
}else{
    // Jika gagal, kembali ke halaman login dengan pesan alert
    echo "<script>alert('Username atau Password salah!'); window.location='../login.php';</script>";
}
?>