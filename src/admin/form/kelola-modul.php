<?php

if (!isset($_GET['id_kursus'])) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded-lg'>Error: ID Kursus tidak ditemukan.</div>";
    return;
}
$id_kursus = (int)$_GET['id_kursus'];

$query_kursus = "SELECT judul, deskripsi FROM kursus WHERE id_kursus = $id_kursus";
$result_kursus = mysqli_query($koneksi, $query_kursus);
$kursus = mysqli_fetch_assoc($result_kursus);

if (!$kursus) {
    echo "<div class='bg-red-100 text-red-700 p-4 rounded-lg'>Error: Kursus dengan ID tersebut tidak ditemukan.</div>";
    return;
}

$query_materi = "SELECT * FROM materi WHERE id_kursus = $id_kursus ORDER BY nomor_urut ASC";
$result_materi = mysqli_query($koneksi, $query_materi);

$query_pertanyaan = "SELECT * FROM pertanyaan WHERE id_kursus = $id_kursus";
$result_pertanyaan = mysqli_query($koneksi, $query_pertanyaan);
?>

<div class="max-w-7xl mx-auto">
    <?php
    if (isset($_SESSION['pesan_sukses'])) {
        echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert"><p>' . $_SESSION['pesan_sukses'] . '</p></div>';
        unset($_SESSION['pesan_sukses']);
    }
    if (isset($_SESSION['pesan_error'])) {
        echo '<div class="bg-red-100 border-l-4 border-danger text-red-700 p-4 mb-6 rounded-md" role="alert"><p>' . $_SESSION['pesan_error'] . '</p></div>';
        unset($_SESSION['pesan_error']);
    }
    ?>
    <div class="flex items-center gap-4 mb-6">
        <a href="dashboard.php?page=modul" class="text-[#6D00A8] hover:text-[#6D00A8]" title="Kembali ke Manajemen Modul">
            <i class="fa-solid fa-arrow-left fa-lg"></i>
        </a>
        <div>
            <p class="text-xl font-semibold text-slate-700"><?php echo htmlspecialchars($kursus['judul']); ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- daftar materi -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-dark">Daftar Materi</h2>
                <a href="dashboard.php?page=tambah_materi&id_kursus=<?php echo $id_kursus; ?>" class="bg-[#6D00A8] text-white px-4 py-2 rounded-lg font-semibold text-sm flex items-center gap-2 hover:bg-[#6D00A8]-hover">
                    <i class="fa-solid fa-plus"></i> Tambah Materi
                </a>
            </div>
            <div class="space-y-3">
                <?php if (mysqli_num_rows($result_materi) > 0): ?>
                    <?php while ($materi = mysqli_fetch_assoc($result_materi)): ?>
                        <div class="flex justify-between items-center p-3 border rounded-lg hover:bg-slate-50">
                            <div class="flex items-center gap-4">
                                <span class="text-slate-400 font-bold w-6 text-center"><?php echo $materi['nomor_urut']; ?></span>
                                <i class="fa-solid fa-file-alt text-[#6D00A8]"></i>
                                <span class="font-medium text-slate-800"><?php echo htmlspecialchars($materi['judul']); ?></span>
                            </div>
                            <div class="space-x-3">
                                <a href="dashboard.php?page=edit_materi&id_materi=<?php echo $materi['id_materi']; ?>" title="Edit Materi" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pencil"></i></a>
                                <a href="dashboard.php?page=hapus_materi&aksi=proses&id_materi=<?php echo $materi['id_materi']; ?>" onclick="return confirm('Yakin hapus materi ini?');" title="Hapus Materi" class="text-danger hover:text-red-700"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-slate-500 py-4">Belum ada materi ditambahkan.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- daftar pertanyaan -->
        <div class="bg-white p-6 rounded-xl shadow-md">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-dark">Daftar Pertanyaan Kuis</h2>
                <a href="dashboard.php?page=tambah_pertanyaan&id_kursus=<?php echo $id_kursus; ?>" class="bg-[#6D00A8] text-white px-4 py-2 rounded-lg font-semibold text-sm flex items-center gap-2 hover:bg-[#6D00A8] transition-colors duration-300">
                    <i class="fa-solid fa-plus"></i> Tambah Pertanyaan
                </a>
            </div>
            <div class="space-y-3">
                <?php if (mysqli_num_rows($result_pertanyaan) > 0): ?>
                    <?php $no_soal = 1;
                    while ($soal = mysqli_fetch_assoc($result_pertanyaan)): ?>
                        <div class="flex justify-between items-center p-3 border rounded-lg hover:bg-slate-50">
                            <div class="flex items-center gap-4">
                                <span class="text-slate-400 font-bold w-6 text-center"><?php echo $no_soal++; ?></span>
                                <i class="fa-solid fa-question-circle text-yellow-500"></i>
                                <p class="font-medium text-slate-800 truncate max-w-xs">
                                    <?php echo htmlspecialchars($soal['teks_pertanyaan']); ?>
                                </p>
                            </div>
                            <div class="space-x-3">
                                <a href="dashboard.php?page=edit_pertanyaan&id_pertanyaan=<?php echo $soal['id_pertanyaan']; ?>" title="Edit Pertanyaan" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pencil"></i></a>
                                <a href="dashboard.php?page=hapus_pertanyaan&aksi=proses&id_pertanyaan=<?php echo $soal['id_pertanyaan']; ?>" onclick="return confirm('Yakin hapus pertanyaan ini beserta semua pilihan jawabannya?');" title="Hapus Pertanyaan" class="text-danger hover:text-red-700"><i class="fa-solid fa-trash"></i></a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-center text-slate-500 py-4">Belum ada pertanyaan kuis ditambahkan.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>