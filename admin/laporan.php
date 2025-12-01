<?php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';
?>

<div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
    <div>
        <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Data Laporan Mandor</h3>
        <p class="text-slate-500 mt-1">Verifikasi dan kelola laporan operasional masuk.</p>
    </div>
    
    <div class="flex gap-3">
        <a href="cetak_pdf.php" target="_blank" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-rose-600 rounded-xl hover:bg-rose-700 hover:shadow-lg hover:shadow-rose-500/30 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
            <i class="fa-solid fa-file-pdf mr-2"></i> Export PDF
        </a>

        <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-bold text-slate-700 transition-all duration-200 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
            <i class="fa-solid fa-print mr-2"></i> Print
        </button>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-5 px-6"><i class="fa-regular fa-calendar-days mr-2"></i>Waktu & Pelapor</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-bus-simple mr-2"></i>Armada</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-route mr-2"></i>Rute & Pax</th>
                    <th class="py-5 px-6 text-center"><i class="fa-solid fa-camera mr-2"></i>Bukti</th>
                    <th class="py-5 px-6 text-center"><i class="fa-solid fa-circle-info mr-2"></i>Status</th>
                    <th class="py-5 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <?php
                // PERBAIKAN QUERY: 
                // Kita tambahkan JOIN ke kategori_lokasi dua kali (k_asal dan k_tujuan)
                // untuk mengambil nama lokasi berdasarkan ID yang tersimpan.
                $query = "SELECT l.*, 
                                 u.nama_lengkap as nama_mandor,
                                 k_asal.nama_lokasi as nama_asal,
                                 k_tujuan.nama_lokasi as nama_tujuan
                          FROM laporan_keberangkatan l 
                          JOIN users u ON l.user_id = u.id 
                          LEFT JOIN kategori_lokasi k_asal ON l.lokasi_id = k_asal.id
                          LEFT JOIN kategori_lokasi k_tujuan ON l.tujuan_id = k_tujuan.id
                          ORDER BY l.created_at DESC";
                
                $result = mysqli_query($conn, $query);

                if(mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)) : 
                ?>
                    <tr class="hover:bg-slate-50 transition-colors duration-200">
                        
                        <td class="py-5 px-6 align-top">
                            <div class="font-bold text-slate-800 text-base"><?= date('d M Y', strtotime($row['tanggal_berangkat'])) ?></div>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded">
                                    <?= substr($row['waktu_berangkat'], 0, 5) ?> WIB
                                </span>
                            </div>
                            <div class="mt-2 flex items-center text-xs text-slate-400">
                                <i class="fa-solid fa-user-tie mr-1"></i> <?= $row['nama_mandor'] ?>
                            </div>
                        </td>

                        <td class="py-5 px-6 align-top">
                            <div class="font-bold text-slate-800 text-base"><?= $row['no_polisi'] ?></div>
                            <div class="text-xs text-slate-500 mt-1 flex items-center">
                                <i class="fa-solid fa-id-card mr-1.5 text-slate-400"></i> <?= $row['nama_driver'] ?>
                            </div>
                        </td>

                        <td class="py-5 px-6 align-top">
                            <div class="flex flex-col gap-1 mb-2">
                                <div class="flex items-center text-xs text-slate-500">
                                    <i class="fa-regular fa-circle text-[10px] mr-2 text-blue-500"></i>
                                    <?= $row['nama_asal'] ?? '-' ?>
                                </div>
                                <div class="flex items-center font-bold text-slate-700">
                                    <i class="fa-solid fa-location-dot text-xs mr-2 text-red-500"></i>
                                    <?= $row['nama_tujuan'] ?? '-' ?>
                                </div>
                            </div>
                            
                            <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">
                                <i class="fa-solid fa-users text-[10px]"></i> <?= $row['jumlah_penumpang'] ?> Org
                            </span>
                        </td>

                        <td class="py-5 px-6 align-middle text-center">
                            <?php if(!empty($row['foto_dokumentasi'])): ?>
                                <button onclick="lihatFoto('<?= $base_url . 'assets/uploads/' . $row['foto_dokumentasi'] ?>')" 
                                    class="h-9 w-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition border border-blue-100" 
                                    title="Lihat Foto">
                                    <i class="fa-regular fa-image"></i>
                                </button>
                            <?php else: ?>
                                <span class="h-9 w-9 flex items-center justify-center text-slate-300">
                                    <i class="fa-solid fa-image-slash"></i>
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="py-5 px-6 align-middle text-center">
                            <?php if($row['status'] == 'terverifikasi'): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/20">
                                    <i class="fa-solid fa-check text-[10px]"></i> Verified
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/20 animate-pulse">
                                    <i class="fa-solid fa-hourglass-half text-[10px]"></i> Pending
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="py-5 px-6 align-middle text-center">
                            <div class="flex item-center justify-center gap-2">
                                
                                <?php if($row['status'] == 'pending'): ?>
                                <a href="proses_laporan.php?act=verifikasi&id=<?= $row['id'] ?>" 
                                   class="h-9 w-9 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 shadow-md shadow-blue-500/30 transition transform hover:-translate-y-0.5" 
                                   title="Verifikasi Laporan">
                                    <i class="fa-solid fa-check"></i>
                                </a>
                                <?php endif; ?>

                                <button onclick="konfirmasiHapus(<?= $row['id'] ?>)" 
                                    class="h-9 w-9 rounded-full bg-white text-rose-500 border border-rose-200 flex items-center justify-center hover:bg-rose-50 hover:border-rose-300 transition" 
                                    title="Hapus Data">
                                    <i class="fa-solid fa-trash text-sm"></i>
                                </button>

                            </div>
                        </td>
                    </tr>
                <?php 
                    endwhile; 
                } else {
                    echo "<tr><td colspan='6' class='text-center py-12 text-slate-400 italic bg-white'>Tidak ada data laporan.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function lihatFoto(url) {
        Swal.fire({
            imageUrl: url,
            imageHeight: 400,
            imageAlt: 'Dokumentasi',
            showCloseButton: true,
            showConfirmButton: false,
            backdrop: `rgba(15, 23, 42, 0.6)`,
            customClass: { popup: 'rounded-2xl' }
        });
    }

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Laporan?',
            text: "Data yang dihapus tidak bisa dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-2xl',
                confirmButton: 'rounded-xl',
                cancelButton: 'rounded-xl'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "proses_laporan.php?act=hapus&id=" + id;
            }
        })
    }
</script>

<?php include '../layouts/footer.php'; ?>