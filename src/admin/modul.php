<?php
if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    unset($_SESSION['pesan_sukses']);
}

// Koneksi kategori untuk filter
$kategori_list = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");

// Ambil filter dari GET
$filter_kategori_id = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;
$search_keyword = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

// Siapkan kondisi WHERE
$where_conditions = [];
if ($filter_kategori_id > 0) {
    $where_conditions[] = "k.id_kategori = " . $filter_kategori_id;
}
if (!empty($search_keyword)) {
    $where_conditions[] = "(k.judul LIKE '%$search_keyword%' OR p.nama LIKE '%$search_keyword%')";
}
$where_clause = !empty($where_conditions) ? "WHERE " . implode(' AND ', $where_conditions) : "";

// Query utama
$query = "SELECT k.id_kursus, k.judul, k.gambar_banner, k.deskripsi, p.nama as pembuat, kat.nama_kategori, 
          (SELECT COUNT(*) FROM materi WHERE id_kursus = k.id_kursus) AS jumlah_materi, 
          (SELECT COUNT(DISTINCT id_pengguna) FROM riwayat_kuis WHERE id_kursus = k.id_kursus) AS jumlah_peserta 
          FROM kursus k 
          JOIN pengguna p ON k.dibuat_oleh = p.id_pengguna 
          LEFT JOIN kategori kat ON k.id_kategori = kat.id_kategori
          $where_clause 
          ORDER BY k.judul ASC";

$result = mysqli_query($koneksi, $query);
?>

<div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
    <!-- tampilkan pesan sukses -->
    <?php if (!empty($pesan_sukses)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Sukses!</p>
            <p><?php echo $pesan_sukses; ?></p>
        </div>
    <?php endif; ?>
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex flex-col items-left gap-2">
            <h2 class="text-2xl font-bold text-codemy-dark mb-2">Data Modul</h2>
            <div class="flex items-center gap-4 w-full md:w-auto ">
                <form action="dashboard.php" method="GET" class="flex flex-col md:flex-row gap-2 w-full">
                <input type="hidden" name="page" value="modul">

                <div class="relative w-full md:w-64">
                    <input type="text" name="search" placeholder="Cari judul modul..." value="<?php echo htmlspecialchars($search_keyword); ?>" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <div class="relative w-full md:w-64">
                    <select name="kategori" onchange="this.form.submit()" class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 appearance-none cursor-pointer text-gray-700 capitalize">
                        <option value="0">Tampilkan Semua Kategori</option>
                        <?php while ($kat_filter = mysqli_fetch_assoc($kategori_list)): ?>
                            <option value="<?php echo $kat_filter['id_kategori']; ?>" <?php echo ($filter_kategori_id == $kat_filter['id_kategori']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($kat_filter['nama_kategori']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                </div>
            </form>
            </div>
        </div>
        <a href="dashboard.php?page=tambah_modul" class="bg-[#6D00A8] text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 hover:bg-codemy-purple transition-colors duration-300">
            <span class="hidden md:inline">Tambah Modul</span>
            <i class="fa-solid fa-plus"></i>
        </a>
    </div>

    <!-- Tabel Data Modul -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-center">
            <thead class="bg-slate-100 text-slate-600 uppercase">
                <tr>
                    <th class="p-4 rounded-l-lg">No</th>
                    <th class="p-4 rounded-l-lg">Judul Modul</th>
                    <th class="p-4 text-center">Banner</th>
                    <th class="p-4 text-center">Deskripsi</th>
                    <th class="p-4">Kategori</th>
                    <th class="p-4">Dibuat Oleh</th>
                    <th class="p-4 text-center">Materi</th>
                    <th class="p-4 text-center">Peserta</th>
                    <th class="p-4 text-center rounded-r-lg">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                // cek apakah query berhasil dan ada data
                if ($result && mysqli_num_rows($result) > 0):
                    // loop setiap baris data modul
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                        <tr class="border-b border-gray-200 hover:bg-slate-50">
                            <td class="p-4 text-slate-600 text-center">
                                <?php echo $no; ?>
                            </td>
                            <!-- Kolom untuk Judul -->
                            <td class="p-4 font-medium text-dark">
                                <?php echo htmlspecialchars($row['judul']); ?>
                            </td>
                            <!-- Kolom untuk Banner -->
                            <td class="p-4 w-20">
                                <img src="../public/uploads/banners/<?php echo htmlspecialchars($row['gambar_banner']); ?>" alt="Banner <?php echo htmlspecialchars($row['judul']); ?>" class="w-20 p-2 h-20 object-cover rounded-md border border-codemy-active">
                            </td>
                            <!-- Kolom deskripsi -->
                            <td class="p-4 text-slate-600 max-w-xs">
                                <?php
                                $limit = 10;
                                $deskripsi = $row['deskripsi'];

                                echo htmlspecialchars(
                                    strlen($deskripsi) > $limit ? substr($deskripsi, 0, $limit) . '...' : $deskripsi
                                );
                                ?>
                            </td>
                            <!-- Kolom untuk Kategori -->
                            <td class="p-4 text-slate-600">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded-full"><?php echo htmlspecialchars($row['nama_kategori'] ?? 'N/A'); ?></span>
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
                                <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $row['id_kursus']; ?>" class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-semibold hover:bg-green-200 transition-colors" title="Kelola Konten">
                                    <i class="fa-solid fa-folder-open"></i> Kelola
                                </a>
                                <a href="dashboard.php?page=edit_modul&id=<?php echo $row['id_kursus']; ?>" class="text-blue-500 hover:text-blue-700" title="Edit Modul">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <!-- Tombol Hapus -->
                                <a href="dashboard.php?page=modul&aksi=hapus&id=<?php echo $row['id_kursus']; ?>" onclick="return confirm('Yakin ingin menghapus modul ini? Semua materi dan kuis di dalamnya akan ikut terhapus!');" class="text-red-500 hover:text-red-700" title="Hapus Modul">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        $no++;
                    endwhile;
                else:
                    ?>
                    <!-- Tampilan jika tidak ada data modul -->
                    <tr>
                        <td colspan="9" class="p-8 text-slate-500">
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