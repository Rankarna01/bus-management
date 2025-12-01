<?php
// mandor/proses_lokasi.php
session_start();
include '../config/database.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'mandor') { exit; }

$act = $_REQUEST['act'];

if ($act == 'tambah') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama_lokasi']);
    mysqli_query($conn, "INSERT INTO kategori_lokasi (nama_lokasi) VALUES ('$nama')");
    $_SESSION['success'] = "Lokasi berhasil ditambahkan";
} 
elseif ($act == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($conn, "DELETE FROM kategori_lokasi WHERE id='$id'");
    $_SESSION['success'] = "Lokasi berhasil dihapus";
}

header("Location: lokasi.php");
?>