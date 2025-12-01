<?php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';

$user_id = $_SESSION['user_id'];

// Ambil data lokasi untuk Dropdown (Biar efisien dipanggil sekali saja)
$q_lokasi = mysqli_query($conn, "SELECT * FROM kategori_lokasi ORDER BY nama_lokasi ASC");
$data_lokasi = [];
while ($row = mysqli_fetch_assoc($q_lokasi)) {
    $data_lokasi[] = $row;
}
?>

<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-6 gap-4">
    <div>
        <h3 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">Laporan Operasional</h3>
        <p class="text-slate-500 mt-1 text-sm">Monitor riwayat dan input keberangkatan.</p>
    </div>
    
    <button onclick="toggleModal('modalInput')" class="w-full md:w-auto group relative inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
        <i class="fa-solid fa-plus mr-2 transition-transform group-hover:rotate-90"></i>
        Buat Laporan Baru
    </button>
</div>

<div class="bg-transparent md:bg-white md:border md:border-slate-200 md:rounded-2xl md:shadow-sm overflow-hidden">
    <table class="w-full text-left border-collapse block md:table">
        <thead class="hidden md:table-header-group bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
            <tr>
                <th class="py-5 px-6">Waktu</th>
                <th class="py-5 px-6">Rute Perjalanan</th>
                <th class="py-5 px-6">Armada</th>
                <th class="py-5 px-6 text-center">Pax</th>
                <th class="py-5 px-6 text-center">Status</th>
                <th class="py-5 px-6 text-center">Aksi</th>
            </tr>
        </thead>

        <tbody class="block md:table-row-group text-sm text-slate-600">
            <?php
            // QUERY DOUBLE JOIN (Join Asal & Join Tujuan)
            $query = "SELECT l.*, 
                             k_asal.nama_lokasi AS nama_asal, 
                             k_tujuan.nama_lokasi AS nama_tujuan
                      FROM laporan_keberangkatan l 
                      LEFT JOIN kategori_lokasi k_asal ON l.lokasi_id = k_asal.id
                      LEFT JOIN kategori_lokasi k_tujuan ON l.tujuan_id = k_tujuan.id
                      WHERE l.user_id = '$user_id' 
                      ORDER BY l.created_at DESC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) : ?>
                    
                    <tr class="block md:table-row bg-white rounded-xl shadow-sm border border-slate-200 mb-4 md:mb-0 md:border-none md:shadow-none hover:bg-slate-50 transition-colors">
                        
                        <td class="block md:table-cell px-5 py-3 md:py-5 md:px-6 border-b md:border-b-slate-100 last:border-0 relative">
                            <span class="md:hidden text-xs font-bold text-slate-400 uppercase mb-1 block">Waktu</span>
                            <div class="flex items-center justify-between md:block">
                                <div>
                                    <div class="font-bold text-slate-800 text-base"><?= date('d M Y', strtotime($row['tanggal_berangkat'])) ?></div>
                                    <div class="mt-1 text-xs text-blue-600 font-medium"><?= substr($row['waktu_berangkat'], 0, 5) ?> WIB</div>
                                </div>
                                <div class="md:hidden">
                                    <?php if ($row['status'] == 'terverifikasi'): ?>
                                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-100">Verif</span>
                                    <?php else: ?>
                                        <span class="px-2 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-600 border border-amber-100">Pending</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>

                        <td class="block md:table-cell px-5 py-3 md:py-5 md:px-6 border-b md:border-b-slate-100 last:border-0">
                            <span class="md:hidden text-xs font-bold text-slate-400 uppercase mb-1 block">Rute</span>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center text-slate-700">
                                    <i class="fa-regular fa-circle-dot text-xs text-blue-500 mr-2 w-4"></i>
                                    <span class="font-medium"><?= $row['nama_asal'] ?? '-' ?></span>
                                </div>
                                <div class="ml-2 border-l border-dashed border-slate-300 h-3"></div>
                                <div class="flex items-center text-slate-700">
                                    <i class="fa-solid fa-location-dot text-xs text-red-500 mr-2 w-4"></i>
                                    <span class="font-medium"><?= $row['nama_tujuan'] ?? '-' ?></span>
                                </div>
                            </div>
                        </td>

                        <td class="block md:table-cell px-5 py-3 md:py-5 md:px-6 border-b md:border-b-slate-100 last:border-0">
                            <span class="md:hidden text-xs font-bold text-slate-400 uppercase mb-1 block">Armada</span>
                            <div class="font-bold text-slate-800"><?= $row['no_polisi'] ?></div>
                            <div class="text-xs text-slate-500"><?= $row['nama_driver'] ?></div>
                        </td>

                        <td class="block md:table-cell px-5 py-3 md:py-5 md:px-6 border-b md:border-b-slate-100 last:border-0 md:text-center">
                            <span class="md:hidden text-xs font-bold text-slate-400 uppercase mb-1 inline-block mr-2">Pax:</span>
                            <span class="bg-slate-100 text-slate-600 font-bold py-1 px-3 rounded-lg text-xs"><?= $row['jumlah_penumpang'] ?> Org</span>
                        </td>

                        <td class="hidden md:table-cell px-5 py-3 md:py-5 md:px-6 border-b md:border-b-slate-100 last:border-0 text-center">
                            <?php if ($row['status'] == 'terverifikasi'): ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/20">
                                    <i class="fa-solid fa-check"></i> Verified
                                </span>
                            <?php else: ?>
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/20 animate-pulse">
                                    <i class="fa-solid fa-clock"></i> Pending
                                </span>
                            <?php endif; ?>
                        </td>

                        <td class="block md:table-cell px-5 py-4 md:py-5 md:px-6 md:text-center">
                            <div class="flex justify-end md:justify-center items-center gap-3">
                                <?php if (!empty($row['foto_dokumentasi'])): ?>
                                    <button onclick="lihatFoto('<?= $base_url ?>assets/uploads/<?= $row['foto_dokumentasi'] ?>')" class="text-blue-600 hover:text-blue-800"><i class="fa-regular fa-image text-lg"></i></button>
                                <?php endif; ?>
                                <?php if ($row['status'] == 'pending'): ?>
                                    <a href="proses_laporan.php?act=hapus&id=<?= $row['id'] ?>" onclick="return confirm('Hapus?')" class="text-rose-500 hover:text-rose-700"><i class="fa-solid fa-trash-can text-lg"></i></a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endwhile;
            } else {
                echo "<div class='text-center py-16 text-slate-400 italic bg-white rounded-xl border border-slate-200'><p>Belum ada data laporan.</p></div>";
            }
            ?>
        </tbody>
    </table>
</div>

<div id="modalInput" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalInput')"></div>
    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end md:items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-100">
                
                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-white">
                    <h3 class="text-lg font-bold text-slate-800">Laporan Baru</h3>
                    <button onclick="toggleModal('modalInput')" class="text-slate-400 hover:text-red-500"><i class="fa-solid fa-xmark text-lg"></i></button>
                </div>

                <form action="proses_laporan.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="act" value="tambah">
                    
                    <div class="px-6 py-6 space-y-5 max-h-[70vh] overflow-y-auto custom-scrollbar">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase">Tanggal</label>
                                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm focus:border-blue-500 outline-none" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase">Jam</label>
                                <input type="time" name="waktu" value="<?= date('H:i') ?>" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm focus:border-blue-500 outline-none" required>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Lokasi Keberangkatan</label>
                            <div class="relative">
                                <select name="lokasi_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm focus:border-blue-500 outline-none appearance-none cursor-pointer" required>
                                    <option value="">-- Pilih Asal --</option>
                                    <?php foreach($data_lokasi as $lok): ?>
                                        <option value="<?= $lok['id'] ?>"><?= $lok['nama_lokasi'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-3.5 text-xs text-slate-500 pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Lokasi Tujuan</label>
                            <div class="relative">
                                <select name="tujuan_id" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm focus:border-blue-500 outline-none appearance-none cursor-pointer" required>
                                    <option value="">-- Pilih Tujuan --</option>
                                    <?php foreach($data_lokasi as $lok): ?>
                                        <option value="<?= $lok['id'] ?>"><?= $lok['nama_lokasi'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <i class="fa-solid fa-chevron-down absolute right-3 top-3.5 text-xs text-slate-500 pointer-events-none"></i>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase">No. Polisi</label>
                            <input type="text" name="no_polisi" placeholder="BK 1234 XX" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm outline-none uppercase font-bold" required>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase">Driver</label>
                                <input type="text" name="driver" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm outline-none" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase">Pax</label>
                                <input type="number" name="jumlah" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-3 text-sm outline-none" required>
                            </div>
                        </div>

                        <div class="space-y-1">
                             <label class="text-xs font-semibold text-slate-600 uppercase">Foto Bukti</label>
                            <input type="file" name="foto" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-col-reverse md:flex-row-reverse gap-3 border-t border-slate-100">
                        <button type="submit" class="w-full md:w-auto inline-flex justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-bold text-white shadow-sm hover:bg-blue-700 transition">Kirim Data</button>
                        <button type="button" onclick="toggleModal('modalInput')" class="w-full md:w-auto inline-flex justify-center rounded-xl bg-white border border-slate-300 px-5 py-3 text-sm font-bold text-slate-700 hover:bg-slate-50 transition">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleModal(id){ document.getElementById(id).classList.toggle('hidden'); }
    function lihatFoto(url){ Swal.fire({ imageUrl: url, imageHeight: 400, showConfirmButton: false, customClass: { popup: 'rounded-2xl w-[90%] md:w-[32em]' } }); }
    function konfirmasiHapus(id){ 
        Swal.fire({
            title: 'Hapus?', text: "Hapus data ini?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', cancelButtonColor: '#64748b', confirmButtonText: 'Ya', cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) window.location.href = "proses_laporan.php?act=hapus&id=" + id; }) 
    }
</script>

<?php include '../layouts/footer.php'; ?>