<?php
// mandor/proses_laporan.php
session_start();
include '../config/database.php';

// --- SECURITY CHECK ---
// Pastikan user sudah login DAN role-nya adalah mandor
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'mandor') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil variable aksi
$act = $_POST['act'] ?? $_GET['act'] ?? '';
$user_id = $_SESSION['user_id']; // ID User diambil dari session login

// ==========================================================
// 1. PROSES TAMBAH LAPORAN BARU
// ==========================================================
if ($act == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitasi Input (Mencegah SQL Injection sederhana)
    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $waktu      = mysqli_real_escape_string($conn, $_POST['waktu']);
    $no_polisi  = strtoupper(mysqli_real_escape_string($conn, $_POST['no_polisi'])); // Huruf Besar
    $driver     = mysqli_real_escape_string($conn, $_POST['driver']);
    $jumlah     = (int) $_POST['jumlah'];
    $tujuan     = mysqli_real_escape_string($conn, $_POST['tujuan']);
    
    $nama_foto_baru = null;

    // --- LOGIKA UPLOAD FOTO ---
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $filename   = $_FILES['foto']['name'];
        $filesize   = $_FILES['foto']['size'];
        $tmp_name   = $_FILES['foto']['tmp_name'];
        
        // Cek Ekstensi
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed_ext)) {
            // Cek Ukuran (2MB = 2097152 Bytes)
            if ($filesize < 2097152) {
                // Generate Nama Unik (biar tidak bentrok)
                $nama_foto_baru = uniqid() . '_' . time() . '.' . $ext;
                
                // Pastikan folder upload ada!
                $target_dir = '../assets/uploads/';
                
                // Pindahkan file
                if (!move_uploaded_file($tmp_name, $target_dir . $nama_foto_baru)) {
                    $_SESSION['error'] = "Gagal mengupload gambar ke server.";
                    header("Location: riwayat.php");
                    exit;
                }
            } else {
                $_SESSION['error'] = "Ukuran foto terlalu besar! Maksimal 2MB.";
                header("Location: riwayat.php");
                exit;
            }
        } else {
            $_SESSION['error'] = "Format file salah! Harap upload JPG atau PNG.";
            header("Location: riwayat.php");
            exit;
        }
    }

    // --- QUERY INSERT DATABASE ---
    $query = "INSERT INTO laporan_keberangkatan 
              (user_id, tanggal_berangkat, waktu_berangkat, no_polisi, nama_driver, jumlah_penumpang, tujuan, foto_dokumentasi, status) 
              VALUES 
              ('$user_id', '$tanggal', '$waktu', '$no_polisi', '$driver', '$jumlah', '$tujuan', '$nama_foto_baru', 'pending')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Laporan keberangkatan berhasil dikirim!";
    } else {
        $_SESSION['error'] = "Database Error: " . mysqli_error($conn);
    }
    
    header("Location: riwayat.php");
    exit;
}

// ==========================================================
// 2. PROSES HAPUS / BATALKAN LAPORAN
// ==========================================================
elseif ($act == 'hapus' && isset($_GET['id'])) {
    $id_laporan = $_GET['id'];

    // Validasi Ketat:
    // 1. Cek ID Laporan
    // 2. Cek apakah milik user yang login (user_id) -> Agar mandor A tidak hapus punya mandor B
    // 3. Cek apakah status masih 'pending' -> Jika sudah Verified, tidak boleh dihapus mandor
    $query_cek = "SELECT * FROM laporan_keberangkatan WHERE id='$id_laporan' AND user_id='$user_id' AND status='pending'";
    $result_cek = mysqli_query($conn, $query_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        // Ambil data untuk hapus foto
        $data = mysqli_fetch_assoc($result_cek);
        $foto = $data['foto_dokumentasi'];

        // Hapus Row Database
        $query_del = "DELETE FROM laporan_keberangkatan WHERE id='$id_laporan'";
        
        if (mysqli_query($conn, $query_del)) {
            // Jika sukses hapus DB, hapus juga file fotonya jika ada
            if ($foto != null) {
                $path_foto = '../assets/uploads/' . $foto;
                if (file_exists($path_foto)) {
                    unlink($path_foto); // Hapus file fisik
                }
            }
            $_SESSION['success'] = "Laporan berhasil dibatalkan.";
        } else {
            $_SESSION['error'] = "Gagal menghapus data.";
        }
    } else {
        $_SESSION['error'] = "Akses Ditolak! Laporan tidak ditemukan atau sudah diverifikasi admin.";
    }

    header("Location: riwayat.php");
    exit;
}

// Jika akses langsung tanpa parameter
else {
    header("Location: riwayat.php");
    exit;
}
?>