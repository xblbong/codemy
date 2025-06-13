<?php
if (!isset($_GET['id_kursus'])) {
    echo "<div class='bg-red-100 p-4'>Error: ID Kursus tidak valid.</div>"; return;
}
$id_kursus = (int)$_GET['id_kursus'];

$pesan_error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <!-- Header Formulir -->
        <div class="flex items-center gap-4 mb-6 pb-4 border-b">
            <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="text-codemy-purple" title="Kembali">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-dark">Tambah Pertanyaan Kuis Baru</h1>
        </div>

        <?php if (!empty($pesan_error)): ?>
            <div class="bg-red-100 p-4 mb-6 rounded-lg text-red-700"><?php echo $pesan_error; ?></div>
        <?php endif; ?>

        <form action="dashboard.php?page=tambah_pertanyaan&id_kursus=<?php echo $id_kursus; ?>" method="POST">
            <div class="space-y-6">
                <!-- Teks Pertanyaan -->
                <div>
                    <label for="teks_pertanyaan" class="block text-sm font-medium text-slate-700 mb-2">Teks Pertanyaan</label>
                    <textarea name="teks_pertanyaan" id="teks_pertanyaan" rows="4" required class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Tuliskan pertanyaan di sini..."></textarea>
                </div>

                <!-- Pilihan Jawaban -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilihan Jawaban (Pilih satu sebagai jawaban benar)</label>
                    <div class="space-y-2">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="flex items-center gap-3">
                            <input type="radio" name="jawaban_benar" value="<?php echo $i; ?>" <?php echo ($i == 0) ? 'checked' : ''; ?> class="h-4 w-4 text-codemy-purple focus:ring-codemy-purple">
                            <input type="text" name="pilihan_jawaban[]" required class="flex-1 p-2 border border-gray-300 rounded-lg" placeholder="Pilihan <?php echo chr(65 + $i); ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Penjelasan Jawaban -->
                 <div>
                    <label for="penjelasan" class="block text-sm font-medium text-slate-700 mb-2">Penjelasan Jawaban (Opsional)</label>
                    <textarea name="penjelasan" id="penjelasan" rows="3" class="w-full p-3 border border-gray-300 rounded-lg" placeholder="Jelaskan mengapa jawaban tersebut benar..."></textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                     <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="bg-slate-200 text-slate-800 px-6 py-2 rounded-lg font-semibold">Batal</a>
                    <button type="submit" class="bg-codemy-purple text-white px-6 py-2 rounded-lg font-semibold flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Simpan Pertanyaan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>