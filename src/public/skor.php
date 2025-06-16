<?php
session_start();
require_once '../config/koneksi.php';

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
$id_pengguna = $_SESSION['id_pengguna'];

// 2. Cek apakah ada parameter id_riwayat di URL
if (!isset($_GET['id_riwayat']) || !is_numeric($_GET['id_riwayat'])) {
    // Jika tidak ada, lempar ke halaman utama atau daftar kursus
    header("Location: index.php");
    exit();
}
$id_riwayat = (int)$_GET['id_riwayat'];

// 3. (PALING PENTING) Ambil data riwayat dan pastikan itu milik pengguna yang login
$query = "
    SELECT 
        rk.skor, 
        rk.status, 
        rk.waktu_pengerjaan,
        p.nama AS nama_pengguna,    -- Mengambil nama dari tabel pengguna
        k.judul AS judul_kursus,    -- Mengambil judul dari tabel kursus
        k.id_kursus
    FROM riwayat_kuis rk
    JOIN pengguna p ON rk.id_pengguna = p.id_pengguna
    JOIN kursus k ON rk.id_kursus = k.id_kursus
    WHERE rk.id_riwayat = ? AND rk.id_pengguna = ?
";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, 'ii', $id_riwayat, $id_pengguna);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$riwayat = mysqli_fetch_assoc($result);

// 4. Jika data tidak ditemukan, lempar ke halaman utama
if (!$riwayat) {
    header("Location: index.php?status=not_found");
    exit();
}

// [PERBAIKAN 2] Ambil semua data yang dibutuhkan dari array $riwayat ke variabel
$skor = $riwayat['skor'];
$status = $riwayat['status'];
$nama_pengguna = $riwayat['nama_pengguna']; // Variabel ini sekarang sudah ada
$judul_kursus = $riwayat['judul_kursus'];   // Variabel ini juga sudah ada
$id_kursus = $riwayat['id_kursus'];
$waktu_pengerjaan = $riwayat['waktu_pengerjaan'];
$warna_status = ($status == 'lulus') ? "text-green-400" : "text-red-400";
$pesan = ($status == 'lulus') ? "Selamat, kamu berhasil!" : "Jangan menyerah, ayo coba lagi!";

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Lexend', 'sans-serif'],
                    },
                    colors: {
                        'codemy-purple': '#58287D',
                        'codemy-dark': '#31004C',
                    }
                }
            }
        }
    </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-codemy-purple to-codemy-dark font-sans">
    <div class="bg-[#211B36] border border-[#A259FF] rounded-xl p-8 max-w-lg w-full text-center shadow-2xl relative">
            
        <p class="text-white text-xs mb-25">Tanggal ujian: <span class="text-yellow-400"><?php echo date('d F Y \p\u\k\u\l H:i', strtotime($riwayat['waktu_pengerjaan'])); ?></span></p>
        <h1 class="text-2xl font-bold text-white mb-2 mt-5">Hasil Kuis</h1>
        <p class="text-gray-300 mb-6"><?php echo htmlspecialchars($nama_pengguna); ?></p>

        <div class="my-8">
            <p class="text-gray-400 text-sm">Skor Kamu</p>
            <p class="text-6xl font-bold text-white"><?php echo round($skor); ?></p>
            <p class="text-xl font-semibold <?php echo $warna_status; ?> mt-2"><?php echo str_replace('_', ' ', ucfirst($status)); ?></p>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="modul-pelatihan.php?id=<?php echo $id_kursus; ?>" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg transition duration-300">
                Kembali ke Materi
            </a>
        </div>
    </div>
</body>
</html>