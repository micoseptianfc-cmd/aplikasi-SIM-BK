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

// ==================== PROSES SIMPAN / UPDATE DATA ====================
if (isset($_POST['simpan'])) {
    $nisn         = $_POST['nisn'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $kelas        = $_POST['kelas'];
    $nama_ayah    = $_POST['nama_ayah'];
    $nama_ibu     = $_POST['nama_ibu'];
    $alamat       = $_POST['alamat'];
    $no_hp_ortu   = $_POST['no_hp_ortu'];

    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $id_siswa = $_GET['id'];
        $query = mysqli_query($koneksi, "UPDATE tb_siswa SET nisn='$nisn', nama_lengkap='$nama_lengkap', kelas='$kelas', nama_ayah='$nama_ayah', nama_ibu='$nama_ibu', alamat='$alamat', no_hp_ortu='$no_hp_ortu' WHERE id_siswa='$id_siswa'");
        $pesan = "Data siswa berhasil diperbarui!";
    } else {
        $query = mysqli_query($koneksi, "INSERT INTO tb_siswa (nisn, nama_lengkap, kelas, nama_ayah, nama_ibu, alamat, no_hp_ortu) VALUES ('$nisn', '$nama_lengkap', '$kelas', '$nama_ayah', '$nama_ibu', '$alamat', '$no_hp_ortu')");
        $pesan = "Data siswa berhasil ditambahkan!";
    }

    if ($query) {
        echo "<script>alert('$pesan'); window.location='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal memproses data.');</script>";
    }
}

// ==================== PROSES HAPUS DATA ====================
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id_siswa = $_GET['id'];
    $query_hapus = mysqli_query($koneksi, "DELETE FROM tb_siswa WHERE id_siswa='$id_siswa'");
    if ($query_hapus) {
        echo "<script>alert('Data siswa berhasil dihapus!'); window.location='data_siswa.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='data_siswa.php';</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Siswa - SIM-BK</title>
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

    <!-- Sidebar Kiri (Permanen) -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="fas fa-graduation-cap me-2 text-warning"></i>SIM-BK
        </div>
        
        <div class="sidebar-menu">
            <div class="text-uppercase text-white-50 small fw-bold px-3 mb-2" style="font-size: 0.75rem; letter-spacing: 1px;">Menu Utama</div>
            <a href="../index.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="data_siswa.php" class="active"><i class="fas fa-users"></i> Data Siswa</a>
            <a href="logbook_harian.php"><i class="fas fa-book"></i> Logbook Harian</a>
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

    <!-- Konten Utama -->
    <div class="main-content">
        <?php if ($aksi == 'tampil'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold text-dark mb-1"><i class="fas fa-users text-primary me-2"></i>Data Siswa Bimbingan</h3>
                <p class="text-muted small">Kelola data profil lengkap serta catatan pendampingan siswa.</p>
            </div>
            <a href="data_siswa.php?aksi=tambah" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-1"></i> Tambah Data Siswa
            </a>
        </div>

        <div class="card card-custom p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>No</th>
                            <th>NISN</th>
                            <th>Nama Lengkap</th>
                            <th>Kelas</th>
                            <th>Nama Ayah / Ibu</th>
                            <th>No. HP Ortu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = mysqli_query($koneksi, "SELECT * FROM tb_siswa ORDER BY id_siswa DESC");
                        if (mysqli_num_rows($query) > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td><strong><?php echo $row['nisn']; ?></strong></td>
                            <td><?php echo $row['nama_lengkap']; ?></td>
                            <td><span class="badge bg-secondary"><?php echo $row['kelas']; ?></span></td>
                            <td><?php echo $row['nama_ayah'] . ' / ' . $row['nama_ibu']; ?></td>
                            <td><?php echo $row['no_hp_ortu']; ?></td>
                            <td class="text-center">
                                <a href="data_siswa.php?aksi=edit&id=<?php echo $row['id_siswa']; ?>" class="btn btn-warning btn-sm text-white" title="Edit"><i class="fas fa-edit"></i></a>
                                <a href="data_siswa.php?aksi=hapus&id=<?php echo $row['id_siswa']; ?>" class="btn btn-danger btn-sm" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted py-4'>Belum ada data siswa yang dimasukkan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php elseif ($aksi == 'tambah' || $aksi == 'edit'): ?>
        <?php
        $nisn = $nama_lengkap = $kelas = $nama_ayah = $nama_ibu = $alamat = $no_hp_ortu = "";
        $judul_form = "Tambah Data Siswa Baru";
        $ikon_form = "fa-user-plus text-primary";
        $teks_tombol = "Simpan Data";
        $warna_tombol = "btn-primary";
        $form_action = "data_siswa.php";

        if ($aksi == 'edit' && isset($_GET['id'])) {
            $id_siswa = $_GET['id'];
            $q_edit = mysqli_query($koneksi, "SELECT * FROM tb_siswa WHERE id_siswa='$id_siswa'");
            $d_edit = mysqli_fetch_assoc($q_edit);
            if ($d_edit) {
                $nisn         = $d_edit['nisn'];
                $nama_lengkap = $d_edit['nama_lengkap'];
                $kelas        = $d_edit['kelas'];
                $nama_ayah    = $d_edit['nama_ayah'];
                $nama_ibu     = $d_edit['nama_ibu'];
                $alamat       = $d_edit['alamat'];
                $no_hp_ortu   = $d_edit['no_hp_ortu'];
                
                $judul_form   = "Edit Data Siswa";
                $ikon_form    = "fa-user-edit text-warning";
                $teks_tombol  = "Perbarui Data";
                $warna_tombol = "btn-warning text-white";
                $form_action  = "data_siswa.php?aksi=edit&id=" . $id_siswa;
            }
        }
        ?>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-custom p-4 p-md-5">
                    <h3 class="fw-bold text-dark mb-4"><i class="fas <?php echo $ikon_form; ?> me-2"></i><?php echo $judul_form; ?></h3>
                    
                    <form action="<?php echo $form_action; ?>" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">NISN</label>
                            <input type="text" name="nisn" class="form-control" value="<?php echo $nisn; ?>" placeholder="Masukkan NISN siswa..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap Siswa</label>
                            <input type="text" name="nama_lengkap" class="form-control" value="<?php echo $nama_lengkap; ?>" placeholder="Masukkan nama lengkap..." required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Kelas</label>
                            <input type="text" name="kelas" class="form-control" value="<?php echo $kelas; ?>" placeholder="Contoh: VII A, VIII B, dll..." required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Ayah</label>
                                <input type="text" name="nama_ayah" class="form-control" value="<?php echo $nama_ayah; ?>" placeholder="Nama ayah...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Nama Ibu</label>
                                <input type="text" name="nama_ibu" class="form-control" value="<?php echo $nama_ibu; ?>" placeholder="Nama ibu...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat tempat tinggal siswa..."><?php echo $alamat; ?></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">No. HP Orang Tua / Wali</label>
                            <input type="text" name="no_hp_ortu" class="form-control" value="<?php echo $no_hp_ortu; ?>" placeholder="Nomor WhatsApp/HP orang tua...">
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="data_siswa.php" class="btn btn-secondary px-4 rounded-pill">Batal</a>
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