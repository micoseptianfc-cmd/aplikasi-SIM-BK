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

// ==================== PROSES SIMPAN / UPDATE KONSELING ====================
if (isset($_POST['simpan'])) {
    $tanggal               = $_POST['tanggal'];
    $jenis_layanan         = $_POST['jenis_layanan'];
    $id_siswa              = $_POST['id_siswa'];
    $masalah_utama         = $_POST['masalah_utama'];
    $teknik_konseling      = $_POST['teknik_konseling'];
    $hasil_konseling       = $_POST['hasil_konseling'];
    $rencana_tindak_lanjut = $_POST['rencana_tindak_lanjut'];
    $tingkat_kerahasiaan   = $_POST['tingkat_kerahasiaan'];

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id_konseling = $_GET['id'];
        $query = mysqli_query($koneksi, "UPDATE tb_konseling SET tanggal='$tanggal', jenis_layanan='$jenis_layanan', id_siswa='$id_siswa', masalah_utama='$masalah_utama', teknik_konseling='$teknik_konseling', hasil_konseling='$hasil_konseling', rencana_tindak_lanjut='$rencana_tindak_lanjut', tingkat_kerahasiaan='$tingkat_kerahasiaan' WHERE id_konseling='$id_konseling'");
        $pesan = "Data konseling berhasil diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_konseling (tanggal, jenis_layanan, id_siswa, masalah_utama, teknik_konseling, hasil_konseling, rencana_tindak_lanjut, tingkat_kerahasiaan) VALUES ('$tanggal', '$jenis_layanan', '$id_siswa', '$masalah_utama', '$teknik_konseling', '$hasil_konseling', '$rencana_tindak_lanjut', '$tingkat_kerahasiaan')");
        $pesan = "Data konseling berhasil ditambahkan!";
    }

    if ($query) {
        echo "<script>alert('$pesan'); window.location='konseling.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses data.');</script>";
    }
}

// ==================== PROSES HAPUS KONSELING ====================
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id_konseling = $_GET['id'];
    $query_hapus = mysqli_query($koneksi, "DELETE FROM tb_konseling WHERE id_konseling='$id_konseling'");
    if ($query_hapus) {
        echo "<script>alert('Data konseling berhasil dihapus!'); window.location='konseling.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='konseling.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konseling Siswa - SIM-BK</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Select2 CSS untuk Live Search Siswa -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <!-- Google Fonts -->
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

    <!-- Sidebar Kiri (Permanen) -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2 text-warning"></i>SIM-BK
        </div>
        
        <div class="sidebar-menu">
            <div class="text-uppercase text-white-50 small fw-bold px-3 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</div>
            <a href="../index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="data_siswa.php"><i class="fas fa-users"></i> Data Siswa</a>
            <a href="logbook_harian.php"><i class="fas fa-book"></i> Logbook Harian</a>
            <a href="konseling.php" class="active"><i class="fas fa-comments"></i> Konseling Siswa</a>
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

    <!-- Konten Utama -->
    <div class="main-content">
        <?php if ($aksi == 'tampil'): ?>
        <!-- ==================== TABEL KONSELING ==================== -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-comments text-info me-2"></i>Konseling Siswa</h3>
                <p class="text-muted small">Kelola data sesi konsultasi, pendekatan, hasil, dan tingkat kerahasiaan data siswa.</p>
            </div>
            <a href="konseling.php?aksi=tambah" class="btn btn-info rounded-pill px-4 shadow-sm text-white">
                <i class="fas fa-plus me-1"></i> Tambah Sesi Konseling
            </a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Layanan & Siswa (NISN)</th>
                            <th>Masalah Utama</th>
                            <th>Teknik / Solusi</th>
                            <th>Kerahasiaan</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT tb_konseling.*, tb_siswa.nama_lengkap, tb_siswa.nisn, tb_siswa.kelas FROM tb_konseling JOIN tb_siswa ON tb_konseling.id_siswa = tb_siswa.id_siswa ORDER BY tb_konseling.tanggal DESC, tb_konseling.id_konseling DESC");
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo date('d-m-Y', strtotime($row['tanggal'])); ?></strong></td>
                            <td>
                                <span class="badge <?php echo ($row['jenis_layanan'] == 'Individu') ? 'bg-primary' : 'bg-success'; ?> mb-1">
                                    <?php echo $row['jenis_layanan']; ?>
                                </span><br>
                                <strong><?php echo $row['nama_lengkap']; ?></strong><br>
                                <small class="text-muted">NISN: <?php echo $row['nisn']; ?> (<?php echo $row['kelas']; ?>)</small>
                            </td>
                            <td><?php echo mb_strimwidth($row['masalah_utama'], 0, 45, "..."); ?></td>
                            <td>
                                <small class="fw-bold text-primary">Teknik: <?php echo $row['teknik_konseling']; ?></small><br>
                                <span><?php echo mb_strimwidth($row['hasil_konseling'], 0, 40, "..."); ?></span>
                            </td>
                            <td>
                                <span class="badge bg-dark">
                                    <i class="fas fa-lock me-1 text-warning"></i><?php echo $row['tingkat_kerahasiaan']; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="konseling.php?aksi=edit&id=<?php echo $row['id_konseling']; ?>" class="btn btn-warning btn-sm text-white mb-1" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="konseling.php?aksi=hapus&id=<?php echo $row['id_konseling']; ?>" class="btn btn-danger btn-sm mb-1" title="Hapus" onclick="return confirm('Yakin ingin menghapus data konseling ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada data sesi konseling siswa.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
        <!-- ==================== FORM TAMBAH / EDIT KONSELING ==================== -->
        <?php
        $tanggal = date('Y-m-d');
        $jenis_layanan = "Individu";
        $id_siswa_pilih = "";
        $masalah_utama = $teknik_konseling = $hasil_konseling = $rencana_tindak_lanjut = "";
        $tingkat_kerahasiaan = "Rahasia (R1)";
        
        $judul_form = "Tambah Sesi Konseling Baru";
        $teks_tombol = "Simpan Konseling";
        $warna_tombol = "btn-info text-white";
        $form_action = "konseling.php";

        if ($aksi == 'edit' && isset($_GET['id'])) {
            $id_konseling = $_GET['id'];
            $q_edit = mysqli_query($koneksi, "SELECT * FROM tb_konseling WHERE id_konseling='$id_konseling'");
            $d_edit = mysqli_fetch_assoc($q_edit);
            if ($d_edit) {
                $tanggal               = $d_edit['tanggal'];
                $jenis_layanan         = $d_edit['jenis_layanan'];
                $id_siswa_pilih        = $d_edit['id_siswa'];
                $masalah_utama         = $d_edit['masalah_utama'];
                $teknik_konseling      = $d_edit['teknik_konseling'];
                $hasil_konseling       = $d_edit['hasil_konseling'];
                $rencana_tindak_lanjut = $d_edit['rencana_tindak_lanjut'];
                $tingkat_kerahasiaan   = $d_edit['tingkat_kerahasiaan'];
                
                $judul_form   = "Edit Sesi Konseling";
                $teks_tombol  = "Perbarui Konseling";
                $warna_tombol = "btn-warning text-white";
                $form_action  = "konseling.php?aksi=edit&id=" . $id_konseling;
            }
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4 p-md-5">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas fa-comments text-info me-2"></i><?php echo $judul_form; ?></h3>
                    
                    <form action="<?php echo $form_action; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal Konseling</label>
                            <input type="date" name="tanggal" class="form-control" value="<?php echo $tanggal; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jenis Layanan</label>
                            <select name="jenis_layanan" class="form-select" required>
                                <option value="Individu" <?php if($jenis_layanan == 'Individu') echo 'selected'; ?>>Konseling Individu</option>
                                <option value="Kelompok" <?php if($jenis_layanan == 'Kelompok') echo 'selected'; ?>>Konseling Kelompok</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih / Cari Siswa (Ketik Nama atau NISN)</label>
                            <select name="id_siswa" id="search-siswa" class="form-select" required>
                                <option value="" disabled selected>-- Ketik nama atau NISN siswa di sini --</option>
                                <?php
                                $q_siswa = mysqli_query($koneksi, "SELECT * FROM tb_siswa ORDER BY nama_lengkap ASC");
                                while ($s = mysqli_fetch_assoc($q_siswa)) {
                                    $selected = ($id_siswa_pilih == $s['id_siswa']) ? 'selected' : '';
                                    echo "<option value='".$s['id_siswa']."' ".$selected.">".$s['nama_lengkap']." (NISN: ".$s['nisn']." - Kelas: ".$s['kelas'].")</option>";
                                }
                                ?>
                            </select>
                            <small class="text-muted">Ketik 1 atau beberapa huruf/angka untuk memunculkan daftar secara spesifik.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Masalah Utama</label>
                            <textarea name="masalah_utama" class="form-control" rows="3" placeholder="Uraikan inti permasalahan yang sedang dihadapi siswa..." required><?php echo $masalah_utama; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Teknik / Pendekatan Konseling</label>
                            <input type="text" name="teknik_konseling" class="form-control" value="<?php echo $teknik_konseling; ?>" placeholder="Contoh: Behavioral, Reality Therapy, Konseling suportif..." required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Hasil Konseling</label>
                            <textarea name="hasil_konseling" class="form-control" rows="3" placeholder="Uraikan hasil atau kesimpulan dari proses konseling..." required><?php echo $hasil_konseling; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Rencana Tindak Lanjut</label>
                            <textarea name="rencana_tindak_lanjut" class="form-control" rows="2" placeholder="Langkah lanjutan yang akan dilakukan (misal: pemantauan, home visit, dll)..."><?php echo $rencana_tindak_lanjut; ?></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Tingkat Kerahasiaan Data (Enkripsi / Proteksi Privasi)</label>
                            <select name="tingkat_kerahasiaan" class="form-select" required>
                                <option value="Rahasia Sekali (R2 - Enkripsi Tinggi)" <?php if($tingkat_kerahasiaan == 'Rahasia Sekali (R2 - Enkripsi Tinggi)') echo 'selected'; ?>>Rahasia Sekali (R2 - Enkripsi Tinggi)</option>
                                <option value="Rahasia (R1 - Standar BK)" <?php if($tingkat_kerahasiaan == 'Rahasia (R1 - Standar BK)') echo 'selected'; ?>>Rahasia (R1 - Standar BK)</option>
                                <option value="Terbuka / Terbatas" <?php if($tingkat_kerahasiaan == 'Terbuka / Terbatas') echo 'selected'; ?>>Terbuka / Terbatas untuk Guru Mapel</option>
                            </select>
                            <small class="text-muted">Fitur pendukung kode etik bimbingan konseling dan keamanan data sistem.</small>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="konseling.php" class="btn btn-secondary px-4 rounded-pill">Batal</a>
                            <button type="submit" name="simpan" class="btn <?php echo $warna_tombol; ?> px-4 shadow-sm">
                                <i class="fas fa-save me-1"></i> <?php echo $teks_tombol; ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery (Dibutuhkan oleh Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 JS untuk Live Search -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // Mengaktifkan fitur live search Select2 pada select siswa
            $('#search-siswa').select2({
                theme: 'bootstrap-5',
                placeholder: "-- Ketik nama atau NISN siswa di sini --",
                allowClear: true
            });
        });
    </script>
    <script src="../assets/js/print-pdf.js"></script>
</body>
</html>