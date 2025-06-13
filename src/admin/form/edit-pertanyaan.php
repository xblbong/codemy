<?php
if (!isset($_GET['id_pertanyaan'])) {
    echo "<div class='bg-red-100 p-4'>Error: ID Pertanyaan tidak valid.</div>"; return;
}
$id_pertanyaan = (int)$_GET['id_pertanyaan'];

// select semua pertanyaan yang dimana id_pertanyaan = $id_pertanyaan untuk menampilkan pertanyaan yang ingin diedit 
$query_pertanyaan = "SELECT * FROM pertanyaan WHERE id_pertanyaan = $id_pertanyaan";
$result_pertanyaan = mysqli_query($koneksi, $query_pertanyaan);
$pertanyaan = mysqli_fetch_assoc($result_pertanyaan);

if (!$pertanyaan) {
    echo "<div class='bg-red-100 p-4'>Error: Pertanyaan tidak ditemukan.</div>"; return;
}
$id_kursus = $pertanyaan['id_kursus'];

// select semua pilihan jawaban yang dimana id_pertanyaan = $id_pertanyaan untuk menampilkan pilihan jawaban
$query_pilihan = "SELECT * FROM pilihan_jawaban WHERE id_pertanyaan = $id_pertanyaan";
$result_pilihan = mysqli_query($koneksi, $query_pilihan);

//untuk menampilkan pesan error
$pesan_error = $_SESSION['pesan_error'] ?? '';
unset($_SESSION['pesan_error']);
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b">
            <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="text-codemy-purple" title="Kembali">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-dark">Edit Pertanyaan Kuis</h1>
        </div>

        <?php if (!empty($pesan_error)): ?>
            <div class="bg-red-100 p-4 mb-6 rounded-lg text-red-700"><?php echo $pesan_error; ?></div>
        <?php endif; ?>

        <form action="dashboard.php?page=edit_pertanyaan&id_pertanyaan=<?php echo $id_pertanyaan; ?>" method="POST">
            <!-- Hidden input untuk mengirim id_kursus -->
            <input type="hidden" name="id_kursus" value="<?php echo $id_kursus; ?>">

            <div class="space-y-6">
                <!-- Teks Pertanyaan -->
                <div>
                    <label for="teks_pertanyaan" class="block text-sm font-medium text-slate-700 mb-2">Teks Pertanyaan</label>
                    <textarea name="teks_pertanyaan" id="teks_pertanyaan" rows="4" required class="w-full p-3 border rounded-lg"><?php echo htmlspecialchars($pertanyaan['teks_pertanyaan']); ?></textarea>
                </div>

                <!-- Pilihan Jawaban -->
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilihan Jawaban (Pilih satu sebagai jawaban benar)</label>
                    <div class="space-y-2">
                        <?php while ($pilihan = mysqli_fetch_assoc($result_pilihan)): ?>
                        <div class="flex items-center gap-3">
                            <!-- Hidden input untuk ID Pilihan -->
                            <input type="hidden" name="pilihan_id[]" value="<?php echo $pilihan['id_pilihan']; ?>">
                            <!-- Radio button -->
                            <input type="radio" name="jawaban_benar_id" value="<?php echo $pilihan['id_pilihan']; ?>" <?php echo ($pilihan['cek_jawaban'] == 1) ? 'checked' : ''; ?> class="h-4 w-4 text-codemy-purple">
                            <!-- Input teks pilihan -->
                            <input type="text" name="pilihan_teks[]" required class="flex-1 p-2 border rounded-lg" value="<?php echo htmlspecialchars($pilihan['teks_pilihan']); ?>">
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Penjelasan Jawaban -->
                 <div>
                    <label for="penjelasan" class="block text-sm font-medium text-slate-700 mb-2">Penjelasan Jawaban (Opsional)</label>
                    <textarea name="penjelasan" id="penjelasan" rows="3" class="w-full p-3 border rounded-lg"><?php echo htmlspecialchars($pertanyaan['penjelasan']); ?></textarea>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-4 pt-4 border-t">
                     <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="bg-slate-200 text-slate-800 px-6 py-2 rounded-lg">Batal</a>
                    <button type="submit" class="bg-codemy-purple text-white px-6 py-2 rounded-lg flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update Pertanyaan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>