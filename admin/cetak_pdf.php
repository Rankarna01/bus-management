<?php
// admin/cetak_pdf.php

// 1. Load Library Dompdf via Autoload Composer
require '../vendor/autoload.php';

// 2. Koneksi Database & Session
session_start();
include '../config/database.php';

// Cek Login
if (!isset($_SESSION['login'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Gunakan Namespace Dompdf
use Dompdf\Dompdf;
use Dompdf\Options;

// 3. Konfigurasi Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true); 
$dompdf = new Dompdf($options);

// 4. Ambil Data Laporan (UPDATED QUERY dengan DOUBLE JOIN LOKASI)
$query = "SELECT l.*, 
                 u.nama_lengkap as nama_mandor,
                 k_asal.nama_lokasi as nama_asal,
                 k_tujuan.nama_lokasi as nama_tujuan
          FROM laporan_keberangkatan l 
          JOIN users u ON l.user_id = u.id 
          LEFT JOIN kategori_lokasi k_asal ON l.lokasi_id = k_asal.id
          LEFT JOIN kategori_lokasi k_tujuan ON l.tujuan_id = k_tujuan.id
          ORDER BY l.tanggal_berangkat DESC";

$result = mysqli_query($conn, $query);

// 5. Susun Template HTML & CSS
$html = '
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keberangkatan Bus</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 2px 0; font-size: 10px; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table, th, td { border: 1px solid #999; }
        th { background-color: #eee; padding: 8px; text-align: center; font-weight: bold; font-size: 10px; text-transform: uppercase; }
        td { padding: 6px; text-align: left; vertical-align: top; }
        
        .center { text-align: center; }
        .badge-verif { color: green; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .badge-pending { color: orange; font-weight: bold; text-transform: uppercase; font-size: 9px; }
        .rute-arrow { color: #666; font-weight: bold; }
        
        .footer { margin-top: 30px; text-align: right; font-size: 9px; font-style: italic; border-top: 1px solid #eee; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <h1>Sistem Manajemen Keberangkatan Bus</h1>
        <p>Laporan Operasional Armada & Perjalanan</p>
    </div>

    <div style="margin-bottom: 15px;">
        <strong>Dicetak Oleh:</strong> ' . $_SESSION['nama_lengkap'] . ' (' . ucfirst($_SESSION['role']) . ')<br>
        <strong>Tanggal Cetak:</strong> ' . date('d F Y, H:i') . ' WIB
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="15%">Waktu</th>
                <th width="15%">Armada</th>
                <th width="30%">Rute Perjalanan</th>
                <th width="10%">Pax</th>
                <th width="15%">Mandor</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>';

$no = 1;
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Logika Badge Status
        $status_label = ($row['status'] == 'terverifikasi') 
            ? '<span class="badge-verif">Verified</span>' 
            : '<span class="badge-pending">Pending</span>';

        // Logika Nama Lokasi (Handle jika null)
        $asal   = $row['nama_asal'] ?? '-';
        $tujuan = $row['nama_tujuan'] ?? '-';

        $html .= '
            <tr>
                <td class="center">' . $no++ . '</td>
                <td>
                    <strong>' . date('d/m/Y', strtotime($row['tanggal_berangkat'])) . '</strong><br>
                    ' . substr($row['waktu_berangkat'], 0, 5) . ' WIB
                </td>
                <td>
                    <strong>' . $row['no_polisi'] . '</strong><br>
                    <span style="font-size:9px; color:#555;">' . $row['nama_driver'] . '</span>
                </td>
                <td>
                    ' . $asal . ' <br>
                    <span class="rute-arrow">⬇ ke ⬇</span> <br>
                    <strong>' . $tujuan . '</strong>
                </td>
                <td class="center">' . $row['jumlah_penumpang'] . '</td>
                <td>' . $row['nama_mandor'] . '</td>
                <td class="center">' . $status_label . '</td>
            </tr>';
    }
} else {
    $html .= '<tr><td colspan="7" class="center">Tidak ada data laporan periode ini.</td></tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Bus Management v1.0
    </div>

</body>
</html>';

// 6. Load HTML ke Dompdf
$dompdf->loadHtml($html);

// 7. Atur Ukuran Kertas (A4, Landscape agar muat kolom rute yang lebar)
$dompdf->setPaper('A4', 'landscape');

// 8. Render PDF
$dompdf->render();

// 9. Output Stream
$dompdf->stream("Laporan_Bus_" . date('Y-m-d_H-i') . ".pdf", array("Attachment" => 0));

?>