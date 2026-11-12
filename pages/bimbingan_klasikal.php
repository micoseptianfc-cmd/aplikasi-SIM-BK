<?php
session_start();
if (!isset($_SESSION['username'])) { header("location:../login.php"); exit(); }
include '../config/koneksi.php';
$username_aktif = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username_aktif'");
$data_user = mysqli_fetch_assoc($query_user);
$foto_profil = "../assets/img/ratil.jpeg";
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

// ==================== PROSES SIMPAN / UPDATE BIMBINGAN KLASIKAL ====================
if (isset($_POST['simpan'])) {
    $tanggal           = $_POST['tanggal'];
    $kelas_tujuan      = $_POST['kelas_tujuan'];
    $topik_materi      = $_POST['topik_materi'];
    
    // Rekap Kehadiran
    $hadir  = $_POST['hadir'] ?? 0;
    $sakit  = $_POST['sakit'] ?? 0;
    $izin   = $_POST['izin'] ?? 0;
    $alfa   = $_POST['alfa'] ?? 0;
    $rekap_kehadiran = "Hadir: $hadir, Sakit: $sakit, Izin: $izin, Alfa: $alfa";

    $dinamika_kelompok = $_POST['dinamika_kelompok'];

    // Manajemen RPL (Upload file atau teks keterangan)
    $manajemen_rpl_lama = $_POST['manajemen_rpl_lama'] ?? '';
    $nama_file_rpl = $manajemen_rpl_lama;
    if (isset($_FILES['manajemen_rpl']['name']) && $_FILES['manajemen_rpl']['error'] == 0) {
        $nama_asli = $_FILES['manajemen_rpl']['name'];
        $nama_file_rpl = 'rpl_' . time() . '_' . basename($nama_asli);
        move_uploaded_file($_FILES['manajemen_rpl']['tmp_name'], '../assets/img/upload_rpl/' . $nama_file_rpl);
    }

    if (!is_dir('../assets/img/upload_rpl/')) {
        mkdir('../assets/img/upload_rpl/', 0777, true);
    }

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $query = mysqli_query($koneksi, "UPDATE tb_bimbingan_klasikal SET tanggal='$tanggal', kelas_tujuan='$kelas_tujuan', topik_materi='$topik_materi', rekap_kehadiran='$rekap_kehadiran', manajemen_rpl='$nama_file_rpl', dinamika_kelompok='$dinamika_kelompok' WHERE id_klasikal='$id'");
        $pesan = "Data bimbingan klasikal berhasil diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_bimbingan_klasikal (tanggal, kelas_tujuan, topik_materi, rekap_kehadiran, manajemen_rpl, dinamika_kelompok) VALUES ('$tanggal', '$kelas_tujuan', '$topik_materi', '$rekap_kehadiran', '$nama_file_rpl', '$dinamika_kelompok')");
        $pesan = "Data bimbingan klasikal berhasil ditambahkan!";
    }
    if ($query) { echo "<script>alert('$pesan'); window.location='bimbingan_klasikal.php';</script>"; }
}

// ==================== PROSES HAPUS ====================
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $q = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT manajemen_rpl FROM tb_bimbingan_klasikal WHERE id_klasikal='$id'"));
    if (!empty($q['manajemen_rpl']) && file_exists('../assets/img/upload_rpl/' . $q['manajemen_rpl'])) {
        unlink('../assets/img/upload_rpl/' . $q['manajemen_rpl']);
    }
    mysqli_query($koneksi, "DELETE FROM tb_bimbingan_klasikal WHERE id_klasikal='$id'");
    echo "<script>alert('Data bimbingan klasikal berhasil dihapus!'); window.location='bimbingan_klasikal.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bimbingan Klasikal - SIM-BK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <a href="bimbingan_karir.php"><i class="fas fa-briefcase"></i> Bimbingan Karir</a>
            <a href="penanganan_kasus.php"><i class="fas fa-exclamation-triangle"></i> Penanganan Kasus</a>
            <a href="bimbingan_klasikal.php" class="active"><i class="fas fa-chalkboard"></i> Bimbingan Klasikal</a>
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
        <?php if ($aksi == 'tampil'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-chalkboard text-success me-2"></i>Bimbingan Klasikal (Masuk Kelas)</h3>
                <p class="text-muted small">Kelola jadwal, rombel, materi, rekap kehadiran, manajemen RPL, dan dinamika kelompok.</p>
            </div>
            <a href="bimbingan_klasikal.php?aksi=tambah" class="btn btn-success rounded-pill px-4 shadow-sm text-white"><i class="fas fa-plus me-1"></i> Tambah Bimbingan Klasikal</a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal / Jadwal</th>
                            <th>Rombel (Kelas)</th>
                            <th>Topik & Materi</th>
                            <th>Daftar Hadir Siswa</th>
                            <th>Manajemen RPL</th>
                            <th>Dinamika Kelompok</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q = mysqli_query($koneksi, "SELECT * FROM tb_bimbingan_klasikal ORDER BY tanggal DESC");
                        if (mysqli_num_rows($q) > 0) {
                            while ($r = mysqli_fetch_assoc($q)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo date('d-m-Y', strtotime($r['tanggal'])); ?></strong></td>
                            <td><span class="badge bg-secondary"><?php echo $r['kelas_tujuan']; ?></span></td>
                            <td><strong><?php echo $r['topik_materi']; ?></strong></td>
                            <td><small class="text-dark fw-semibold"><?php echo $r['rekap_kehadiran']; ?></small></td>
                            <td>
                                <?php if (!empty($r['manajemen_rpl'])): ?>
                                    <a href="../assets/img/upload_rpl/<?php echo $r['manajemen_rpl']; ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="fas fa-file-alt me-1"></i> Lihat RPL</a>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada file</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo mb_strimwidth($r['dinamika_kelompok'], 0, 35, "..."); ?></td>
                            <td class="text-center">
                                <a href="bimbingan_klasikal.php?aksi=edit&id=<?php echo $r['id_klasikal']; ?>" class="btn btn-warning btn-sm text-white mb-1" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="bimbingan_klasikal.php?aksi=hapus&id=<?php echo $r['id_klasikal']; ?>" class="btn btn-danger btn-sm mb-1" title="Hapus" onclick="return confirm('Hapus data ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php }} else { echo "<tr><td colspan='8' class='text-center text-muted py-4'>Belum ada data bimbingan klasikal.</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
        <?php
        $tanggal = date('Y-m-d'); $kelas = ""; $topik = ""; $h = 30; $s = 0; $i = 0; $a = 0; $rpl_lama = ""; $dinamika = "";
        $judul = "Tambah Bimbingan Klasikal Baru"; $btn = "Simpan Data"; $action = "bimbingan_klasikal.php";
        if ($aksi == 'edit' && isset($_GET['id'])) {
            $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_bimbingan_klasikal WHERE id_klasikal='$_GET[id]'"));
            $tanggal = $edit['tanggal']; $kelas = $edit['kelas_tujuan']; $topik = $edit['topik_materi']; $rpl_lama = $edit['manajemen_rpl']; $dinamika = $edit['dinamika_kelompok'];
            
            // Parsing string rekap kehadiran (misal: Hadir: 30, Sakit: 1, Izin: 0, Alfa: 0)
            if (!empty($edit['rekap_kehadiran'])) {
                sscanf($edit['rekap_kehadiran'], "Hadir: %d, Sakit: %d, Izin: %d, Alfa: %d", $h, $s, $i, $a);
            }
            
            $judul = "Edit Bimbingan Klasikal"; $btn = "Perbarui Data"; $action = "bimbingan_klasikal.php?aksi=edit&id=" . $_GET['id'];
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4 p-md-5">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-chalkboard text-success me-2"></i><?php echo $judul; ?></h3>
                    <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="manajemen_rpl_lama" value="<?php echo $rpl_lama; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jadwal / Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal" class="form-control" value="<?php echo $tanggal; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Rombel (Kelas)</label>
                            <input type="text" name="kelas_tujuan" class="form-control" value="<?php echo $kelas; ?>" placeholder="Contoh: VII A, VIII B, IX C..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Materi / Topik Bimbingan</label>
                            <input type="text" name="topik_materi" class="form-control" value="<?php echo $topik; ?>" placeholder="Contoh: Manajemen Waktu Belajar, Bahaya Bullying..." required>
                        </div>
                        
                        <!-- Rekap Kehadiran Siswa -->
                        <div class="card bg-light border-0 p-3 mb-3 rounded-4">
                            <label class="form-label fw-bold text-secondary mb-2"><i class="fas fa-user-check me-1"></i> Rekap Daftar Hadir Siswa</label>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <label class="form-label small">Hadir</label>
                                    <input type="number" name="hadir" class="form-control" value="<?php echo $h; ?>" min="0" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label small">Sakit</label>
                                    <input type="number" name="sakit" class="form-control" value="<?php echo $s; ?>" min="0" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label small">Izin</label>
                                    <input type="number" name="izin" class="form-control" value="<?php echo $i; ?>" min="0" required>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <label class="form-label small">Alfa</label>
                                    <input type="number" name="alfa" class="form-control" value="<?php echo $a; ?>" min="0" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Manajemen RPL (Rencana Pelaksanaan Layanan - Upload Dokumen)</label>
                            <?php if(!empty($rpl_lama)): ?>
                                <div class="mb-1"><small class="text-muted">File RPL saat ini: <a href="../assets/img/upload_rpl/<?php echo $rpl_lama; ?>" target="_blank"><?php echo $rpl_lama; ?></a></small></div>
                            <?php endif; ?>
                            <input type="file" name="manajemen_rpl" class="form-control">
                            <small class="text-muted">Unggah dokumen RPL (PDF, Word, atau gambar pendukung).</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan Dinamika Kelompok</label>
                            <textarea name="dinamika_kelompok" class="form-control" rows="4" placeholder="Uraikan keaktifan, antusiasme, atau kendala interaksi siswa selama layanan di kelas..." required><?php echo $dinamika; ?></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="bimbingan_klasikal.php" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                            <button type="submit" name="simpan" class="btn btn-success px-4 rounded-pill shadow-sm"><i class="fas fa-save me-1"></i> <?php echo $btn; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/print-pdf.js"></script>
</body>
</html>