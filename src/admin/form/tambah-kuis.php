<?php
// Validasi ID Kursus dari URL
if (!isset($_GET['id_kursus'])) {
    echo "<div class='bg-red-100 p-4 rounded-lg'>Error: ID Kursus tidak valid.</div>";
    return;
}
$id_kursus = (int)$_GET['id_kursus'];

$pesan_error = '';

// --- Logika untuk Memproses Form ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Gunakan transaction untuk memastikan semua query berhasil atau tidak sama sekali
    mysqli_begin_transaction($koneksi);

    try {
        // Ambil data pertanyaan
        $teks_pertanyaan = mysqli_real_escape_string($koneksi, $_POST['teks_pertanyaan']);
        $penjelasan = mysqli_real_escape_string($koneksi, $_POST['penjelasan']);
        
        if (empty($teks_pertanyaan)) {
            throw new Exception("Teks pertanyaan tidak boleh kosong.");
        }

        // 1. Simpan Pertanyaan ke tabel 'pertanyaan'
        $query_pertanyaan = "INSERT INTO pertanyaan (id_kursus, teks_pertanyaan, penjelasan) VALUES ('$id_kursus', '$teks_pertanyaan', '$penjelasan')";
        if (!mysqli_query($koneksi, $query_pertanyaan)) {
            throw new Exception("Gagal menyimpan pertanyaan.");
        }
        $id_pertanyaan_baru = mysqli_insert_id($koneksi);

        // Ambil data pilihan jawaban dan jawaban benar
        $pilihan_jawaban = $_POST['pilihan_jawaban']; // Ini adalah array
        $jawaban_benar = $_POST['jawaban_benar']; // Ini adalah index dari jawaban yg benar (0, 1, 2, 3)

        // 2. Simpan setiap pilihan jawaban ke tabel 'pilihan_jawaban'
        foreach ($pilihan_jawaban as $index => $teks_pilihan) {
            $teks_pilihan_bersih = mysqli_real_escape_string($koneksi, $teks_pilihan);
            $apakah_benar = ($index == $jawaban_benar) ? 1 : 0; // Tentukan apakah ini jawaban yg benar

            if (empty($teks_pilihan_bersih)) continue; // Lewati jika pilihan kosong

            $query_pilihan = "INSERT INTO pilihan_jawaban (id_pertanyaan, teks_pilihan, cek_jawaban) VALUES ('$id_pertanyaan_baru', '$teks_pilihan_bersih', '$apakah_benar')";
            if (!mysqli_query($koneksi, $query_pilihan)) {
                throw new Exception("Gagal menyimpan pilihan jawaban.");
            }
        }
        
        // Jika semua berhasil, commit transaksi
        mysqli_commit($koneksi);

        // Redirect kembali ke halaman kelola modul
        header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus . "&status=sukses_pertanyaan");
        exit();

    } catch (Exception $e) {
        // Jika ada error, batalkan semua query (rollback)
        mysqli_rollback($koneksi);
        $pesan_error = $e->getMessage();
    }
}
?>
<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <!-- Header Formulir -->
        <div class="flex items-center gap-4 mb-6 pb-4 border-b">
            <a href="dashboard.php?page=kelola_modul&id_kursus=<?php echo $id_kursus; ?>" class="text-codemy-purple hover:text-codemy-purple-hover" title="Kembali">
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
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pilihan Jawaban (Centang yang benar)</label>
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