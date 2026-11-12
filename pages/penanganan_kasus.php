<?php
session_start();
if (!isset($_SESSION['username'])) { header("location:../login.php"); exit(); }
include '../config/koneksi.php';
$username_aktif = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username_aktif'");
$data_user = mysqli_fetch_assoc($query_user);
$foto_profil = "../assets/img/ratil.jpeg";
$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

// ==================== PROSES SIMPAN / UPDATE PENANGANAN KASUS ====================
if (isset($_POST['simpan'])) {
    $id_siswa         = $_POST['id_siswa'];
    $tanggal_kejadian = $_POST['tanggal_kejadian'];
    $kategori         = $_POST['kategori'];
    $kronologi        = $_POST['kronologi'];
    $pihak_terlibat   = $_POST['pihak_terlibat'];

    // Upload Surat Panggilan Ortu
    $surat_panggilan_lama = $_POST['surat_panggilan_lama'] ?? '';
    $nama_file_sp = $surat_panggilan_lama;
    if (isset($_FILES['surat_panggilan']['name']) && $_FILES['surat_panggilan']['error'] == 0) {
        $nama_asli = $_FILES['surat_panggilan']['name'];
        $nama_file_sp = 'sp_' . time() . '_' . basename($nama_asli);
        move_uploaded_file($_FILES['surat_panggilan']['tmp_name'], '../assets/img/upload_kasus/' . $nama_file_sp);
    }

    // Upload Surat Perjanjian Siswa
    $surat_perjanjian_lama = $_POST['surat_perjanjian_lama'] ?? '';
    $nama_file_sperj = $surat_perjanjian_lama;
    if (isset($_FILES['surat_perjanjian']['name']) && $_FILES['surat_perjanjian']['error'] == 0) {
        $nama_asli = $_FILES['surat_perjanjian']['name'];
        $nama_file_sperj = 'sperj_' . time() . '_' . basename($nama_asli);
        move_uploaded_file($_FILES['surat_perjanjian']['tmp_name'], '../assets/img/upload_kasus/' . $nama_file_sperj);
    }

    if (!is_dir('../assets/img/upload_kasus/')) {
        mkdir('../assets/img/upload_kasus/', 0777, true);
    }

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id = $_GET['id'];
        $query = mysqli_query($koneksi, "UPDATE tb_kasus SET id_siswa='$id_siswa', tanggal_kejadian='$tanggal_kejadian', kategori='$kategori', kronologi='$kronologi', pihak_terlibat='$pihak_terlibat', surat_panggilan='$nama_file_sp', surat_perjanjian='$nama_file_sperj' WHERE id_kasus='$id'");
        $pesan = "Data penanganan kasus berhasil diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_kasus (id_siswa, tanggal_kejadian, kategori, kronologi, pihak_terlibat, surat_panggilan, surat_perjanjian) VALUES ('$id_siswa', '$tanggal_kejadian', '$kategori', '$kronologi', '$pihak_terlibat', '$nama_file_sp', '$nama_file_sperj')");
        $pesan = "Data penanganan kasus berhasil ditambahkan!";
    }
    if ($query) { echo "<script>alert('$pesan'); window.location='penanganan_kasus.php';</script>"; }
}

// ==================== PROSES HAPUS ====================
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $q = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT surat_panggilan, surat_perjanjian FROM tb_kasus WHERE id_kasus='$id'"));
    if (!empty($q['surat_panggilan']) && file_exists('../assets/img/upload_kasus/' . $q['surat_panggilan'])) {
        unlink('../assets/img/upload_kasus/' . $q['surat_panggilan']);
    }
    if (!empty($q['surat_perjanjian']) && file_exists('../assets/img/upload_kasus/' . $q['surat_perjanjian'])) {
        unlink('../assets/img/upload_kasus/' . $q['surat_perjanjian']);
    }
    mysqli_query($koneksi, "DELETE FROM tb_kasus WHERE id_kasus='$id'");
    echo "<script>alert('Data kasus berhasil dihapus!'); window.location='penanganan_kasus.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Penanganan Kasus - SIM-BK</title>
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
            <a href="bimbingan_karir.php"><i class="fas fa-briefcase"></i> Bimbingan Karir</a>
            <a href="penanganan_kasus.php" class="active"><i class="fas fa-exclamation-triangle"></i> Penanganan Kasus</a>
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
        <?php if ($aksi == 'tampil'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-exclamation-triangle text-danger me-2"></i>Penanganan Kasus & Berita Acara</h3>
                <p class="text-muted small">Kelola identitas, kronologi, pihak terlibat, serta manajemen dokumen surat panggilan dan perjanjian.</p>
            </div>
            <a href="penanganan_kasus.php?aksi=tambah" class="btn btn-danger rounded-pill px-4 shadow-sm text-white"><i class="fas fa-plus me-1"></i> Tambah Penanganan Kasus</a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal Kejadian</th>
                            <th>Identitas Siswa (Kls, Kelamin, Alamat)</th>
                            <th>Kategori & Kronologi</th>
                            <th>Pihak Terlibat</th>
                            <th>Dokumen Berita Acara</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $q = mysqli_query($koneksi, "SELECT tb_kasus.*, tb_siswa.nama_lengkap, tb_siswa.nisn, tb_siswa.kelas, tb_siswa.alamat FROM tb_kasus JOIN tb_siswa ON tb_kasus.id_siswa = tb_siswa.id_siswa ORDER BY tanggal_kejadian DESC");
                        if (mysqli_num_rows($q) > 0) {
                            while ($r = mysqli_fetch_assoc($q)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo date('d-m-Y', strtotime($r['tanggal_kejadian'])); ?></strong></td>
                            <td>
                                <strong><?php echo $r['nama_lengkap']; ?></strong><br>
                                <small class="text-muted">NISN: <?php echo $r['nisn']; ?> | Kls: <?php echo $r['kelas']; ?></small><br>
                                <small class="text-muted">Alamat: <?php echo mb_strimwidth($r['alamat'], 0, 30, "..."); ?></small>
                            </td>
                            <td>
                                <span class="badge <?php echo ($r['kategori']=='Berat')?'bg-danger':(($r['kategori']=='Sedang')?'bg-warning text-dark':'bg-success'); ?> mb-1">
                                    <?php echo $r['kategori']; ?>
                                </span><br>
                                <span><?php echo mb_strimwidth($r['kronologi'], 0, 40, "..."); ?></span>
                            </td>
                            <td><?php echo $r['pihak_terlibat']; ?></td>
                            <td>
                                <?php if (!empty($r['surat_panggilan'])): ?>
                                    <a href="../assets/img/upload_kasus/<?php echo $r['surat_panggilan']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary mb-1 d-block"><i class="fas fa-file-pdf me-1"></i> Surat Panggilan</a>
                                <?php endif; ?>
                                <?php if (!empty($r['surat_perjanjian'])): ?>
                                    <a href="../assets/img/upload_kasus/<?php echo $r['surat_perjanjian']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary d-block"><i class="fas fa-file-contract me-1"></i> Surat Perjanjian</a>
                                <?php endif; ?>
                                <?php if (empty($r['surat_panggilan']) && empty($r['surat_perjanjian'])): ?>
                                    <span class="text-muted small">Tidak ada dokumen</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="penanganan_kasus.php?aksi=edit&id=<?php echo $r['id_kasus']; ?>" class="btn btn-warning btn-sm text-white mb-1" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="penanganan_kasus.php?aksi=hapus&id=<?php echo $r['id_kasus']; ?>" class="btn btn-danger btn-sm mb-1" title="Hapus" onclick="return confirm('Hapus data kasus ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php }} else { echo "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada data penanganan kasus.</td></tr>"; } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
        <?php
        $id_s = ""; $tgl = date('Y-m-d'); $kat = "Ringan"; $kron = ""; $pihak = ""; $sp_lama = ""; $sperj_lama = "";
        $judul = "Tambah Penanganan Kasus Baru"; $btn = "Simpan Data"; $action = "penanganan_kasus.php";
        if ($aksi == 'edit' && isset($_GET['id'])) {
            $edit = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM tb_kasus WHERE id_kasus='$_GET[id]'"));
            $id_s = $edit['id_siswa']; $tgl = $edit['tanggal_kejadian']; $kat = $edit['kategori']; $kron = $edit['kronologi']; $pihak = $edit['pihak_terlibat']; $sp_lama = $edit['surat_panggilan']; $sperj_lama = $edit['surat_perjanjian'];
            $judul = "Edit Penanganan Kasus"; $btn = "Perbarui Data"; $action = "penanganan_kasus.php?aksi=edit&id=" . $_GET['id'];
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4 p-md-5">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-exclamation-triangle text-danger me-2"></i><?php echo $judul; ?></h3>
                    <form action="<?php echo $action; ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="surat_panggilan_lama" value="<?php echo $sp_lama; ?>">
                        <input type="hidden" name="surat_perjanjian_lama" value="<?php echo $sperj_lama; ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Siswa (Pencarian Nama & NISN)</label>
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
                            <label class="form-label fw-semibold">Tanggal Kejadian</label>
                            <input type="date" name="tanggal_kejadian" class="form-control" value="<?php echo $tgl; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kategori Kasus</label>
                            <select name="kategori" class="form-select" required>
                                <option value="Ringan" <?php if($kat=='Ringan') echo 'selected'; ?>>Ringan</option>
                                <option value="Sedang" <?php if($kat=='Sedang') echo 'selected'; ?>>Sedang</option>
                                <option value="Berat" <?php if($kat=='Berat') echo 'selected'; ?>>Berat</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Uraian Kronologi Kasus</label>
                            <textarea name="kronologi" class="form-control" rows="4" placeholder="Uraikan kronologi kejadian secara runtut..." required><?php echo $kron; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pihak yang Terlibat</label>
                            <input type="text" name="pihak_terlibat" class="form-control" value="<?php echo $pihak; ?>" placeholder="Contoh: Siswa bersangkutan, Guru piket, Orang tua siswa..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Manajemen Dokumen: Surat Panggilan Orang Tua (Opsional)</label>
                            <?php if(!empty($sp_lama)): ?>
                                <div class="mb-1"><small class="text-muted">File saat ini: <a href="../assets/img/upload_kasus/<?php echo $sp_lama; ?>" target="_blank"><?php echo $sp_lama; ?></a></small></div>
                            <?php endif; ?>
                            <input type="file" name="surat_panggilan" class="form-control">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Manajemen Dokumen: Surat Perjanjian Siswa (Opsional)</label>
                            <?php if(!empty($sperj_lama)): ?>
                                <div class="mb-1"><small class="text-muted">File saat ini: <a href="../assets/img/upload_kasus/<?php echo $sperj_lama; ?>" target="_blank"><?php echo $sperj_lama; ?></a></small></div>
                            <?php endif; ?>
                            <input type="file" name="surat_perjanjian" class="form-control">
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="penanganan_kasus.php" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                            <button type="submit" name="simpan" class="btn btn-danger px-4 rounded-pill shadow-sm"><i class="fas fa-save me-1"></i> <?php echo $btn; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script> $(document).ready(function() { $('#search-siswa').select2({ theme: 'bootstrap-5' }); }); </script>
    <script src="../assets/js/print-pdf.js"></script>
</body>
</html>