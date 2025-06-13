<?php
if (!isset($_GET['id_kursus'])) {
    echo "<div class='bg-red-100 p-4'>Error: ID Kursus tidak valid.</div>"; return;
}
$id_kursus = (int)$_GET['id_kursus'];

// untuk proses tambah pertanyaan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['tambah_pertanyaan'])) {
    $teks_pertanyaan = mysqli_real_escape_string($koneksi, $_POST['teks_pertanyaan']);
    $pilihan = $_POST['pilihan']; // Ini array
    $jawaban_benar_idx = $_POST['jawaban_benar']; // Ini index (0-3)

    if (!empty($teks_pertanyaan)) {
        mysqli_begin_transaction($koneksi);
        try {
            // 1. Simpan pertanyaan
            $stmt_q = mysqli_prepare($koneksi, "INSERT INTO pertanyaan (id_kursus, teks_pertanyaan) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_q, 'is', $id_kursus, $teks_pertanyaan);
            mysqli_stmt_execute($stmt_q);
            $id_pertanyaan_baru = mysqli_insert_id($koneksi);

            // 2. Simpan pilihan jawaban
            foreach ($pilihan as $index => $teks_pilihan) {
                if (empty($teks_pilihan)) continue;
                $is_correct = ($index == $jawaban_benar_idx) ? 1 : 0;
                $stmt_p = mysqli_prepare($koneksi, "INSERT INTO pilihan_jawaban (id_pertanyaan, teks_pilihan, cek_jawaban) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt_p, 'isi', $id_pertanyaan_baru, $teks_pilihan, $is_correct);
                mysqli_stmt_execute($stmt_p);
            }
            mysqli_commit($koneksi);
            $_SESSION['pesan_sukses'] = "Pertanyaan baru berhasil ditambahkan.";
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['pesan_error'] = "Gagal menambah pertanyaan.";
        }
        // Redirect ke halaman ini sendiri untuk refresh data dan menampilkan pesan
        header("Location: dashboard.php?page=kelola_kuis&id_kursus=" . $id_kursus);
        exit();
    }
}

// --- Logika untuk MENGAMBIL data yang akan ditampilkan ---
$query_kursus = "SELECT judul FROM kursus WHERE id_kursus = $id_kursus";
$kursus = mysqli_fetch_assoc(mysqli_query($koneksi, $query_kursus));

$query_pertanyaan = "SELECT * FROM pertanyaan WHERE id_kursus = $id_kursus ORDER BY id_pertanyaan ASC";
$result_pertanyaan = mysqli_query($koneksi, $query_pertanyaan);

// Ambil pesan notifikasi dari session
$pesan_sukses = $_SESSION['pesan_sukses'] ?? ''; unset($_SESSION['pesan_sukses']);
$pesan_error = $_SESSION['pesan_error'] ?? ''; unset($_SESSION['pesan_error']);
?>

<div class="max-w-4xl mx-auto space-y-8">
    
    <!-- Header Halaman -->
    <div class="flex items-center gap-4">
        <a href="dashboard.php?page=kuis" class="text-codemy-purple hover:text-codemy-purple-hover" title="Kembali ke Daftar Kuis"><i class="fa-solid fa-arrow-left fa-lg"></i></a>
        <div>
            <h1 class="text-2xl font-bold text-dark">Kelola Kuis</h1>
            <p class="text-slate-600">Modul: <?php echo htmlspecialchars($kursus['judul']); ?></p>
        </div>
    </div>

    <!-- Tampilkan Pesan Notifikasi -->
    <?php if ($pesan_sukses): ?>
        <div class="bg-green-100 text-green-700 p-4 rounded-lg"><?php echo $pesan_sukses; ?></div>
    <?php endif; ?>
    <?php if ($pesan_error): ?>
        <div class="bg-red-100 text-red-700 p-4 rounded-lg"><?php echo $pesan_error; ?></div>
    <?php endif; ?>

    <!-- === FORMULIR TAMBAH PERTANYAAN === -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-bold text-dark mb-4">Tambah Pertanyaan Baru</h2>
        <form action="dashboard.php?page=kelola_kuis&id_kursus=<?php echo $id_kursus; ?>" method="POST">
            <input type="hidden" name="tambah_pertanyaan" value="1">
            <div class="space-y-4">
                <div>
                    <label for="teks_pertanyaan" class="block text-sm font-medium text-slate-700 mb-1">Pertanyaan</label>
                    <textarea name="teks_pertanyaan" required class="w-full p-2 border rounded-lg" rows="3"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Pilihan Jawaban (Pilih yang benar)</label>
                    <div class="space-y-2">
                        <?php for ($i = 0; $i < 4; $i++): ?>
                        <div class="flex items-center gap-3">
                            <input type="radio" name="jawaban_benar" value="<?php echo $i; ?>" <?php if($i==0) echo 'checked'; ?>>
                            <input type="text" name="pilihan[]" required class="flex-1 p-2 border rounded-lg" placeholder="Pilihan <?php echo chr(65 + $i); ?>">
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="text-right">
                    <button type="submit" class="bg-codemy-purple text-codemy-purple hover:text-codemy-purple-hover text-white px-6 py-2 rounded-lg font-semibold">
                        <i class="fa-solid fa-plus"></i> Simpan Pertanyaan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- === DAFTAR PERTANYAAN YANG SUDAH ADA === -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-bold text-dark mb-4">Daftar Pertanyaan (<?php echo mysqli_num_rows($result_pertanyaan); ?> soal)</h2>
        <div class="space-y-3">
            <?php if (mysqli_num_rows($result_pertanyaan) > 0): ?>
                <?php while ($soal = mysqli_fetch_assoc($result_pertanyaan)): ?>
                    <div class="p-3 border rounded-lg flex justify-between items-center">
                        <p class="text-slate-800"><?php echo htmlspecialchars($soal['teks_pertanyaan']); ?></p>
                        <div class="space-x-3 flex-shrink-0 ml-4">
                             <a href="#" class="text-blue-500" title="Edit"><i class="fa-solid fa-pencil"></i></a>
                             <a href="#" class="text-danger" title="Hapus"><i class="fa-solid fa-trash"></i></a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="text-center text-slate-500 py-4">Belum ada pertanyaan untuk kuis ini.</p>
            <?php endif; ?>
        </div>
    </div>

</div>