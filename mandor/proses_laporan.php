<?php
// mandor/proses_laporan.php
session_start();
include '../config/database.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'mandor') {
    header("Location: ../auth/login.php");
    exit;
}

$act = $_POST['act'] ?? $_GET['act'] ?? '';
$user_id = $_SESSION['user_id'];

// ==========================================================
// 1. PROSES TAMBAH LAPORAN BARU
// ==========================================================
if ($act == 'tambah' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil Input
    $lokasi_id  = mysqli_real_escape_string($conn, $_POST['lokasi_id']); // Asal
    $tujuan_id  = mysqli_real_escape_string($conn, $_POST['tujuan_id']); // Tujuan (Baru)
    
    // Validasi Logika: Asal & Tujuan tidak boleh sama
    if($lokasi_id == $tujuan_id){
        $_SESSION['error'] = "Lokasi Asal dan Tujuan tidak boleh sama!";
        header("Location: riwayat.php");
        exit;
    }

    $tanggal    = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $waktu      = mysqli_real_escape_string($conn, $_POST['waktu']);
    $no_polisi  = strtoupper(mysqli_real_escape_string($conn, $_POST['no_polisi']));
    $driver     = mysqli_real_escape_string($conn, $_POST['driver']);
    $jumlah     = (int) $_POST['jumlah'];
    
    $nama_foto_baru = null;

    // --- LOGIKA UPLOAD FOTO (Sama seperti sebelumnya) ---
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $filename   = $_FILES['foto']['name'];
        $filesize   = $_FILES['foto']['size'];
        $tmp_name   = $_FILES['foto']['tmp_name'];
        $ext        = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed    = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed)) {
            if ($filesize < 2097152) {
                $nama_foto_baru = uniqid() . '_' . time() . '.' . $ext;
                if (!move_uploaded_file($tmp_name, '../assets/uploads/' . $nama_foto_baru)) {
                    $_SESSION['error'] = "Gagal upload gambar."; header("Location: riwayat.php"); exit;
                }
            } else {
                $_SESSION['error'] = "File terlalu besar (Max 2MB)."; header("Location: riwayat.php"); exit;
            }
        } else {
            $_SESSION['error'] = "Format file salah."; header("Location: riwayat.php"); exit;
        }
    }

    // --- QUERY INSERT (Updated: tujuan_id) ---
    $query = "INSERT INTO laporan_keberangkatan 
              (user_id, lokasi_id, tujuan_id, tanggal_berangkat, waktu_berangkat, no_polisi, nama_driver, jumlah_penumpang, foto_dokumentasi, status) 
              VALUES 
              ('$user_id', '$lokasi_id', '$tujuan_id', '$tanggal', '$waktu', '$no_polisi', '$driver', '$jumlah', '$nama_foto_baru', 'pending')";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Laporan berhasil dikirim!";
    } else {
        $_SESSION['error'] = "Database Error: " . mysqli_error($conn);
    }
    
    header("Location: riwayat.php");
    exit;
}

// ==========================================================
// 2. PROSES HAPUS
// ==========================================================
elseif ($act == 'hapus' && isset($_GET['id'])) {
    $id_laporan = $_GET['id'];
    $query_cek = "SELECT * FROM laporan_keberangkatan WHERE id='$id_laporan' AND user_id='$user_id' AND status='pending'";
    $result_cek = mysqli_query($conn, $query_cek);

    if (mysqli_num_rows($result_cek) > 0) {
        $data = mysqli_fetch_assoc($result_cek);
        if ($data['foto_dokumentasi'] != null) {
            $path = '../assets/uploads/' . $data['foto_dokumentasi'];
            if (file_exists($path)) unlink($path);
        }
        mysqli_query($conn, "DELETE FROM laporan_keberangkatan WHERE id='$id_laporan'");
        $_SESSION['success'] = "Laporan berhasil dibatalkan.";
    } else {
        $_SESSION['error'] = "Gagal menghapus data.";
    }
    header("Location: riwayat.php");
    exit;
}
else {
    header("Location: riwayat.php");
    exit;
}
?>