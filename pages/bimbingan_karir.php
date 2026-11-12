<?php
session_start();
if (!isset($_SESSION['username'])) { header("location:../login.php"); exit(); }
include '../config/koneksi.php';
$username_aktif = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username_aktif'");
$data_user = mysqli_fetch_assoc($query_user);
$foto_profil = "../assets/img/ratil.jpeg";

$sub = isset($_GET['sub']) ? $_GET['sub'] : 'konsultasi';
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

// ==================== PROSES SIMPAN / UPDATE MENU 1 & 3 (KONSULTASI & RENCANA) ====================
if (isset($_POST['simpan_konsultasi'])) {
    $tanggal          = $_POST['tanggal'];
    $id_siswa         = $_POST['id_siswa'];
    $topik_pembahasan = $_POST['topik_pembahasan'];
    $hasil_diskusi    = $_POST['hasil_diskusi'];
    $status_rencana   = $_POST['status_rencana'];
    $tujuan_tempat    = $_POST['tujuan_tempat'];

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $query = mysqli_query($koneksi, "UPDATE tb_bimbingan_karir SET tanggal='$tanggal', id_siswa='$id_siswa', topik_pembahasan='$topik_pembahasan', hasil_diskusi='$hasil_diskusi', status_rencana='$status_rencana', tujuan_tempat='$tujuan_tempat' WHERE id_karir='$_GET[id]'");
        $pesan = "Data catatan konsultasi karir diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_bimbingan_karir (tanggal, id_siswa, topik_pembahasan, hasil_diskusi, status_rencana, tujuan_tempat) VALUES ('$tanggal', '$id_siswa', '$topik_pembahasan', '$hasil_diskusi', '$status_rencana', '$tujuan_tempat')");
        $pesan = "Data catatan konsultasi karir ditambahkan!";
    }
    if ($query) { echo "<script>alert('$pesan'); window.location='bimbingan_karir.php?sub=konsultasi';</script>"; }
}

// ==================== PROSES SIMPAN / UPDATE MENU 2 (PEMETAAN AWAL) ====================
if (isset($_POST['simpan_pemetaan'])) {
    $id_siswa       = $_POST['id_siswa'];
    $pilihan_utama  = $_POST['pilihan_utama'];
    $bidang_jurusan = $_POST['bidang_jurusan'];
    $keterangan     = $_POST['keterangan'];

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $query = mysqli_query($koneksi, "UPDATE tb_pemetaan_karir SET id_siswa='$id_siswa', pilihan_utama='$pilihan_utama', bidang_jurusan='$bidang_jurusan', keterangan='$keterangan' WHERE id_pemetaan='$_GET[id]'");
        $pesan = "Data pemetaan awal diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_pemetaan_karir (id_siswa, pilihan_utama, bidang_jurusan, keterangan) VALUES ('$id_siswa', '$pilihan_utama', '$bidang_jurusan', '$keterangan')");
        $pesan = "Data pemetaan awal ditambahkan!";
    }
    if ($query) { echo "<script>alert('$pesan'); window.location='bimbingan_karir.php?sub=pemetaan';</script>"; }
}

// ==================== PROSES HAPUS DATA ====================
if ($aksi == 'hapus_konsultasi' && isset($_GET['id'])) {
    mysqli_query($koneksi, "DELETE FROM tb_bimbingan_karir WHERE id_karir='$_GET[id]'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='bimbingan_karir.php?sub=konsultasi';</script>";
}
if ($aksi == 'hapus_pemetaan' && isset($_GET['id'])) {
    mysqli_query($koneksi, "DELETE FROM tb_pemetaan_karir WHERE id_pemetaan='$_GET[id]'");
    echo "<script>alert('Data berhasil dihapus!'); window.location='bimbingan_karir.php?sub=pemetaan';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bimbingan Karir - SIM-BK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
        .user-profile-box { padding: 20px; border-top: 1px solid rgba(255,255,255,0.1); background: rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="sidebar-brand"><i class="fas fa-graduation-cap me-2 text-warning"></i>SIM-BK</div>
        <div class="sidebar-menu">
            <div class="text-uppercase text-white-50 small fw-bold px-3 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</div>
            <a href="../index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
            <a href="logbook_harian.php"><i class="fas fa-book"></i> Logbook Harian</a>
            <a href="konseling.php"><i class="fas fa-comments"></i> Konseling Siswa</a>
            <a href="bimbingan_karir.php" class="active"><i class="fas fa-briefcase"></i> Bimbingan Karir</a>
            <a href="penanganan_kasus.php"><i class="fas fa-exclamation-triangle"></i> Penanganan Kasus</a>
            <a href="bimbingan_klasikal.php"><i class="fas fa-chalkboard"></i> Bimbingan Klasikal</a>
        </div>
        <div class="user-profile-box">
            <div class="d-flex align-items-center">
                <img src="<?php echo $foto_profil; ?>" class="rounded-circle border border-white" width="40" height="40" style="object-fit: cover;">
                <div class="ms-3 text-white">
                    <h6 class="mb-0 fw-bold fs-6"><?php echo $data_user['nama_lengkap'] ?? $data_user['username']; ?></h6>
                    <small class="text-white-50" style="font-size: 0.75rem;">Guru BK</small>
                </div>
            </div>
            <a href="../proses/proses_logout.php" class="btn btn-light btn-sm rounded-circle p-2 text-danger shadow-sm" onclick="return confirm('Yakin ingin keluar?')"><i class="fas fa-power-off"></i></a>
        </div>
    </div>

    <div class="main-content">
        <!-- Header Judul & Tab Sub-Menu -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-briefcase text-warning me-2"></i>Manajemen Bimbingan Karir</h3>
                <p class="text-muted small">Kelola pemetaan awal, catatan konsultasi, serta data rencana studi lanjut siswa.</p>
            </div>
            <!-- Navigasi Sub-Menu (Tab) -->
            <div class="btn-group shadow-sm" role="group">
                <a href="bimbingan_karir.php?sub=konsultasi" class="btn <?php echo ($sub == 'konsultasi') ? 'btn-warning text-white fw-bold' : 'btn-light'; ?>">Menu 1: Catatan Konsultasi</a>
                <a href="bimbingan_karir.php?sub=pemetaan" class="btn <?php echo ($sub == 'pemetaan') ? 'btn-warning text-white fw-bold' : 'btn-light'; ?>">Menu 2: Pemetaan Awal</a>
                <a href="bimbingan_karir.php?sub=rencana" class="btn <?php echo ($sub == 'rencana') ? 'btn-warning text-white fw-bold' : 'btn-light'; ?>">Menu 3: Data Rencana Studi</a>
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- SUB-MENU 1: CATATAN KONSULTASI KARIR -->
        <!-- ================================================================= -->
        <?php if ($sub == 'konsultasi'): ?>
            <?php if ($aksi == 'tampil'): ?>
            <div class="d-flex justify-content-end mb-3">
                <a href="bimbingan_karir.php?sub=konsultasi&aksi=tambah" class="btn btn-warning rounded-pill px-4 text-white shadow-sm"><i class="fas fa-plus me-1"></i> Tambah Catatan Konsultasi</a>
            </div>
            <div class="card card-custom p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Siswa & NISN</th>
                                <th>Topik Pembahasan</th>
                                <th>Hasil Diskusi</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $q = mysqli_query($koneksi, "SELECT tb_bimbingan_karir.*, tb_siswa.nama_lengkap, tb_siswa.nisn, tb_siswa.kelas FROM tb_bimbingan_karir JOIN tb_siswa ON tb_bimbingan_karir.id_siswa = tb_siswa.id_siswa ORDER BY tanggal DESC");
                            if (mysqli_num_rows($q) > 0) {
                                while ($r = mysqli_fetch_assoc($q)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo date('d-m-Y', strtotime($r['tanggal'])); ?></strong></td>
                                <td><strong><?php echo $r['nama_lengkap']; ?></strong><br><small class="text-muted">NISN: <?php echo $r['nisn']; ?> (<?php echo $r['kelas']; ?>)</small></td>
                                <td><?php echo $r['topik_pembahasan']; ?></td>
                                <td><?php echo $r['hasil_diskusi']; ?></td>
                                <td class="text-center">
                                    <a href="bimbingan_karir.php?sub=konsultasi&aksi=edit&id=<?php echo $r['id_karir']; ?>" class="btn btn-warning btn-sm text-white mb-1"><i class="fas fa-edit"></i></a>
                                    <a href="bimbingan_karir.php?sub=konsultasi&aksi=hapus_konsultasi&id=<?php echo $r['id_karir']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php }} else { echo "<tr><td colspan='6' class='text-center text-muted py-4'>Belum ada catatan konsultasi karir.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
            <?php
            $tanggal = date('Y-m-d'); $id_s = ""; $topik = ""; $hasil = ""; $rencana = "Kuliah"; $tujuan = "";
            $judul = "Tambah Catatan Konsultasi Karir"; $btn = "Simpan Data"; $action = "bimbingan_karir.php?sub=konsultasi";
            if ($aksi == 'edit' && isset($_GET['id'])) {
                $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_bimbingan_karir WHERE id_karir='$_GET[id]'"));
                $tanggal = $edit['tanggal']; $id_s = $edit['id_siswa']; $topik = $edit['topik_pembahasan']; $hasil = $edit['hasil_diskusi']; $rencana = $edit['status_rencana']; $tujuan = $edit['tujuan_tempat'];
                $judul = "Edit Catatan Konsultasi Karir"; $btn = "Perbarui Data"; $action = "bimbingan_karir.php?sub=konsultasi&aksi=edit&id=" . $_GET['id'];
            }
            ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-custom p-4 p-md-5">
                        <h4 class="fw-bold text-dark mb-4"><i class="fas fa-comments text-warning me-2"></i><?php echo $judul; ?></h4>
                        <form action="<?php echo $action; ?>" method="POST">
                            <input type="hidden" name="status_rencana" value="<?php echo $rencana; ?>">
                            <input type="hidden" name="tujuan_tempat" value="<?php echo $tujuan; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tanggal Konsultasi</label>
                                <input type="date" name="tanggal" class="form-control" value="<?php echo $tanggal; ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Siswa (Ketik Nama / NISN)</label>
                                <select name="id_siswa" id="search-siswa" class="form-select" required>
                                    <option value="" disabled selected>-- Ketik nama atau NISN siswa --</option>
                                    <?php
                                    $qs = mysqli_query($koneksi, "SELECT * FROM tb_siswa ORDER BY nama_lengkap ASC");
                                    while ($s = mysqli_fetch_assoc($qs)) {
                                        $sel = ($id_s == $s['id_siswa']) ? 'selected' : '';
                                        echo "<option value='".$s['id_siswa']."' ".$sel.">".$s['nama_lengkap']." (NISN: ".$s['nisn']." - Kelas: ".$s['kelas'].")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Topik Pembahasan</label>
                                <input type="text" name="topik_pembahasan" class="form-control" value="<?php echo $topik; ?>" placeholder="Contoh: Konsultasi pilihan jurusan kuliah..." required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Hasil Diskusi</label>
                                <textarea name="hasil_diskusi" class="form-control" rows="4" placeholder="Uraikan hasil diskusi karir bersama siswa..." required><?php echo $hasil; ?></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="bimbingan_karir.php?sub=konsultasi" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                                <button type="submit" name="simpan_konsultasi" class="btn btn-warning px-4 text-white rounded-pill shadow-sm"><i class="fas fa-save me-1"></i> <?php echo $btn; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <!-- ================================================================= -->
        <!-- SUB-MENU 2: PEMETAAN AWAL KARIR SISWA -->
        <!-- ================================================================= -->
        <?php elseif ($sub == 'pemetaan'): ?>
            <?php if ($aksi == 'tampil'): ?>
            <div class="d-flex justify-content-end mb-3">
                <a href="bimbingan_karir.php?sub=pemetaan&aksi=tambah" class="btn btn-warning rounded-pill px-4 text-white shadow-sm"><i class="fas fa-plus me-1"></i> Tambah Pemetaan Awal</a>
            </div>
            <div class="card card-custom p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa & NISN</th>
                                <th>Pilihan Awal Karir</th>
                                <th>Bidang / Jurusan Diminati</th>
                                <th>Keterangan Singkat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $qp = mysqli_query($koneksi, "SELECT tb_pemetaan_karir.*, tb_siswa.nama_lengkap, tb_siswa.nisn, tb_siswa.kelas FROM tb_pemetaan_karir JOIN tb_siswa ON tb_pemetaan_karir.id_siswa = tb_siswa.id_siswa ORDER BY id_pemetaan DESC");
                            if (mysqli_num_rows($qp) > 0) {
                                while ($rp = mysqli_fetch_assoc($qp)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $rp['nama_lengkap']; ?></strong><br><small class="text-muted">NISN: <?php echo $rp['nisn']; ?> (<?php echo $rp['kelas']; ?>)</small></td>
                                <td><span class="badge bg-primary"><?php echo $rp['pilihan_utama']; ?></span></td>
                                <td><?php echo $rp['bidang_jurusan']; ?></td>
                                <td><?php echo $rp['keterangan']; ?></td>
                                <td class="text-center">
                                    <a href="bimbingan_karir.php?sub=pemetaan&aksi=edit&id=<?php echo $rp['id_pemetaan']; ?>" class="btn btn-warning btn-sm text-white mb-1"><i class="fas fa-edit"></i></a>
                                    <a href="bimbingan_karir.php?sub=pemetaan&aksi=hapus_pemetaan&id=<?php echo $rp['id_pemetaan']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php }} else { echo "<tr><td colspan='6' class='text-center text-muted py-4'>Belum ada data pemetaan awal karir.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
            <?php
            $id_s = ""; $pilihan = "Kuliah (Beasiswa)"; $bidang = ""; $ket = "";
            $judul = "Tambah Pemetaan Awal Karir"; $btn = "Simpan Data"; $action = "bimbingan_karir.php?sub=pemetaan";
            if ($aksi == 'edit' && isset($_GET['id'])) {
                $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_pemetaan_karir WHERE id_pemetaan='$_GET[id]'"));
                $id_s = $edit['id_siswa']; $pilihan = $edit['pilihan_utama']; $bidang = $edit['bidang_jurusan']; $ket = $edit['keterangan'];
                $judul = "Edit Pemetaan Awal Karir"; $btn = "Perbarui Data"; $action = "bimbingan_karir.php?sub=pemetaan&aksi=edit&id=" . $_GET['id'];
            }
            ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-custom p-4 p-md-5">
                        <h4 class="fw-bold text-dark mb-4"><i class="fas fa-compass text-warning me-2"></i><?php echo $judul; ?></h4>
                        <form action="<?php echo $action; ?>" method="POST">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Siswa (Ketik Nama / NISN)</label>
                                <select name="id_siswa" id="search-siswa" class="form-select" required>
                                    <option value="" disabled selected>-- Ketik nama atau NISN siswa --</option>
                                    <?php
                                    $qs = mysqli_query($koneksi, "SELECT * FROM tb_siswa ORDER BY nama_lengkap ASC");
                                    while ($s = mysqli_fetch_assoc($qs)) {
                                        $sel = ($id_s == $s['id_siswa']) ? 'selected' : '';
                                        echo "<option value='".$s['id_siswa']."' ".$sel.">".$s['nama_lengkap']." (NISN: ".$s['nisn']." - Kelas: ".$s['kelas'].")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Menu Pilihan Awal Karir</label>
                                <select name="pilihan_utama" class="form-select" required>
                                    <option value="Kuliah (Beasiswa)" <?php if($pilihan=='Kuliah (Beasiswa)') echo 'selected'; ?>>Kuliah (Beasiswa)</option>
                                    <option value="Kuliah Mandiri (PTN / PTS)" <?php if($pilihan=='Kuliah Mandiri (PTN / PTS)') echo 'selected'; ?>>Kuliah Mandiri (PTN / PTS)</option>
                                    <option value="Sekolah Kedinasan" <?php if($pilihan=='Sekolah Kedinasan') echo 'selected'; ?>>Sekolah Kedinasan</option>
                                    <option value="Bekerja" <?php if($pilihan=='Bekerja') echo 'selected'; ?>>Bekerja</option>
                                    <option value="Buka Usaha / Wirausaha" <?php if($pilihan=='Buka Usaha / Wirausaha') echo 'selected'; ?>>Buka Usaha / Wirausaha</option>
                                    <option value="Bingung / Belum Kepikiran" <?php if($pilihan=='Bingung / Belum Kepikiran') echo 'selected'; ?>>Bingung / Belum Kepikiran</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bidang / Jurusan yang Diminati (Teks Singkat)</label>
                                <input type="text" name="bidang_jurusan" class="form-control" value="<?php echo $bidang; ?>" placeholder="Contoh: Teknik Informatika, Kedokteran, Desain Grafis..." required>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Keterangan / Catatan Tambahan</label>
                                <textarea name="keterangan" class="form-control" rows="3" placeholder="Catatan kondisi atau minat bakat siswa..."><?php echo $ket; ?></textarea>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="bimbingan_karir.php?sub=pemetaan" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                                <button type="submit" name="simpan_pemetaan" class="btn btn-warning px-4 text-white rounded-pill shadow-sm"><i class="fas fa-save me-1"></i> <?php echo $btn; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

        <!-- ================================================================= -->
        <!-- SUB-MENU 3: DATA RENCANA STUDI / LANJUT -->
        <!-- ================================================================= -->
        <?php elseif ($sub == 'rencana'): ?>
            <?php if ($aksi == 'tampil'): ?>
            <div class="d-flex justify-content-end mb-3">
                <a href="bimbingan_karir.php?sub=rencana&aksi=tambah" class="btn btn-warning rounded-pill px-4 text-white shadow-sm"><i class="fas fa-plus me-1"></i> Tambah Data Rencana Studi</a>
            </div>
            <div class="card card-custom p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Siswa & NISN</th>
                                <th>Status Rencana</th>
                                <th>Tujuan (Kampus / Tempat Kerja)</th>
                                <th>Catatan Pendampingan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $qr = mysqli_query($koneksi, "SELECT tb_bimbingan_karir.*, tb_siswa.nama_lengkap, tb_siswa.nisn, tb_siswa.kelas FROM tb_bimbingan_karir JOIN tb_siswa ON tb_bimbingan_karir.id_siswa = tb_siswa.id_siswa ORDER BY tanggal DESC");
                            if (mysqli_num_rows($qr) > 0) {
                                while ($rr = mysqli_fetch_assoc($qr)) {
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td><strong><?php echo $rr['nama_lengkap']; ?></strong><br><small class="text-muted">NISN: <?php echo $rr['nisn']; ?> (<?php echo $rr['kelas']; ?>)</small></td>
                                <td><span class="badge <?php echo ($rr['status_rencana']=='Kuliah')?'bg-success':'bg-info'; ?>"><?php echo $rr['status_rencana']; ?></span></td>
                                <td><strong><?php echo $rr['tujuan_tempat']; ?></strong></td>
                                <td><?php echo $rr['topik_pembahasan']; ?></td>
                                <td class="text-center">
                                    <a href="bimbingan_karir.php?sub=rencana&aksi=edit&id=<?php echo $rr['id_karir']; ?>" class="btn btn-warning btn-sm text-white mb-1"><i class="fas fa-edit"></i></a>
                                    <a href="bimbingan_karir.php?sub=rencana&aksi=hapus_konsultasi&id=<?php echo $rr['id_karir']; ?>" class="btn btn-danger btn-sm mb-1" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            <?php }} else { echo "<tr><td colspan='6' class='text-center text-muted py-4'>Belum ada data rencana studi/lanjut.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
            <?php
            $tanggal = date('Y-m-d'); $id_s = ""; $status = "Kuliah"; $tujuan = ""; $topik = "Pendaftaran Kuliah/Kerja"; $hasil = "-";
            $judul = "Tambah Data Rencana Studi / Kerja"; $btn = "Simpan Data"; $action = "bimbingan_karir.php?sub=rencana";
            if ($aksi == 'edit' && isset($_GET['id'])) {
                $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_bimbingan_karir WHERE id_karir='$_GET[id]'"));
                $tanggal = $edit['tanggal']; $id_s = $edit['id_siswa']; $status = $edit['status_rencana']; $tujuan = $edit['tujuan_tempat']; $topik = $edit['topik_pembahasan']; $hasil = $edit['hasil_diskusi'];
                $judul = "Edit Data Rencana Studi / Kerja"; $btn = "Perbarui Data"; $action = "bimbingan_karir.php?sub=rencana&aksi=edit&id=" . $_GET['id'];
            }
            ?>
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card card-custom p-4 p-md-5">
                        <h4 class="fw-bold text-dark mb-4"><i class="fas fa-university text-warning me-2"></i><?php echo $judul; ?></h4>
                        <form action="<?php echo $action; ?>" method="POST">
                            <input type="hidden" name="tanggal" value="<?php echo $tanggal; ?>">
                            <input type="hidden" name="topik_pembahasan" value="<?php echo $topik; ?>">
                            <input type="hidden" name="hasil_diskusi" value="<?php echo $hasil; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Pilih Siswa (Ketik Nama / NISN)</label>
                                <select name="id_siswa" id="search-siswa" class="form-select" required>
                                    <option value="" disabled selected>-- Ketik nama atau NISN siswa --</option>
                                    <?php
                                    $qs = mysqli_query($koneksi, "SELECT * FROM tb_siswa ORDER BY nama_lengkap ASC");
                                    while ($s = mysqli_fetch_assoc($qs)) {
                                        $sel = ($id_s == $s['id_siswa']) ? 'selected' : '';
                                        echo "<option value='".$s['id_siswa']."' ".$sel.">".$s['nama_lengkap']." (NISN: ".$s['nisn']." - Kelas: ".$s['kelas'].")</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Kategori Rencana Lanjut</label>
                                <select name="status_rencana" class="form-select" required>
                                    <option value="Kuliah" <?php if($status=='Kuliah') echo 'selected'; ?>>Mendaftar Kuliah</option>
                                    <option value="Bekerja" <?php if($status=='Bekerja') echo 'selected'; ?>>Melamar Kerja</option>
                                </select>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Tujuan (Di Kampus Mana / Tempat Kerja Mana)</label>
                                <input type="text" name="tujuan_tempat" class="form-control" value="<?php echo $tujuan; ?>" placeholder="Contoh: Universitas Indonesia / PT Astra Honda Motor..." required>
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="bimbingan_karir.php?sub=rencana" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                                <button type="submit" name="simpan_konsultasi" class="btn btn-warning px-4 text-white rounded-pill shadow-sm"><i class="fas fa-save me-1"></i> <?php echo $btn; ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script> $(document).ready(function() { $('#search-siswa').select2({ theme: 'bootstrap-5' }); }); </script>
    <script src="../assets/js/print-pdf.js"></script>
</body>
</html>