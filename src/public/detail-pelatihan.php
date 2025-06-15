<?php
session_start();
require_once '../config/koneksi.php';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id_kursus = (int)$_GET['id'];

$query_kursus = "
    SELECT 
        k.judul, 
        k.deskripsi, 
        k.gambar_banner, 
        p.nama AS pembuat, 
        kat.nama_kategori
    FROM 
        kursus k
    JOIN 
        pengguna p ON k.dibuat_oleh = p.id_pengguna
    LEFT JOIN 
        kategori kat ON k.id_kategori = kat.id_kategori
    WHERE 
        k.id_kursus = $id_kursus
";
$result_kursus = mysqli_query($koneksi, $query_kursus);
$kursus = mysqli_fetch_assoc($result_kursus);

if (!$kursus) {
    header("Location: index.php");
    exit();
}

$query_total_materi = "SELECT COUNT(*) AS total FROM materi WHERE id_kursus = $id_kursus";
$total_materi = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total_materi))['total'];

$query_ada_kuis = "SELECT COUNT(*) AS total FROM pertanyaan WHERE id_kursus = $id_kursus";
$ada_kuis = mysqli_fetch_assoc(mysqli_query($koneksi, $query_ada_kuis))['total'] > 0;

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelatihan</title>
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

<body>
    <?php include('./includes/header.php') ?>
    <div class="text-white py-16">
        <div class="container mx-auto">
            <!-- Gambar -->
            <div class="flex flex-col justify-center mb-8">
                <h1 class="capitalize text-3xl font-bold mb-8 text-left text-[#fff]"> <?php echo htmlspecialchars($kursus['judul']); ?></h1>
                <img src="../public/uploads/banners/<?php echo htmlspecialchars($kursus['gambar_banner']); ?>"
                    alt="Banner <?php echo htmlspecialchars($kursus['judul']); ?>"
                    class="w-full h-auto max-h-[400px] object-cover rounded-xl shadow-lg">
            </div>
            <div class="capitalize flex items-center gap-4 text-sm text-slate-300 mb-5">
                <span>Dibuat oleh <b><?php echo htmlspecialchars($kursus['pembuat']); ?></b></span>
                <span class="text-slate-500">|</span>
                <span class="bg-yellow-500/20 text-yellow-300 px-3 py-1 rounded-full">
                    <?php echo htmlspecialchars($kursus['nama_kategori'] ?? 'Umum'); ?>
                </span>
            </div>
            <div class="bg-white/10 border border-white/20 rounded-lg p-4 flex flex-wrap gap-6 items-center mt-4 mb-5">
                <div class="flex-1 min-w-[200px]">
                    <ul class="text-white text-sm space-y-1 list-disc list-inside">
                        <li>4 menit total video pembelajaran</li>
                        <li>5 bahan bacaan</li>
                        <li>5 konten dapat diunduh</li>
                        <li>Kuis yang dapat dikerjakan</li>
                    </ul>
                </div>
            </div>
            <div class="flex justify-end items-center gap-3 mb-8">
                <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-facebook"></i></a>
                <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-telegram"></i></a>
            </div>
            <p class="text-[#E0D7F3] text-sm leading-relaxed tracking-wide mb-6 border-y-2 border-white/20 py-4">
                <?php echo nl2br(htmlspecialchars($kursus['deskripsi'])); ?>
            </p>
            <div class="flex flex-col md:flex-row justify-end md:justify-end">
                <a href="modul-pelatihan.php?id=<?php echo $id_kursus; ?>" class="px-8 py-2 rounded-lg bg-[#A259FF] hover:bg-[#FFB800] text-white hover:text-[#31004C] font-semibold text-base transition shadow-md">Mulai Belajar</a>
            </div>
        </div>
    </div>
    <?php include('./includes/footer.php') ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</body>

</html>