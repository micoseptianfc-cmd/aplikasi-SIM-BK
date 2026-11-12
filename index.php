<?php
session_start();
if (!isset($_SESSION['username'])) { header("location:login.php"); exit(); }
include 'config/koneksi.php';
$username_aktif = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username_aktif'");
$data_user = mysqli_fetch_assoc($query_user);
$foto_profil = "assets/img/ratil.jpeg";

// Fungsi aman untuk menghitung baris tabel
function hitungData($koneksi, $nama_tabel) {
    $q = mysqli_query($koneksi, "SHOW TABLES LIKE '$nama_tabel'");
    if ($q && mysqli_num_rows($q) > 0) {
        $res = mysqli_query($koneksi, "SELECT COUNT(*) as j FROM $nama_tabel");
        if ($res) {
            $row = mysqli_fetch_assoc($res);
            return $row['j'] ?? 0;
        }
    }
    return 0;
}

// Menghitung jumlah data per modul
$total_siswa     = hitungData($koneksi, 'tb_siswa');
$total_logbook   = hitungData($koneksi, 'tb_logbook');
$total_konseling = hitungData($koneksi, 'tb_konseling');
$total_karir     = hitungData($koneksi, 'tb_bimbingan_karir');
$total_kasus     = hitungData($koneksi, 'tb_kasus');
$total_klasikal  = hitungData($koneksi, 'tb_bimbingan_klasikal');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <title>Dashboard - SIM-BK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .sidebar { width: 280px; height: 100vh; background: linear-gradient(180deg, #5A3D77 0%, #2A1835 100%); position: fixed; top: 0; left: 0; z-index: 100; display: flex; flex-direction: column; box-shadow: 4px 0 15px rgba(0,0,0,0.05); }
        .sidebar-brand { padding: 25px 20px; font-size: 1.25rem; font-weight: 700; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { padding: 20px 15px; flex-grow: 1; overflow-y: auto; }
        .sidebar-menu a { display: flex; align-items: center; color: rgba(255,255,255,0.8); text-decoration: none; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; font-weight: 500; transition: all 0.3s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background-color: rgba(255,255,255,0.15); color: #fff; transform: translateX(5px); }
        .sidebar-menu a i { margin-right: 12px; font-size: 1.1rem; }
        .main-content {
            margin-left: 280px;
            padding: 30px;
            min-height: 100vh;
            background-color: #f4f7f6;
            /* Menambahkan tekstur pola titik-titik yang elegan */
            background-image: radial-gradient(#d1d8df 1px, transparent 1px);
            background-size: 20px 20px;
        }
        .card-custom { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .stat-card { border-left: 5px solid; }
        .user-profile-box { padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-graduation-cap me-2 text-warning"></i>SIM-BK</div>
        <div class="sidebar-menu">
            <div class="text-uppercase text-white-50 small fw-bold px-3 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</div>
            <a href="index.php" class="active"><i class="fas fa-home"></i> Dashboard</a>
            <a href="pages/data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
            <a href="pages/logbook_harian.php"><i class="fas fa-book"></i> Logbook Harian</a>
            <a href="pages/konseling.php"><i class="fas fa-comments"></i> Konseling Siswa</a>
            <a href="pages/bimbingan_karir.php"><i class="fas fa-briefcase"></i> Bimbingan Karir</a>
            <a href="pages/penanganan_kasus.php"><i class="fas fa-exclamation-triangle"></i> Penanganan Kasus</a>
            <a href="pages/bimbingan_klasikal.php"><i class="fas fa-chalkboard"></i> Bimbingan Klasikal</a>
        </div>
        <div class="user-profile-box">
            <div class="d-flex align-items-center">
                <img src="<?php echo $foto_profil; ?>" class="rounded-circle border border-white" width="40" height="40" style="object-fit: cover;">
                <div class="ms-3 text-white">
                    <h6 class="mb-0 fw-bold fs-6"><?php echo $data_user['nama_lengkap'] ?? $data_user['username']; ?></h6>
                    <small class="text-white-50" style="font-size: 0.75rem;">Guru BK</small>
                </div>
            </div>
            <a href="proses/proses_logout.php" class="btn btn-light btn-sm rounded-circle p-2 text-danger shadow-sm" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i></a>
        </div>
    </div>

    <div class="main-content">
        <div class="card card-custom p-4 bg-white mb-4">
            <h3 class="fw-bold text-dark mb-1">Halo, <?php echo $data_user['username']; ?>! 👋</h3>
            <p class="text-muted small mb-0">Berikut adalah rekapitulasi data layanan bimbingan konseling secara keseluruhan.</p>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3 bg-white border-primary">
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Total Siswa</p>
                    <h2 class="fw-bold text-dark mb-0"><?php echo $total_siswa; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3 bg-white border-success">
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Logbook Harian</p>
                    <h2 class="fw-bold text-dark mb-0"><?php echo $total_logbook; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3 bg-white border-info">
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Sesi Konseling</p>
                    <h2 class="fw-bold text-dark mb-0"><?php echo $total_konseling; ?></h2>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card card-custom stat-card p-3 bg-white border-danger">
                    <p class="text-muted small mb-1 fw-semibold text-uppercase">Kasus Aktif</p>
                    <h2 class="fw-bold text-dark mb-0"><?php echo $total_kasus; ?></h2>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-custom p-4 bg-white">
                    <h5 class="fw-bold text-dark mb-3"><i class="fas fa-chart-bar text-primary me-2"></i>Grafik Rekapitulasi Layanan Bimbingan Konseling</h5>
                    <hr class="text-muted opacity-25">
                    <div style="position: relative; height: 320px; width: 100%;">
                        <canvas id="grafikSimBk"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const ctx = document.getElementById('grafikSimBk').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Data Siswa', 'Logbook Harian', 'Konseling Siswa', 'Bimbingan Karir', 'Penanganan Kasus', 'Bimbingan Klasikal'],
                datasets: [{
                    label: 'Jumlah Data',
                    data: [
                        <?php echo $total_siswa; ?>, 
                        <?php echo $total_logbook; ?>, 
                        <?php echo $total_konseling; ?>, 
                        <?php echo $total_karir; ?>, 
                        <?php echo $total_kasus; ?>, 
                        <?php echo $total_klasikal; ?>
                    ],
                    backgroundColor: ['#1e3c72', '#28a745', '#17a2b8', '#ffc107', '#dc3545', '#6c757d'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } },
                plugins: { legend: { display: false } }
            }
        });
    </script>
    <script src="assets/js/print-pdf.js"></script>
</body>
</html>