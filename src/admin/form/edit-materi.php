<?php
if (!isset($_GET['id_materi'])) {
    echo "<div class='bg-red-100 p-4 rounded-lg'>Error: ID Materi tidak valid.</div>";
    return;
}
$id_materi = (int)$_GET['id_materi'];

//ambil data materi dari database
$query_materi = "SELECT * FROM materi WHERE id_materi = $id_materi";
$result_materi = mysqli_query($koneksi, $query_materi);
$materi = mysqli_fetch_assoc($result_materi);

// kalau materi tidak ditemukan
if (!$materi) {
    echo "<div class='bg-red-100 p-4 rounded-lg'>Error: Materi tidak ditemukan.</div>";
    return;
}
$id_kursus = $materi['id_kursus']; // simpan id_kursus untuk digunakan di form

// ambil error dan input lama dari session
$errors = $_SESSION['form_errors'] ?? [];
$old_input = $_SESSION['old_input'] ?? [];

// hapus session setelah diambil agar tidak muncul lagi saat refresh
unset($_SESSION['form_errors']);
unset($_SESSION['old_input']);
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <!-- Header Formulir -->
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-200">
            <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="text-codemy-purple hover:text-codemy-purple-hover" title="Kembali ke Kelola Modul">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-dark">Edit Materi</h1>
        </div>

        <!-- Tampilkan pesan error database jika ada -->
        <?php if (isset($errors['database'])): ?>
            <div class="bg-red-100 p-4 mb-6 rounded-lg text-red-700"><?php echo $errors['database']; ?></div>
        <?php endif; ?>

        <!-- Formulir -->
        <form action="dashboard.php?page=edit_materi&id_materi=<?php echo $id_materi; ?>" method="POST">
            <!-- Hidden input untuk mengirim id_kursus saat submit -->
            <input type="hidden" name="id_kursus" value="<?php echo $id_kursus; ?>">
            
            <div class="space-y-6">
                <!-- Judul Materi -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-2">Judul Materi</label>
                    <input type="text" name="judul" id="judul" required 
                           value="<?php echo htmlspecialchars($old_input['judul'] ?? $materi['judul']); ?>"
                           class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['judul']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-codemy-purple-hover'; ?>">
                    <?php if (isset($errors['judul'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['judul']}</p>"; ?>
                </div>

                <!-- Isi Materi -->
                <div>
                    <label for="isi_materi" class="block text-sm font-medium text-slate-700 mb-2">Isi Materi / Teks</label>
                    <textarea name="isi_materi" id="isi_materi" rows="8" required 
                              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['isi_materi']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-codemy-purple-hover'; ?>" placeholder="Tuliskan isi materi di sini..."
                    ><?php echo htmlspecialchars($old_input['isi_materi'] ?? $materi['isi_materi']); ?></textarea>
                    <?php if (isset($errors['isi_materi'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['isi_materi']}</p>"; ?>
                </div>

                <!-- URL Video -->
                <div>
                    <label for="url_video" class="block text-sm font-medium text-slate-700 mb-2">URL Video YouTube (Opsional)</label>
                    <input type="url" name="url_video" id="url_video"
                           value="<?php echo htmlspecialchars($old_input['url_video'] ?? $materi['url_video']); ?>"
                           class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['url_video']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-codemy-purple-hover'; ?>">
                    <?php if (isset($errors['url_video'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['url_video']}</p>"; ?>
                </div>

                <!-- Nomor Urut -->
                <div>
                    <label for="nomor_urut" class="block text-sm font-medium text-slate-700 mb-2">Nomor Urut</label>
                    <input type="number" name="nomor_urut" id="nomor_urut" required min="1"
                           value="<?php echo htmlspecialchars($old_input['nomor_urut'] ?? $materi['nomor_urut']); ?>"
                           class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['nomor_urut']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-codemy-purple-hover'; ?>">
                    <?php if (isset($errors['nomor_urut'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['nomor_urut']}</p>"; ?>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                    <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="bg-slate-200 text-slate-800 px-6 py-2 rounded-lg font-semibold">Batal</a>
                    <button type="submit" class="bg-codemy-purple text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>