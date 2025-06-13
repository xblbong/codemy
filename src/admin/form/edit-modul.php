<?php
if (!isset($_GET['id'])) {
    echo "<div class='bg-red-100 p-4'>Error: ID Modul tidak valid.</div>";
    return;
}
$id_kursus = (int)$_GET['id'];

// Ambil data kursus yang akan diedit
$query_kursus = "SELECT * FROM kursus WHERE id_kursus = $id_kursus";
$result_kursus = mysqli_query($koneksi, $query_kursus);
$kursus = mysqli_fetch_assoc($result_kursus);
if (!$kursus) {
    echo "<div class='bg-red-100 p-4'>Error: Modul tidak ditemukan.</div>";
    return;
}

// Ambil semua kategori untuk dropdown
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);

// Ambil error dan input lama dari session jika ada
$errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];
unset($_SESSION['form_errors'], $_SESSION['old_input']);
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b">
            <a href="dashboard.php?page=modul" class="text-codemy-purple" title="Kembali">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-dark">Edit Modul</h1>
        </div>

        <?php if (isset($errors['database'])): ?>
            <div class="bg-red-100 p-4 mb-6 rounded-lg"><?php echo $errors['database']; ?></div>
        <?php endif; ?>

        <form action="dashboard.php?page=edit_modul&id=<?php echo $id_kursus; ?>" method="POST" enctype="multipart/form-data">
            <!-- Hidden input untuk mengirim nama file banner lama -->
            <input type="hidden" name="banner_lama" value="<?php echo htmlspecialchars($kursus['gambar_banner']); ?>">

            <div class="space-y-6">
                <!-- Judul Kursus -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-gray-800 mb-2">Judul Kursus</label>
                    <input type="text" name="judul" id="judul" value="<?php echo htmlspecialchars($old_input['judul'] ?? $kursus['judul']); ?>" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['judul']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-codemy-purple-hover'; ?>" placeholder="Tuliskan judul kursus di sini..." required>
                    <?php if (isset($errors['judul'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['judul']}</p>"; ?>
                </div>

                <!-- Kategori -->
                <div class="relative">
                    <label for="id_kategori" class="block text-sm font-medium text-gray-800 mb-2">Kategori Modul</label>
                    <select name="id_kategori" id="id_kategori" class="capitalize w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 appearance-none cursor-pointer">
                        <option value="">-- Pilih Kategori --</option>
                        <?php while ($kat = mysqli_fetch_assoc($result_kategori)): ?>
                            <option value="<?php echo $kat['id_kategori']; ?>"
                                <?php
                                // Logika untuk memilih kategori yang sudah tersimpan
                                $selected_kategori = $old_input['id_kategori'] ?? $kursus['id_kategori'];
                                if ($selected_kategori == $kat['id_kategori']) echo 'selected';
                                ?>>
                                <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 top-6 right-0 flex items-center px-2 text-gray-700">
                        <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                            <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                        </svg>
                    </div>
                    <?php if (isset($errors['kategori'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['kategori']}</p>"; ?>
                </div>

                <!-- Banner -->
                <div>
                    <label for="gambar_banner" class="block text-sm font-medium text-gray-800 mb-2">Ganti Gambar Banner (Opsional)</label>
                    <div class="flex items-center gap-4">
                        <?php if (!empty($kursus['gambar_banner'])): ?>
                            <img src="../public/uploads/banners/<?php echo htmlspecialchars($kursus['gambar_banner']); ?>" alt="Banner saat ini" class="w-20 h-20 object-cover rounded-md">
                        <?php endif; ?>
                        <input type="file" name="gambar_banner" id="gambar_banner" class="block w-full text-sm ...">
                    </div>
                    <?php if (isset($errors['banner'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['banner']}</p>"; ?>
                </div>

                <!-- Deskripsi Kursus -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-gray-800 mb-2">Deskripsi Kursus</label>
                    <textarea name="deskripsi" id="deskripsi" rows="4" class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['deskripsi']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-codemy-purple-hover'; ?>" placeholder="Tuliskan deskripsi kursus di sini..." required><?php echo htmlspecialchars($old_input['deskripsi'] ?? $kursus['deskripsi']); ?></textarea>
                    <?php if (isset($errors['deskripsi'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['deskripsi']}</p>"; ?>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="dashboard.php?page=modul" class="bg-slate-200 px-6 py-2 rounded-lg font-semibold">Batal</a>
                    <button type="submit" class="bg-codemy-purple text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update Modul
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>