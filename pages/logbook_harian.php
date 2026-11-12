<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location:../login.php");
    exit();
}
include '../config/koneksi.php';
$username_aktif = $_SESSION['username'];
$query_user = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username='$username_aktif'");
$data_user = mysqli_fetch_assoc($query_user);
$foto_profil = "../assets/img/ratil.jpeg";

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : 'tampil';

// ==================== PROSES SIMPAN / UPDATE LOGBOOK ====================
if (isset($_POST['simpan'])) {
    $tanggal_waktu  = $_POST['tanggal_waktu'];
    $jenis_kegiatan = $_POST['jenis_kegiatan'];
    $sasaran        = $_POST['sasaran'];
    $ringkasan      = $_POST['ringkasan'];
    
    // Penanganan Upload File Dokumentasi (Foto/File)
    $nama_file_dok = $_POST['dokumen_lama'] ?? '';
    if (isset($_FILES['dokumentasi']['name']) && $_FILES['dokumentasi']['error'] == 0) {
        $lokasi_tmp  = $_FILES['dokumentasi']['tmp_name'];
        $nama_asli   = $_FILES['dokumentasi']['name'];
        
        $nama_file_dok = time() . '_' . basename($nama_asli);
        $folder_tujuan = '../assets/img/upload_logbook/' . $nama_file_dok;
        
        if (!is_dir('../assets/img/upload_logbook/')) {
            mkdir('../assets/img/upload_logbook/', 0777, true);
        }
        
        move_uploaded_file($lokasi_tmp, $folder_tujuan);
    }

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id_logbook = $_GET['id'];
        $query = mysqli_query($koneksi, "UPDATE tb_logbook SET tanggal_waktu='$tanggal_waktu', jenis_kegiatan='$jenis_kegiatan', sasaran='$sasaran', ringkasan='$ringkasan', dokumentasi='$nama_file_dok' WHERE id_logbook='$id_logbook'");
        $pesan = "Logbook harian berhasil diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_logbook (tanggal_waktu, jenis_kegiatan, sasaran, ringkasan, dokumentasi) VALUES ('$tanggal_waktu', '$jenis_kegiatan', '$sasaran', '$ringkasan', '$nama_file_dok')");
        $pesan = "Logbook harian berhasil ditambahkan!";
    }

    if ($query) {
        echo "<script>alert('$pesan'); window.location='logbook_harian.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses data.');</script>";
    }
}

// ==================== PROSES HAPUS LOGBOOK ====================
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id_logbook = $_GET['id'];
    
    $q_file = mysqli_query($koneksi, "SELECT dokumentasi FROM tb_logbook WHERE id_logbook='$id_logbook'");
    $d_file = mysqli_fetch_assoc($q_file);
    if (!empty($d_file['dokumentasi']) && file_exists('../assets/img/upload_logbook/' . $d_file['dokumentasi'])) {
        unlink('../assets/img/upload_logbook/' . $d_file['dokumentasi']);
    }

    $query_hapus = mysqli_query($koneksi, "DELETE FROM tb_logbook WHERE id_logbook='$id_logbook'");
    if ($query_hapus) {
        echo "<script>alert('Logbook berhasil dihapus!'); window.location='logbook_harian.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='logbook_harian.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Harian - SIM-BK</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; overflow-x: hidden; }
        .sidebar {
            width: 280px; height: 100vh;
            background: linear-gradient(180deg, #5A3D77 0%, #2A1835 100%);
            position: fixed; top: 0; left: 0; z-index: 100;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 15px rgba(0,0,0,0.05);
        }
        .sidebar-brand { padding: 25px 20px; font-size: 1.25rem; font-weight: 700; color: #fff; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu { padding: 20px 15px; flex-grow: 1; overflow-y: auto; }
        .sidebar-menu a {
            display: flex; align-items: center; color: rgba(255,255,255,0.8);
            text-decoration: none; padding: 12px 15px; border-radius: 10px; margin-bottom: 8px; font-weight: 500; transition: all 0.3s ease;
        }
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
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2 text-warning"></i>SIM-BK
        </div>
        
        <div class="sidebar-menu">
            <div class="text-uppercase text-white-50 small fw-bold px-3 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</div>
            <a href="../index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
            <a href="logbook_harian.php" class="active"><i class="fas fa-book"></i> Logbook Harian</a>
            <a href="konseling.php"><i class="fas fa-comments"></i> Konseling Siswa</a>
            <a href="bimbingan_karir.php"><i class="fas fa-briefcase"></i> Bimbingan Karir</a>
            <a href="penanganan_kasus.php"><i class="fas fa-exclamation-triangle"></i> Penanganan Kasus</a>
            <a href="bimbingan_klasikal.php"><i class="fas fa-chalkboard"></i> Bimbingan Klasikal</a>
        </div>

        <div class="user-profile-box">
            <div class="d-flex align-items-center">
                <img src="<?php echo $foto_profil; ?>" alt="Profil" class="rounded-circle border border-white" width="40" height="40" style="object-fit: cover;">
                <div class="ms-3 text-white">
                    <h6 class="mb-0 fw-bold fs-6"><?php echo $data_user['nama_lengkap'] ?? $data_user['username']; ?></h6>
                    <small class="text-white-50" style="font-size: 0.75rem;"><?php echo $data_user['level'] ?? 'Guru BK'; ?></small>
                </div>
            </div>
            <a href="../proses/proses_logout.php" class="btn btn-light btn-sm rounded-circle p-2 text-danger shadow-sm" title="Logout" onclick="return confirm('Yakin ingin keluar dari sistem?')">
                <i class="fas fa-power-off"></i>
            </a>
        </div>
    </div>

    <div class="main-content">
        <?php if ($aksi == 'tampil'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-book text-success me-2"></i>Logbook Harian BK</h3>
                <p class="text-muted small">Catat agenda kegiatan, sasaran layanan, ringkasan, serta dokumentasi harian.</p>
            </div>
            <a href="logbook_harian.php?aksi=tambah" class="btn btn-success rounded-pill px-4 shadow-sm text-white">
                <i class="fas fa-plus me-1"></i> Tambah Logbook
            </a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Hari/Tanggal & Waktu</th>
                            <th>Jenis Kegiatan</th>
                            <th>Sasaran</th>
                            <th>Ringkasan Kegiatan</th>
                            <th>Dokumentasi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM tb_logbook ORDER BY tanggal_waktu DESC, id_logbook DESC");
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                                $hari_tanggal = date('d-m-Y H:i', strtotime($row['tanggal_waktu']));
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $hari_tanggal; ?></strong> WIB</td>
                            <td><?php echo $row['jenis_kegiatan']; ?></td>
                            <td>
                                <span class="badge <?php echo ($row['sasaran'] == 'Individu') ? 'bg-primary' : 'bg-info'; ?>">
                                    <?php echo $row['sasaran']; ?>
                                </span>
                            </td>
                            <td><?php echo mb_strimwidth($row['ringkasan'], 0, 50, "..."); ?></td>
                            <td>
                                <?php if (!empty($row['dokumentasi'])): ?>
                                    <a href="../assets/img/upload_logbook/<?php echo $row['dokumentasi']; ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-image me-1"></i> Lihat Foto
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted small">Tidak ada</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <a href="logbook_harian.php?aksi=edit&id=<?php echo $row['id_logbook']; ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="logbook_harian.php?aksi=hapus&id=<?php echo $row['id_logbook']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus logbook ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada catatan logbook harian.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
        <?php
        $tanggal_waktu = date('Y-m-d\TH:i');
        $jenis_kegiatan = $sasaran = $ringkasan = $dokumentasi = "";
        $judul_form = "Tambah Logbook Harian Baru";
        $teks_tombol = "Simpan Logbook";
        $warna_tombol = "btn-success";
        $form_action = "logbook_harian.php";

        if ($aksi == 'edit' && isset($_GET['id'])) {
            $id_logbook = $_GET['id'];
            $q_edit = mysqli_query($koneksi, "SELECT * FROM tb_logbook WHERE id_logbook='$id_logbook'");
            $d_edit = mysqli_fetch_assoc($q_edit);
            if ($d_edit) {
                $tanggal_waktu  = date('Y-m-d\TH:i', strtotime($d_edit['tanggal_waktu']));
                $jenis_kegiatan = $d_edit['jenis_kegiatan'];
                $sasaran        = $d_edit['sasaran'];
                $ringkasan      = $d_edit['ringkasan'];
                $dokumentasi    = $d_edit['dokumentasi'];
                
                $judul_form   = "Edit Logbook Harian";
                $teks_tombol  = "Perbarui Logbook";
                $warna_tombol = "btn-warning text-white";
                $form_action  = "logbook_harian.php?aksi=edit&id=" . $id_logbook;
            }
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4 p-md-5">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-book text-success me-2"></i><?php echo $judul_form; ?></h3>
                    
                    <form action="<?php echo $form_action; ?>" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="dokumen_lama" value="<?php echo $dokumentasi; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hari/Tanggal & Waktu</label>
                            <input type="datetime-local" name="tanggal_waktu" class="form-control" value="<?php echo $tanggal_waktu; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Kegiatan / Agenda Layanan</label>
                            <input type="text" name="jenis_kegiatan" class="form-control" value="<?php echo $jenis_kegiatan; ?>" placeholder="Contoh: Konseling Individu, Bimbingan Klasikal, Home Visit..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sasaran Kegiatan</label>
                            <select name="sasaran" class="form-select" required>
                                <option value="" disabled selected>-- Pilih Sasaran --</option>
                                <option value="Individu" <?php if($sasaran == 'Individu') echo 'selected'; ?>>Individu (Perorangan)</option>
                                <option value="Kelompok" <?php if($sasaran == 'Kelompok') echo 'selected'; ?>>Kelompok / Klasikal</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Lampiran Ringkasan Kegiatan</label>
                            <textarea name="ringkasan" class="form-control" rows="4" placeholder="Uraikan ringkasan hasil atau jalannya kegiatan bimbingan..." required><?php echo $ringkasan; ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Lampiran Dokumentasi (Foto/File)</label>
                            <?php if (!empty($dokumentasi)): ?>
                                <div class="mb-2">
                                    <small class="text-muted">File saat ini: <a href="../assets/img/upload_logbook/<?php echo $dokumentasi; ?>" target="_blank"><?php echo $dokumentasi; ?></a></small>
                                </div>
                            <?php endif; ?>
                            <input type="file" name="dokumentasi" class="form-control">
                            <small class="text-muted" style="font-size: 0.75rem;">Format yang didukung: JPG, PNG, JPEG (Opsional)</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="logbook_harian.php" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                            <button type="submit" name="simpan" class="btn <?php echo $warna_tombol; ?> px-4 rounded-pill shadow-sm">
                                <i class="fas fa-save me-1"></i> <?php echo $teks_tombol; ?>
                            </button>
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