<?php
session_start();
require_once '../config/koneksi.php'; // Pastikan path ini benar

//cek apakah pengguna sudah login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
$id_pengguna = $_SESSION['id_pengguna'];

// 2. Validasi ID Riwayat dari URL
if (!isset($_GET['id_riwayat']) || !is_numeric($_GET['id_riwayat'])) {
    header("Location: index.php"); 
    exit();
}
$id_riwayat = (int)$_GET['id_riwayat'];

// 3. Ambil data riwayat utama dari database
// Query ini juga memeriksa apakah riwayat ini milik pengguna yang sedang login
$query_riwayat = "SELECT * FROM riwayat_kuis WHERE id_riwayat = ? AND id_pengguna = ?";
$stmt_riwayat = mysqli_prepare($koneksi, $query_riwayat);
mysqli_stmt_bind_param($stmt_riwayat, 'ii', $id_riwayat, $id_pengguna);
mysqli_stmt_execute($stmt_riwayat);
$result_riwayat = mysqli_stmt_get_result($stmt_riwayat);
$riwayat = mysqli_fetch_assoc($result_riwayat);

// Jika riwayat tidak ditemukan (ID salah atau bukan milik user), kembalikan
// if (!$riwayat) {
//     header("Location: index.php");
//     exit();
// }

// 4. Ambil data 'Total Soal' dengan menghitung dari detail_jawaban
$query_total_soal = "SELECT COUNT(*) AS total FROM detail_jawaban WHERE id_riwayat = ?";
$stmt_total_soal = mysqli_prepare($koneksi, $query_total_soal);
mysqli_stmt_bind_param($stmt_total_soal, 'i', $id_riwayat);
mysqli_stmt_execute($stmt_total_soal);
$result_total_soal = mysqli_stmt_get_result($stmt_total_soal);
$total_soal = mysqli_fetch_assoc($result_total_soal)['total'];

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Skor Quiz</title>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
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
                        'codemy-black': '#0C0B17',
                        'codemy-yellow': '#FFB800',
                    }
                }
            }
        }
    </script>
</head>

<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#58287D] via-[#31004C] to-[#0C0B17] font-sans">
    <div class="flex-1 flex items-center justify-center py-16">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-[#211B36] border border-[#A259FF] rounded-xl p-8 md:p-12 flex flex-col items-center shadow-lg">
                <div class="w-full text-center mb-8">
                    <span class="text-white text-base md:text-lg">Tanggal Ujian
                         <span class="ml-2 text-[#FFB800] font-semibold">
                            <?php echo date('d F Y, H:i', strtotime($riwayat['waktu_pengerjaan'])); ?>
                        </span>
                    </span>
                </div>
                <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-0 text-center">
                    <div>
                        <div class="text-[#E0D7F3] text-lg md:text-xl mb-2">Total Soal</div>
                        <div class="text-white text-5xl md:text-6xl font-bold">
                            <?php echo $total_soal; ?>
                        </div>
                    </div>
                    <div>
                        <div class="text-[#E0D7F3] text-lg md:text-xl mb-2">Skor</div>
                        <div class="text-white text-5xl md:text-6xl font-bold">
                            <?php echo number_format($riwayat['skor'], 0); ?>
                        </div>
                    </div>
                </div>
                <div class="mt-12">
                    <a href="index.php" class="px-8 py-3 rounded-lg bg-[#A259FF] hover:bg-[#FFB800] text-white hover:text-[#31004C] font-semibold text-base transition shadow-md">
                        Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>