<?php
// admin/proses_laporan.php
session_start();
include '../config/database.php';

// Cek apakah user adalah admin
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'] ?? null;
$act = $_GET['act'] ?? null;

if ($id && $act) {
    if ($act == 'verifikasi') {
        // Update status jadi terverifikasi
        $query = "UPDATE laporan_keberangkatan SET status = 'terverifikasi' WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['success'] = "Laporan berhasil diverifikasi!";
        } else {
            $_SESSION['error'] = "Gagal memverifikasi laporan.";
        }
    } 
    elseif ($act == 'hapus') {
        // Ambil info file gambar dulu untuk dihapus dari folder
        $cek = mysqli_query($conn, "SELECT foto_dokumentasi FROM laporan_keberangkatan WHERE id = '$id'");
        $data = mysqli_fetch_assoc($cek);
        
        // Hapus data dari DB
        $query = "DELETE FROM laporan_keberangkatan WHERE id = '$id'";
        if (mysqli_query($conn, $query)) {
            // Jika ada fotonya, hapus fisik filenya
            if(!empty($data['foto_dokumentasi'])){
                $file_path = "../assets/uploads/" . $data['foto_dokumentasi'];
                if(file_exists($file_path)){
                    unlink($file_path);
                }
            }
            $_SESSION['success'] = "Data laporan berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus data.";
        }
    }
}

// Redirect kembali ke halaman laporan
header("Location: laporan.php");
exit;
?>