<?php
// admin/cetak_pdf.php

// 1. Load Library Dompdf via Autoload Composer
require '../vendor/autoload.php';

// 2. Koneksi Database & Session
session_start();
include '../config/database.php';

// Cek Login (Bisa Admin atau Direksi)
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Gunakan Namespace Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// 3. Konfigurasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true); // Agar bisa baca gambar/logo jika ada
$dompdf = new Dompdf($options);

// 4. Ambil Data Laporan dari Database
$query = "SELECT l.*, u.nama_lengkap as nama_mandor 
          FROM laporan_keberangkatan l 
          JOIN users u ON l.user_id = u.id 
          ORDER BY l.tanggal_berangkat DESC";
$result = mysqli_query($conn, $query);

// 5. Susun Template HTML & CSS untuk PDF
// Kita pakai CSS manual agar rapi di PDF (Tailwind tidak support penuh di Dompdf)
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keberangkatan Bus</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 20px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #999; }
        th { background-color: #eee; padding: 8px; text-align: center; font-weight: bold; font-size: 11px; }
        td { padding: 6px; text-align: left; vertical-align: top; }
        .center { text-align: center; }
        .badge-verif { color: green; font-weight: bold; }
        .badge-pending { color: orange; font-weight: bold; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 10px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Sistem Manajemen Keberangkatan Bus</h1>
        <p>Jalan Raya Lintas Sumatera No. 123, Medan, Sumatera Utara</p>
        <p>Telp: (061) 1234567 | Email: admin@busman.com</p>
    </div>

    <h3 style="text-align:center;">Laporan Operasional Harian</h3>
    <p>Dicetak Tanggal: ' . date('d F Y, H:i') . ' WIB</p>
    <p>Oleh: ' . $_SESSION['nama_lengkap'] . ' (' . ucfirst($_SESSION['role']) . ')</p>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="15%">No. Polisi / Driver</th>
                <th width="20%">Tujuan</th>
                <th width="10%">Pnmbg</th>
                <th width="15%">Mandor</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>';

$no = 1;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status_label = ($row['status'] == 'terverifikasi') 
            ? '<span class="badge-verif">Verified</span>' 
            : '<span class="badge-pending">Pending</span>';

        $html .= '
            <tr>
                <td class="center">' . $no++ . '</td>
                <td>
                    <strong>' . date('d/m/Y', strtotime($row['tanggal_berangkat'])) . '</strong><br>
                    ' . substr($row['waktu_berangkat'], 0, 5) . ' WIB
                </td>
                <td>
                    ' . $row['no_polisi'] . '<br>
                    <small>(' . $row['nama_driver'] . ')</small>
                </td>
                <td>' . $row['tujuan'] . '</td>
                <td class="center">' . $row['jumlah_penumpang'] . ' Org</td>
                <td>' . $row['nama_mandor'] . '</td>
                <td class="center">' . $status_label . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="7" class="center">Tidak ada data laporan.</td></tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak otomatis oleh Sistem Manajemen Bus.</p>
    </div>

</body>
</html>';

// 6. Load HTML ke Dompdf
$dompdf->loadHtml($html);

// 7. Atur Ukuran Kertas (A4, Landscape agar muat tabel lebar)
$dompdf->setPaper('A4', 'landscape');

// 8. Render PDF (Proses Konversi)
$dompdf->render();

// 9. Output ke Browser (Stream)
// "Attachment" => 0 (Preview di browser), 1 (Langsung Download)
$dompdf->stream("Laporan_Bus_" . date('Y-m-d_H-i') . ".pdf", array("Attachment" => 0));

?>