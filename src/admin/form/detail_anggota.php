<?php
// Validasi ID Pengguna dari URL
if (!isset($_GET['id'])) {
    echo "<div class='bg-red-100 p-4'>Error: ID Pengguna tidak valid.</div>"; return;
}
$id_pengguna = (int)$_GET['id'];

// --- Logika untuk UPDATE data pengguna saat form disubmit ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $status = mysqli_real_escape_string($koneksi, $_POST['status']);
    // Ambil tanggal, jika kosong set ke NULL
    $langganan_berakhir = !empty($_POST['langganan_berakhir_pada']) ? "'".$_POST['langganan_berakhir_pada']."'" : "NULL";

    $query_update = "UPDATE pengguna SET 
                        nama = '$nama', 
                        email = '$email', 
                        status = '$status', 
                        langganan_berakhir_pada = $langganan_berakhir
                     WHERE id_pengguna = $id_pengguna";
    
    if (mysqli_query($koneksi, $query_update)) {
        $_SESSION['pesan_sukses'] = "Data pengguna berhasil diperbarui.";
    } else {
        $_SESSION['pesan_error'] = "Gagal memperbarui data pengguna.";
    }
}

// --- Logika untuk MENGAMBIL data yang akan ditampilkan ---
$query_pengguna = "SELECT * FROM pengguna WHERE id_pengguna = $id_pengguna";
$result_pengguna = mysqli_query($koneksi, $query_pengguna);
$pengguna = mysqli_fetch_assoc($result_pengguna);

if (!$pengguna) {
    echo "<div class='bg-red-100 p-4'>Error: Pengguna tidak ditemukan.</div>"; return;
}

// Query untuk mengambil progress belajar pengguna
$query_progress = "
    SELECT 
        k.judul,
        rk.skor AS skor_terakhir,
        rk.status AS status_kuis,
        (SELECT COUNT(*) FROM materi WHERE id_kursus = k.id_kursus) AS total_materi,
        (SELECT COUNT(*) FROM progress_materi pm WHERE pm.id_pengguna = $id_pengguna AND pm.id_materi IN (SELECT id_materi FROM materi WHERE id_kursus = k.id_kursus)) AS materi_selesai
    FROM 
        riwayat_kuis rk
    JOIN 
        kursus k ON rk.id_kursus = k.id_kursus
    WHERE 
        rk.id_pengguna = $id_pengguna
    -- Ambil hanya riwayat kuis terbaru untuk setiap kursus
    AND rk.id_riwayat = (SELECT MAX(id_riwayat) FROM riwayat_kuis WHERE id_pengguna = $id_pengguna AND id_kursus = k.id_kursus)
    GROUP BY
        k.id_kursus
";
$result_progress = mysqli_query($koneksi, $query_progress);

// Ambil pesan notifikasi
$pesan_sukses = $_SESSION['pesan_sukses'] ?? ''; unset($_SESSION['pesan_sukses']);
$pesan_error = $_SESSION['pesan_error'] ?? ''; unset($_SESSION['pesan_error']);
?>

<div class="max-w-4xl mx-auto space-y-8">
    <!-- Header Halaman -->
    <div class="flex items-center gap-4">
        <a href="dashboard.php?page=anggota" class="text-codemy-purple" title="Kembali"><i class="fa-solid fa-arrow-left fa-lg"></i></a>
        <h1 class="text-2xl font-bold text-dark">Detail & Edit Anggota</h1>
    </div>

    <!-- Tampilkan Pesan Notifikasi -->
    <?php if ($pesan_sukses) echo "<div class='bg-green-100 text-green-700 p-4 rounded-lg'>$pesan_sukses</div>"; ?>
    <?php if ($pesan_error) echo "<div class='bg-red-100 text-red-700 p-4 rounded-lg'>$pesan_error</div>"; ?>

    <!-- Form Edit Pengguna -->
    <form action="dashboard.php?page=detail_anggota&id=<?php echo $id_pengguna; ?>" method="POST" class="bg-white p-6 rounded-xl shadow-md">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kolom Informasi Dasar -->
            <div>
                <label for="nama" class="block text-sm font-medium text-slate-700">Nama Lengkap</label>
                <input type="text" name="nama" value="<?php echo htmlspecialchars($pengguna['nama']); ?>" class="mt-1 w-full p-2 border rounded-lg">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($pengguna['email']); ?>" class="mt-1 w-full p-2 border rounded-lg">
            </div>
            <!-- Kolom Status -->
            <div>
                <label for="status" class="block text-sm font-medium text-slate-700">Status Akun</label>
                <select name="status" class="mt-1 w-full p-2 border rounded-lg">
                    <option value="aktif" <?php if($pengguna['status'] == 'aktif') echo 'selected'; ?>>Aktif</option>
                    <option value="nonaktif" <?php if($pengguna['status'] == 'nonaktif') echo 'selected'; ?>>Nonaktif</option>
                    <option value="diblokir" <?php if($pengguna['status'] == 'diblokir') echo 'selected'; ?>>Diblokir</option>
                </select>
            </div>
            <!-- Kolom Langganan -->
            <div>
                <label for="langganan_berakhir_pada" class="block text-sm font-medium text-slate-700">Langganan Berakhir Pada</label>
                <input type="date" name="langganan_berakhir_pada" value="<?php echo $pengguna['langganan_berakhir_pada'] ? date('Y-m-d', strtotime($pengguna['langganan_berakhir_pada'])) : ''; ?>" class="mt-1 w-full p-2 border rounded-lg">
            </div>
        </div>
        <div class="text-right mt-6">
            <button type="submit" class="bg-codemy-purple text-white px-6 py-2 rounded-lg font-semibold">
                <i class="fa-solid fa-save"></i> Update Pengguna
            </button>
        </div>
    </form>

    <!-- Tabel Progress Belajar -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-bold text-dark mb-4">Progress Belajar</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="p-3">Nama Kursus</th>
                        <th class="p-3">Progress Materi</th>
                        <th class="p-3 text-center">Skor Kuis Terakhir</th>
                        <th class="p-3 text-center">Status Kuis</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result_progress && mysqli_num_rows($result_progress) > 0): ?>
                        <?php while ($progress = mysqli_fetch_assoc($result_progress)): ?>
                            <?php
                                // Hitung persentase progress materi
                                $persentase = ($progress['total_materi'] > 0) ? ($progress['materi_selesai'] / $progress['total_materi']) * 100 : 0;
                            ?>
                            <tr class="border-b">
                                <td class="p-3 font-medium"><?php echo htmlspecialchars($progress['judul']); ?></td>
                                <td class="p-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-green-500 h-2.5 rounded-full" style="width: <?php echo $persentase; ?>%"></div>
                                    </div>
                                    <span class="text-xs text-slate-500"><?php echo round($persentase); ?>% selesai (<?php echo $progress['materi_selesai'] . '/' . $progress['total_materi']; ?>)</span>
                                </td>
                                <td class="p-3 text-center font-semibold"><?php echo $progress['skor_terakhir'] ? number_format($progress['skor_terakhir'], 2) : '-'; ?></td>
                                <td class="p-3 text-center">
                                    <?php if($progress['status_kuis'] == 'lulus'): ?>
                                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-1 rounded-full">Lulus</span>
                                    <?php elseif($progress['status_kuis'] == 'tidak_lulus'): ?>
                                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-1 rounded-full">Tidak Lulus</span>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="p-4 text-center text-slate-500">Pengguna ini belum mengikuti kursus apapun.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>