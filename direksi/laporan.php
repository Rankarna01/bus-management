<?php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';
?>

<div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
    <div>
        <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Arsip Laporan</h3>
        <p class="text-slate-500 mt-1">Data lengkap operasional armada bus untuk monitoring.</p>
    </div>
    
    <div class="flex gap-3">
        <a href="export_excel.php" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-emerald-600 rounded-xl hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/30 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2">
            <i class="fa-solid fa-file-excel mr-2"></i> Export Excel
        </a>

        <a href="../admin/cetak_pdf.php" target="_blank" class="inline-flex items-center justify-center px-5 py-2.5 text-sm font-bold text-white transition-all duration-200 bg-rose-600 rounded-xl hover:bg-rose-700 hover:shadow-lg hover:shadow-rose-500/30 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
            <i class="fa-solid fa-file-pdf mr-2"></i> Export PDF
        </a>
    </div>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden mb-10">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-5 px-6"><i class="fa-regular fa-calendar-days mr-2"></i>Waktu</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-bus-simple mr-2"></i>Armada</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-route mr-2"></i>Rute & Pax</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-user-tie mr-2"></i>Mandor</th>
                    <th class="py-5 px-6 text-center"><i class="fa-solid fa-circle-info mr-2"></i>Status</th>
                    <th class="py-5 px-6 text-center">Bukti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <?php
                // Query Join untuk mengambil nama Mandor juga
                $query = "SELECT l.*, u.nama_lengkap as nama_mandor 
                          FROM laporan_keberangkatan l 
                          JOIN users u ON l.user_id = u.id 
                          ORDER BY l.tanggal_berangkat DESC, l.waktu_berangkat DESC";
                
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                            
                            <td class="py-5 px-6 align-top">
                                <div class="font-bold text-slate-800 text-base"><?= date('d M Y', strtotime($row['tanggal_berangkat'])) ?></div>
                                <div class="mt-1">
                                    <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-2 py-0.5 rounded border border-blue-100">
                                        <?= substr($row['waktu_berangkat'], 0, 5) ?> WIB
                                    </span>
                                </div>
                            </td>

                            <td class="py-5 px-6 align-top">
                                <div class="font-bold text-slate-800 text-base"><?= $row['no_polisi'] ?></div>
                                <div class="text-xs text-slate-500 mt-1 flex items-center">
                                    <i class="fa-solid fa-id-card mr-1.5 text-slate-400"></i> <?= $row['nama_driver'] ?>
                                </div>
                            </td>

                            <td class="py-5 px-6 align-top">
                                <div class="font-medium text-slate-700 mb-1"><?= $row['tujuan'] ?></div>
                                <span class="inline-flex items-center gap-1 bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">
                                    <i class="fa-solid fa-users text-[10px]"></i> <?= $row['jumlah_penumpang'] ?> Org
                                </span>
                            </td>

                            <td class="py-5 px-6 align-top">
                                <span class="bg-slate-100 px-2.5 py-1 rounded-lg text-slate-600 text-xs font-medium border border-slate-200">
                                    <?= $row['nama_mandor'] ?>
                                </span>
                            </td>

                            <td class="py-5 px-6 align-middle text-center">
                                <?php if ($row['status'] == 'terverifikasi'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/20">
                                        <i class="fa-solid fa-check text-[10px]"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/20">
                                        <i class="fa-solid fa-hourglass-half text-[10px]"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="py-5 px-6 align-middle text-center">
                                <?php if (!empty($row['foto_dokumentasi'])): ?>
                                    <button onclick="lihatFoto('<?= $base_url ?>assets/uploads/<?= $row['foto_dokumentasi'] ?>')" 
                                        class="h-9 w-9 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition border border-blue-100" 
                                        title="Lihat Dokumentasi">
                                        <i class="fa-regular fa-image"></i>
                                    </button>
                                <?php else: ?>
                                    <span class="h-9 w-9 flex items-center justify-center text-slate-300">
                                        <i class="fa-solid fa-image-slash"></i>
                                    </span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='7' class='text-center py-12 text-slate-400 italic'>Belum ada data laporan arsip.</td></tr>";
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
            customClass: {
                popup: 'rounded-2xl'
            }
        });
    }
</script>

<?php include '../layouts/footer.php'; ?>