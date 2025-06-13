<?php
$query = "SELECT k.id_kursus, k.judul,
        -- hitung jumlah pertanyaan yang ada di kuis kursus ini
        count(distinct p.id_pertanyaan) as jumlah_pertanyaan,
        -- hitung berapa kali kuis ini sudah dikerjakan
        count(distinct rk.id_riwayat) as jumlah_pengerjaan,
        -- hitung skor rata-rata dari semua pengerjaan
        avg(rk.skor) as rata_rata_skor
    FROM 
        kursus k
    -- gunakan left join supaya kursus yang belum punya pertanyaan tetap muncul
    LEFT JOIN 
        pertanyaan p ON k.id_kursus = p.id_kursus
    LEFT JOIN 
        riwayat_kuis rk ON k.id_kursus = rk.id_kursus
    GROUP BY
        k.id_kursus, k.judul
    ORDER BY 
        k.judul ASC
";
$result = mysqli_query($koneksi, $query);
?>

<div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex flex-col items-left gap-2">
            <h2 class="text-2xl font-bold text-codemy-dark mb-2">Data Kuis</h2>
            <div class="flex items-center gap-4 w-full md:w-auto ">
                <div class="relative flex-1 md:flex-initial">
                    <input type="text" placeholder="Cari pengguna..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-hover">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>
        <!-- <button class="bg-[#6D00A8] text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 hover:bg-primary-hover transition-colors duration-300">
            <span class="hidden md:inline">Tambah Kuis</span>
            <i class="fa-solid fa-plus"></i>
        </button> -->
    </div>
    <!-- Tabel Data Kuis -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-100 text-slate-600 uppercase">
                <tr>
                    <th class="p-4 rounded-l-lg">Judul Kuis (Modul)</th>
                    <th class="p-4 text-center">Jumlah Pertanyaan</th>
                    <th class="p-4 text-center">Jumlah Pengerjaan</th>
                    <th class="p-4 text-center">Rata-rata Skor</th>
                    <!-- <th class="p-4 text-center rounded-r-lg">Aksi</th> -->
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && mysqli_num_rows($result) > 0):
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                        <tr class="border-b border-gray-200 hover:bg-slate-50">
                            <td class="p-4 font-medium text-dark">
                                <?php echo htmlspecialchars($row['judul']); ?>
                            </td>
                            <td class="p-4 text-slate-600 text-center">
                                <?php echo $row['jumlah_pertanyaan']; ?>
                            </td>
                            <td class="p-4 text-slate-600 text-center">
                                <?php echo $row['jumlah_pengerjaan']; ?>
                            </td>
                            <td class="p-4 text-slate-600 text-center font-semibold">
                                <?php
                                // tampil skor rata-rata, dikasih format 2 desimal klo ada
                                // jika belum ada yang mengerjakan (NULL), tampilkan '-'.
                                echo $row['rata_rata_skor'] ? number_format($row['rata_rata_skor'], 2) : '-';
                                ?>
                            </td>
                            <td class="p-4 text-center">
                                <!-- Tombol Kelola Pertanyaan -->
                                <!-- <a href="dashboard.php?page=kelola_kuis&id_kursus=<?php echo $row['id_kursus']; ?>" class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-semibold hover:bg-blue-200 transition-colors" title="Kelola Pertanyaan">
                                    <i class="fa-solid fa-list-check"></i> Kelola Pertanyaan
                                </a> -->
                            </td>
                        </tr>
                    <?php
                    endwhile;
                else:
                    ?>
                    <!-- tampilan jika tidak ada data kursus sama sekali -->
                    <tr>
                        <td colspan="5" class="p-8 text-center text-slate-500">
                            Belum ada modul/kuis yang tersedia.
                        </td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>