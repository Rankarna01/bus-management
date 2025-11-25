<?php
include '../layouts/header.php';
include '../layouts/sidebar.php';
include '../layouts/topbar.php';

// Ambil ID user yang sedang login
$user_id = $_SESSION['user_id'];
?>

<div class="flex flex-col md:flex-row justify-between items-end mb-8 gap-4">
    <div>
        <h3 class="text-3xl font-bold text-slate-800 tracking-tight">Laporan Operasional</h3>
        <p class="text-slate-500 mt-1 text-sm">Monitor riwayat dan input keberangkatan armada.</p>
    </div>
    
    <button onclick="toggleModal('modalInput')" class="group relative inline-flex items-center justify-center px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 hover:shadow-lg hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-600">
        <i class="fa-solid fa-plus mr-2 transition-transform group-hover:rotate-90"></i>
        Buat Laporan Baru
    </button>
</div>

<div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-xs uppercase tracking-wider text-slate-500 font-semibold">
                    <th class="py-5 px-6"><i class="fa-regular fa-calendar-days mr-2"></i>Waktu</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-bus-simple mr-2"></i>Armada</th>
                    <th class="py-5 px-6"><i class="fa-solid fa-route mr-2"></i>Rute</th>
                    <th class="py-5 px-6 text-center"><i class="fa-solid fa-users mr-2"></i>Pax</th>
                    <th class="py-5 px-6 text-center"><i class="fa-solid fa-circle-info mr-2"></i>Status</th>
                    <th class="py-5 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm text-slate-600">
                <?php
                $query = "SELECT * FROM laporan_keberangkatan WHERE user_id = '$user_id' ORDER BY created_at DESC";
                $result = mysqli_query($conn, $query);

                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-200">
                            
                            <td class="py-5 px-6 align-middle">
                                <div class="font-bold text-slate-800"><?= date('d M Y', strtotime($row['tanggal_berangkat'])) ?></div>
                                <div class="mt-1 flex items-center text-xs text-blue-600 font-medium">
                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-2"></div>
                                    <?= substr($row['waktu_berangkat'], 0, 5) ?> WIB
                                </div>
                            </td>

                            <td class="py-5 px-6 align-middle">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mr-3">
                                        <i class="fa-solid fa-bus"></i>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-800"><?= $row['no_polisi'] ?></div>
                                        <div class="text-xs text-slate-400 mt-0.5"><?= $row['nama_driver'] ?></div>
                                    </div>
                                </div>
                            </td>

                            <td class="py-5 px-6 align-middle">
                                <span class="font-medium text-slate-700"><?= $row['tujuan'] ?></span>
                            </td>

                            <td class="py-5 px-6 align-middle text-center">
                                <span class="bg-slate-100 text-slate-600 font-bold py-1.5 px-3 rounded-lg text-xs">
                                    <?= $row['jumlah_penumpang'] ?>
                                </span>
                            </td>

                            <td class="py-5 px-6 align-middle text-center">
                                <?php if ($row['status'] == 'terverifikasi'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 ring-1 ring-inset ring-emerald-600/20">
                                        <i class="fa-solid fa-check text-[10px]"></i> Verified
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-inset ring-amber-600/20 animate-pulse">
                                        <i class="fa-solid fa-clock text-[10px]"></i> Pending
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="py-5 px-6 align-middle text-center">
                                <div class="flex justify-center items-center gap-3">
                                    <?php if (!empty($row['foto_dokumentasi'])): ?>
                                        <button onclick="lihatFoto('<?= $base_url ?>assets/uploads/<?= $row['foto_dokumentasi'] ?>')" 
                                            class="h-8 w-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-100 transition" 
                                            title="Lihat Foto">
                                            <i class="fa-regular fa-image"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="h-8 w-8 flex items-center justify-center text-slate-300"><i class="fa-solid fa-image-slash"></i></span>
                                    <?php endif; ?>

                                    <?php if ($row['status'] == 'pending'): ?>
                                        <button onclick="konfirmasiHapus(<?= $row['id'] ?>)" 
                                            class="h-8 w-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center hover:bg-rose-100 transition" 
                                            title="Batalkan">
                                            <i class="fa-solid fa-trash-can text-sm"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile;
                } else {
                    echo "<tr><td colspan='6' class='text-center py-16 text-slate-400 italic bg-white'>
                            <div class='flex flex-col items-center'>
                                <i class='fa-solid fa-clipboard-list text-4xl mb-3 text-slate-200'></i>
                                <p>Belum ada data laporan.</p>
                            </div>
                          </td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalInput" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true">
    
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalInput')"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-center justify-center p-4 text-center">
            
            <div class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full max-w-lg border border-slate-100">
                
                <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-white">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Keberangkatan Baru</h3>
                        <p class="text-xs text-slate-500">Isi formulir perjalanan armada.</p>
                    </div>
                    <button onclick="toggleModal('modalInput')" class="h-8 w-8 rounded-full bg-slate-50 text-slate-400 hover:bg-slate-100 hover:text-red-500 transition flex items-center justify-center">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <form action="proses_laporan.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="act" value="tambah">
                    
                    <div class="px-6 py-6 space-y-5">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Tanggal</label>
                                <input type="date" name="tanggal" value="<?= date('Y-m-d') ?>" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Jam</label>
                                <input type="time" name="waktu" value="<?= date('H:i') ?>" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">No. Polisi</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400"><i class="fa-solid fa-bus"></i></span>
                                <input type="text" name="no_polisi" placeholder="Contoh: BK 7788 XX" class="w-full rounded-xl border-slate-200 bg-slate-50 pl-9 pr-3 py-2.5 text-sm uppercase font-semibold focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-3 gap-4">
                            <div class="col-span-2 space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Driver</label>
                                <input type="text" name="driver" placeholder="Nama Supir" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                            </div>
                            <div class="space-y-1">
                                <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Penumpang</label>
                                <input type="number" name="jumlah" min="0" placeholder="0" class="w-full rounded-xl border-slate-200 bg-slate-50 px-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Tujuan</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2.5 text-slate-400"><i class="fa-solid fa-location-dot"></i></span>
                                <input type="text" name="tujuan" placeholder="Contoh: Medan - Banda Aceh" class="w-full rounded-xl border-slate-200 bg-slate-50 pl-9 pr-3 py-2.5 text-sm focus:border-blue-500 focus:bg-white focus:ring-1 focus:ring-blue-500 transition" required>
                            </div>
                        </div>

                        <div class="space-y-1">
                             <label class="text-xs font-semibold text-slate-600 uppercase tracking-wide">Foto Bukti</label>
                            <label class="flex justify-center w-full h-24 px-4 transition bg-white border-2 border-slate-300 border-dashed rounded-xl appearance-none cursor-pointer hover:border-blue-400 hover:bg-slate-50 focus:outline-none">
                                <span class="flex items-center space-x-2">
                                    <i class="fa-solid fa-cloud-arrow-up text-slate-400 text-xl"></i>
                                    <span class="font-medium text-slate-600 text-sm">Klik untuk upload foto</span>
                                </span>
                                <input type="file" name="foto" accept="image/*" class="hidden">
                            </label>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-row-reverse gap-2 border-t border-slate-100">
                        <button type="submit" class="inline-flex w-full justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm hover:bg-blue-700 sm:w-auto transition">
                            Kirim Data
                        </button>
                        <button type="button" onclick="toggleModal('modalInput')" class="inline-flex w-full justify-center rounded-xl bg-white border border-slate-300 px-5 py-2.5 text-sm font-bold text-slate-700 hover:bg-slate-50 sm:w-auto transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle('hidden');
    }

    function lihatFoto(url) {
        Swal.fire({
            imageUrl: url,
            imageHeight: 400,
            imageAlt: 'Bukti Foto',
            showConfirmButton: false,
            showCloseButton: true,
            customClass: {
                popup: 'rounded-2xl'
            },
            backdrop: `rgba(15, 23, 42, 0.6)`
        });
    }

    function konfirmasiHapus(id) {
        Swal.fire({
            title: 'Hapus Laporan?',
            text: "Data yang statusnya pending akan dihapus permanen.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
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