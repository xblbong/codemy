<?php
$query = "SELECT  k.id_kursus, k.judul, p.nama as pembuat, (SELECT COUNT(*) FROM materi WHERE id_kursus = k.id_kursus) AS jumlah_materi, (SELECT COUNT(DISTINCT id_pengguna) FROM riwayat_kuis WHERE id_kursus = k.id_kursus) AS jumlah_peserta FROM kursus k JOIN pengguna p ON k.dibuat_oleh = p.id_pengguna ORDER BY k.judul ASC";
$result = mysqli_query($koneksi, $query);
?>

<div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex flex-col items-left gap-2">
            <h2 class="text-2xl font-bold text-codemy-dark mb-2">Data Modul</h2>
            <div class="flex items-center gap-4 w-full md:w-auto ">
                <div class="relative flex-1 md:flex-initial">
                    <input type="text" placeholder="Cari pengguna..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-hover">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>
        <button class="bg-[#6D00A8] text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 hover:bg-primary-hover transition-colors duration-300">
            <span class="hidden md:inline">Tambah Modul</span>
            <i class="fa-solid fa-plus"></i>
        </button>
    </div>

    <!-- Tabel Data Modul -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-100 text-slate-600 uppercase">
                <tr>
                    <th class="p-4 rounded-l-lg">Judul Modul</th>
                    <th class="p-4">Dibuat Oleh</th>
                    <th class="p-4 text-center">Jumlah Materi</th>
                    <th class="p-4 text-center">Peserta</th>
                    <th class="p-4 text-center rounded-r-lg">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Cek apakah query berhasil dan ada data
                if ($result && mysqli_num_rows($result) > 0):
                    // Loop melalui setiap baris data modul
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                        <tr class="border-b border-gray-200 hover:bg-slate-50">
                            <td class="p-4 font-medium text-dark">
                                <?php echo htmlspecialchars($row['judul']); ?>
                            </td>
                            <td class="p-4 text-slate-600">
                                <?php echo htmlspecialchars($row['pembuat']); ?>
                            </td>
                            <td class="p-4 text-slate-600 text-center">
                                <?php echo $row['jumlah_materi']; ?>
                            </td>
                            <td class="p-4 text-slate-600 text-center">
                                <?php echo $row['jumlah_peserta']; ?>
                            </td>
                            <td class="p-4 text-center space-x-2">
                                <!-- Tombol Kelola: Mengarah ke halaman detail untuk menambah/mengedit materi & kuis -->
                                <a href="kelola_modul.php?id_kursus=<?php echo $row['id_kursus']; ?>" class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold hover:bg-green-200 transition-colors" title="Kelola Konten">
                                    <i class="fa-solid fa-folder-open"></i> Kelola
                                </a>
                                <!-- Tombol Edit: Untuk mengedit judul/deskripsi modul -->
                                <a href="edit_modul.php?id=<?php echo $row['id_kursus']; ?>" class="text-blue-500 hover:text-blue-700" title="Edit Modul">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <!-- Tombol Hapus -->
                                <a href="hapus_modul.php?id=<?php echo $row['id_kursus']; ?>" onclick="return confirm('Yakin ingin menghapus modul ini? Semua materi dan kuis di dalamnya akan ikut terhapus!');" class="text-danger hover:text-red-700" title="Hapus Modul">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <!-- Tampilan jika tidak ada data modul -->
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">
                            Belum ada modul yang dibuat. Silakan klik "Tambah Modul" untuk memulai.
                        </td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>