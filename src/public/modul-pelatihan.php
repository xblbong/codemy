<?php
session_start();
require_once '../config/koneksi.php';
// if (!isset($_SESSION['id_pengguna'])) {
//     header("Location: login.php");
//     exit();
// }
$id_pengguna = $_SESSION['id_pengguna'] ?? 0;
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id_kursus = (int)$_GET['id'];

$query_all_materi = "SELECT id_materi, judul FROM materi WHERE id_kursus = $id_kursus ORDER BY nomor_urut ASC";
$result_all_materi = mysqli_query($koneksi, $query_all_materi);

$id_materi_aktif = 0;
if (isset($_GET['materi'])) {
    $id_materi_aktif = (int)$_GET['materi'];
} else {
    // Jika tidak ada parameter, ambil materi pertama sebagai default
    $first_materi = mysqli_fetch_assoc($result_all_materi);
    if ($first_materi) {
        $id_materi_aktif = $first_materi['id_materi'];
        mysqli_data_seek($result_all_materi, 0); // Kembalikan pointer
    }
}

$materi_aktif = null;
if ($id_materi_aktif > 0) {
    $query_active_materi = "SELECT * FROM materi WHERE id_materi = $id_materi_aktif AND id_kursus = $id_kursus";
    $result_active_materi = mysqli_query($koneksi, $query_active_materi);
    $materi_aktif = mysqli_fetch_assoc($result_active_materi);
}

$query_kursus = "SELECT judul, gambar_banner FROM kursus WHERE id_kursus = $id_kursus";
$kursus = mysqli_fetch_assoc(mysqli_query($koneksi, $query_kursus));

// Jika kursus tidak ditemukan, kembali
if (!$kursus) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($materi_aktif['judul'] ?? $kursus['judul']); ?> - Codemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-blue': '#1a365d',
                    },
                    fontFamily: {
                        sans: ['Lexend', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>

<body>
    <?php include('./includes/header.php') ?>

    <!-- Hero Section -->
    <div class="text-white py-16">
        <div class="container mx-auto">
            <!-- Gambar -->
            <div class="flex flex-col justify-center mb-8">
                <h1 class="text-3xl font-bold mb-8 text-left text-[#fff]"><?php echo htmlspecialchars($kursus['judul']); ?></h1>
                <div class="aspect-video bg-codemy-black rounded-xl">
                    <?php if ($materi_aktif && !empty($materi_aktif['url_video'])): ?>
                        <?php
                        $youtube_url = $materi_aktif['url_video'];
                        $video_id = '';

                        // Cek apakah URL adalah format 'watch?v='
                        if (preg_match('/watch\?v=([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        }
                        // Cek apakah URL adalah format pendek 'youtu.be/'
                        elseif (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $youtube_url, $matches)) {
                            $video_id = $matches[1];
                        }

                        // Jika kita berhasil mendapatkan ID video, buat URL embed
                        if (!empty($video_id)) {
                            $embed_url = "https://www.youtube.com/embed/" . $video_id;
                        ?>
                            <iframe class="w-full h-full rounded-xl"
                                src="<?php echo htmlspecialchars($embed_url); ?>"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen>
                            </iframe>
                        <?php
                        } else {
                            // Jika format URL tidak dikenali, tampilkan pesan error atau banner
                            echo "<div class='flex items-center justify-center h-full text-white/50'>Format URL video tidak valid.</div>";
                        }
                        ?>
                    <?php else: ?>
                        <!-- Fallback jika tidak ada video: tampilkan banner kursus -->
                        <img src="../public/uploads/banners/<?php echo htmlspecialchars($kursus['gambar_banner']); ?>"
                            alt="Banner <?php echo htmlspecialchars($kursus['judul']); ?>"
                            class="w-full h-full object-cover rounded-xl">
                    <?php endif; ?>
                </div>
            </div>
            <div class="flex flex-col md:flex-row gap-6">
                <!-- Sidebar Modul -->
                <?php include('./components/modules.php') ?>
                <!-- Main Content -->
                <main class="flex-1">
                    <div class="bg-white/10 border border-white/20 p-6 rounded-xl min-h-full">
                        <h2 class="text-2xl font-bold text-white mb-4"><?php echo htmlspecialchars($materi_aktif['judul'] ?? 'Selamat Datang'); ?></h2>
                        <div class="text-slate-300 leading-relaxed prose prose-invert">
                            <?php echo $materi_aktif ? nl2br(htmlspecialchars($materi_aktif['isi_materi'])) : 'Pilih materi di samping untuk memulai belajar.'; ?>
                        </div>

                        <!-- Tombol Tandai Selesai -->
                        <div class="text-right mt-8">
                            <a href="actions/tandai_selesai.php?id_materi=<?php echo $id_materi_aktif; ?>" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg">
                                Tandai Selesai & Lanjut <i class="fa-solid fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </main>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include('./includes/footer.php') ?>
</body>

</html>