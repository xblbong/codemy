<?php
session_start();
require_once '../config/koneksi.php';

// Pastikan id_kursus ada dan valid
if (!isset($_GET['id_kursus']) || empty($_GET['id_kursus'])) {
    header("Location: dashboard.php?page=modul");
    exit;
}

$id_kursus = (int)$_GET['id_kursus'];

// Ambil data kursus untuk ditampilkan di judul halaman
$query_kursus = mysqli_query($koneksi, "SELECT judul FROM kursus WHERE id_kursus = $id_kursus");
if (mysqli_num_rows($query_kursus) == 0) {
    // Jika kursus tidak ditemukan, kembali ke halaman modul
    $_SESSION['pesan_error'] = "Kursus tidak ditemukan.";
    header("Location: dashboard.php?page=modul");
    exit;
}
$data_kursus = mysqli_fetch_assoc($query_kursus);
$judul_kursus = $data_kursus['judul'];

// --- LOGIKA UNTUK PROSES AKSI (HAPUS, SIMPAN, EDIT) ---

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// Proses hapus materi
if ($aksi == 'hapus_materi' && isset($_GET['id_materi'])) {
    $id_materi = (int)$_GET['id_materi'];
    mysqli_begin_transaction($koneksi);
    try {
        // Hapus dulu progress terkait materi ini
        mysqli_query($koneksi, "DELETE FROM progress_materi WHERE id_materi = $id_materi");
        // Hapus materi
        mysqli_query($koneksi, "DELETE FROM materi WHERE id_materi = $id_materi AND id_kursus = $id_kursus");
        
        mysqli_commit($koneksi);
        $_SESSION['pesan_sukses'] = "Materi berhasil dihapus.";
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan_error'] = "Gagal menghapus materi: " . $e->getMessage();
    }
    header("Location: kelola_modul.php?id_kursus=$id_kursus");
    exit;
}

// Proses hapus pertanyaan
if ($aksi == 'hapus_pertanyaan' && isset($_GET['id_pertanyaan'])) {
    $id_pertanyaan = (int)$_GET['id_pertanyaan'];
    mysqli_begin_transaction($koneksi);
    try {
        // Hapus dulu dari detail jawaban (jika ada yg pernah menjawab)
        mysqli_query($koneksi, "DELETE FROM detail_jawaban WHERE id_pertanyaan = $id_pertanyaan");
        // Hapus pilihan jawaban terkait
        mysqli_query($koneksi, "DELETE FROM pilihan_jawaban WHERE id_pertanyaan = $id_pertanyaan");
        // Hapus pertanyaan
        mysqli_query($koneksi, "DELETE FROM pertanyaan WHERE id_pertanyaan = $id_pertanyaan AND id_kursus = $id_kursus");

        mysqli_commit($koneksi);
        $_SESSION['pesan_sukses'] = "Pertanyaan kuis berhasil dihapus.";
    } catch (Exception $e) {
        mysqli_rollback($koneksi);
        $_SESSION['pesan_error'] = "Gagal menghapus pertanyaan: " . $e->getMessage();
    }
    header("Location: kelola_modul.php?id_kursus=$id_kursus#kuis"); // Arahkan ke tab kuis
    exit;
}


// --- AMBIL DATA UNTUK DITAMPILKAN ---
// Ambil daftar materi
$query_materi = mysqli_query($koneksi, "SELECT * FROM materi WHERE id_kursus = $id_kursus ORDER BY nomor_urut ASC");

// Ambil daftar pertanyaan dan pilihan jawabannya
$query_pertanyaan = mysqli_query($koneksi, "SELECT * FROM pertanyaan WHERE id_kursus = $id_kursus ORDER BY id_pertanyaan ASC");

// Ambil pesan dari session
if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    unset($_SESSION['pesan_sukses']);
}
if (isset($_SESSION['pesan_error'])) {
    $pesan_error = $_SESSION['pesan_error'];
    unset($_SESSION['pesan_error']);
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Modul</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Lexend', 'sans-serif'], },
                    colors: {
                        'codemy-purple': '#58287D', 'codemy-dark': '#31004C', 'codemy-black': '#0C0B17',
                        'codemy-yellow': '#FFB800', 'codemy-soft': '#F5F2FA', 'codemy-sidebar': '#E9DDF6',
                        'codemy-active': '#A259FF', 'success': '#00c217', 'danger': '#e11d48', 'light': '#f0e6f6',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-codemy-soft min-h-screen font-sans flex">
    <?php include('includes/sidebar.php'); ?>
    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
             <div class="flex items-center gap-4">
                <a href="dashboard.php?page=modul" class="text-codemy-purple hover:text-codemy-dark" title="Kembali ke Manajemen Modul">
                    <i class="fa-solid fa-arrow-left fa-lg"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-extrabold text-codemy-dark">Kelola Modul</h1>
                    <p class="text-slate-500">"<?php echo htmlspecialchars($judul_kursus); ?>"</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <?php include('includes/header.php'); ?>
            </div>
        </div>

        <!-- Tampilkan Pesan Sukses/Error -->
        <?php if (!empty($pesan_sukses)): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo $pesan_sukses; ?></p>
        </div>
        <?php endif; ?>
        <?php if (!empty($pesan_error)): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p><?php echo $pesan_error; ?></p>
        </div>
        <?php endif; ?>


        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Daftar Materi dan Kuis -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Bagian Materi -->
                <div class="bg-white p-6 rounded-xl shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-codemy-dark">Daftar Materi</h2>
                        <a href="form_materi.php?id_kursus=<?php echo $id_kursus; ?>&aksi=tambah" class="bg-codemy-purple text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-codemy-dark">
                            <i class="fa-solid fa-plus mr-2"></i>Tambah Materi
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-100 text-slate-600">
                                <tr>
                                    <th class="p-3 text-left rounded-l-lg">No Urut</th>
                                    <th class="p-3 text-left">Judul Materi</th>
                                    <th class="p-3 text-center rounded-r-lg">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($query_materi) > 0): ?>
                                    <?php while($materi = mysqli_fetch_assoc($query_materi)): ?>
                                    <tr class="border-b">
                                        <td class="p-3 font-medium text-center w-16"><?php echo $materi['nomor_urut']; ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($materi['judul']); ?></td>
                                        <td class="p-3 text-center space-x-2">
                                            <a href="form_materi.php?id_kursus=<?php echo $id_kursus; ?>&aksi=edit&id_materi=<?php echo $materi['id_materi']; ?>" class="text-blue-500 hover:text-blue-700" title="Edit Materi"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="kelola_modul.php?id_kursus=<?php echo $id_kursus; ?>&aksi=hapus_materi&id_materi=<?php echo $materi['id_materi']; ?>" onclick="return confirm('Yakin ingin menghapus materi ini? Progress siswa pada materi ini juga akan hilang.');" class="text-red-500 hover:text-red-700" title="Hapus Materi"><i class="fa-solid fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-slate-500 p-6">Belum ada materi di modul ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Bagian Kuis -->
                <div id="kuis" class="bg-white p-6 rounded-xl shadow-md">
                     <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-bold text-codemy-dark">Daftar Pertanyaan Kuis</h2>
                        <a href="form_kuis.php?id_kursus=<?php echo $id_kursus; ?>&aksi=tambah" class="bg-codemy-yellow text-codemy-dark px-4 py-2 rounded-lg text-sm font-semibold hover:opacity-80">
                            <i class="fa-solid fa-plus mr-2"></i>Tambah Pertanyaan
                        </a>
                    </div>
                    <div class="space-y-6">
                        <?php if (mysqli_num_rows($query_pertanyaan) > 0): $no_soal = 1; ?>
                            <?php while($pertanyaan = mysqli_fetch_assoc($query_pertanyaan)): ?>
                                <div class="border rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <p class="font-semibold text-gray-800"><?php echo $no_soal++; ?>. <?php echo nl2br(htmlspecialchars($pertanyaan['teks_pertanyaan'])); ?></p>
                                        <div class="space-x-3 flex-shrink-0 ml-4">
                                            <a href="form_kuis.php?id_kursus=<?php echo $id_kursus; ?>&aksi=edit&id_pertanyaan=<?php echo $pertanyaan['id_pertanyaan']; ?>" class="text-blue-500 hover:text-blue-700" title="Edit Pertanyaan"><i class="fa-solid fa-pencil"></i></a>
                                            <a href="kelola_modul.php?id_kursus=<?php echo $id_kursus; ?>&aksi=hapus_pertanyaan&id_pertanyaan=<?php echo $pertanyaan['id_pertanyaan']; ?>" onclick="return confirm('Yakin ingin menghapus pertanyaan ini? Seluruh pilihan jawaban dan riwayat jawaban siswa akan ikut terhapus.');" class="text-red-500 hover:text-red-700" title="Hapus Pertanyaan"><i class="fa-solid fa-trash"></i></a>
                                        </div>
                                    </div>
                                    <div class="mt-3 pl-5 space-y-2 text-sm">
                                        <?php 
                                        $id_p = $pertanyaan['id_pertanyaan'];
                                        $query_pilihan = mysqli_query($koneksi, "SELECT * FROM pilihan_jawaban WHERE id_pertanyaan = $id_p");
                                        $abjad = 'a';
                                        while($pilihan = mysqli_fetch_assoc($query_pilihan)):
                                        ?>
                                        <p class="<?php echo $pilihan['cek_jawaban'] ? 'text-green-600 font-bold' : 'text-gray-600'; ?>">
                                            <?php echo $abjad++; ?>. <?php echo htmlspecialchars($pilihan['teks_pilihan']); ?>
                                            <?php if($pilihan['cek_jawaban']) echo ' <i class="fa-solid fa-check-circle text-xs"></i>'; ?>
                                        </p>
                                        <?php endwhile; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center text-slate-500 p-6">Belum ada pertanyaan kuis di modul ini.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan: Ringkasan -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-xl shadow-md sticky top-10">
                    <h3 class="text-lg font-bold text-codemy-dark border-b pb-3 mb-4">Ringkasan Modul</h3>
                    <?php
                        // Hitung jumlah materi
                        $jumlah_materi = mysqli_num_rows($query_materi);
                        // Hitung jumlah pertanyaan kuis
                        $jumlah_pertanyaan = mysqli_num_rows($query_pertanyaan);
                        // Hitung jumlah peserta yang sudah mengerjakan
                        $result_peserta = mysqli_query($koneksi, "SELECT COUNT(DISTINCT id_pengguna) as total FROM riwayat_kuis WHERE id_kursus = $id_kursus");
                        $jumlah_peserta = mysqli_fetch_assoc($result_peserta)['total'];
                    ?>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-slate-600">Judul:</span>
                            <span class="font-semibold text-right"><?php echo htmlspecialchars($judul_kursus); ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Total Materi:</span>
                            <span class="font-semibold"><?php echo $jumlah_materi; ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-600">Total Soal Kuis:</span>
                            <span class="font-semibold"><?php echo $jumlah_pertanyaan; ?></span>
                        </div>
                         <div class="flex justify-between">
                            <span class="text-slate-600">Jumlah Peserta:</span>
                            <span class="font-semibold"><?php echo $jumlah_peserta; ?></span>
                        </div>
                    </div>
                     <a href="dashboard.php?page=modul" class="mt-6 w-full text-center block bg-slate-200 text-slate-800 px-4 py-2 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                        Selesai Mengelola
                    </a>
                </div>
            </div>
        </div>
    </main>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</body>
</html>