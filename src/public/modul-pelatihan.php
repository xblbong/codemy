<?php
session_start();
require_once '../config/koneksi.php';
// if (!isset($_SESSION['id_pengguna'])) {
//     header("Location: login.php");
//     exit();
// }
if (isset($_GET['aksi']) && $_GET['aksi'] == 'selesai') {
    
    // Cek jika pengguna login dan ada id_materi
    if (isset($_SESSION['id_pengguna']) && isset($_GET['id_materi'])) {
        $id_pengguna = (int)$_SESSION['id_pengguna'];
        $id_materi = (int)$_GET['id_materi'];

        // cek pengguna sudah pernah menyelesaikan materi ini
        $stmt_cek = mysqli_prepare($koneksi, "SELECT id_progress FROM progress_materi WHERE id_pengguna = ? AND id_materi = ?");
        mysqli_stmt_bind_param($stmt_cek, 'ii', $id_pengguna, $id_materi);
        mysqli_stmt_execute($stmt_cek);
        mysqli_stmt_store_result($stmt_cek);

        //klo belum bakal ada data progress baru
        if (mysqli_stmt_num_rows($stmt_cek) == 0) {
            $stmt_insert = mysqli_prepare($koneksi, "INSERT INTO progress_materi (id_pengguna, id_materi) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt_insert, 'ii', $id_pengguna, $id_materi);
            mysqli_stmt_execute($stmt_insert);
        }
        mysqli_stmt_close($stmt_cek);
    }

    // klo blum ambil url tujuan untuk redirect ke halaman selanjutnya
    $url_tujuan = isset($_GET['next']) ? urldecode($_GET['next']) : 'index.php';    
    header("Location: " . $url_tujuan);
    exit();
}

$id_pengguna = $_SESSION['id_pengguna'] ?? 0;
//untuk materi aktif
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}
$id_kursus = (int)$_GET['id'];

$query_all_materi = "SELECT id_materi, judul FROM materi WHERE id_kursus = $id_kursus ORDER BY nomor_urut ASC";
$result_all_materi = mysqli_query($koneksi, $query_all_materi);

$id_materi_aktif = 0;
if (isset($_GET['materi']) && is_numeric($_GET['materi'])) {
    $id_materi_aktif = (int)$_GET['materi'];
} else {
    // Jika tidak ada parameter, ambil materi pertama sebagai default
    if ($result_all_materi && mysqli_num_rows($result_all_materi) > 0) {
        $first_materi = mysqli_fetch_assoc($result_all_materi);
        $id_materi_aktif = $first_materi['id_materi'];
        mysqli_data_seek($result_all_materi, 0);
    }
}

$materi_aktif = null;
if ($id_materi_aktif > 0) {
    $query_active_materi = "SELECT * FROM materi WHERE id_materi = $id_materi_aktif AND id_kursus = $id_kursus";
    $result_active_materi = mysqli_query($koneksi, $query_active_materi);
    $materi_aktif = mysqli_fetch_assoc($result_active_materi);
}

// Ambil detail kursus utama
$query_kursus = "SELECT judul, gambar_banner FROM kursus WHERE id_kursus = $id_kursus";
$kursus = mysqli_fetch_assoc(mysqli_query($koneksi, $query_kursus));

if (!$kursus) {
    header("Location: index.php");
    exit();
}

// Ambil materi berikutnya
$nomor_urut_sekarang = 0;
$link_tujuan = '#'; // Link default jika ini materi terakhir dan tidak ada kuis
$teks_selanjutnya = 'Selesai Belajar'; // Teks default

if ($materi_aktif) {
    $nomor_urut_sekarang = $materi_aktif['nomor_urut'];
    
    // cari materi dengan nomor urut setelah nomor urut saat ini
    $query_materi_berikutnya = "SELECT id_materi FROM materi WHERE id_kursus = $id_kursus AND nomor_urut > $nomor_urut_sekarang ORDER BY nomor_urut ASC LIMIT 1";
    $result_materi_berikutnya = mysqli_query($koneksi, $query_materi_berikutnya);

    if ($materi_berikutnya = mysqli_fetch_assoc($result_materi_berikutnya)) {
        // klo ada ke materi selanjutnya
        $link_tujuan = "modul-pelatihan.php?id=$id_kursus&materi=" . $materi_berikutnya['id_materi'];
        $teks_selanjutnya = 'Lanjut ke Materi Berikutnya';
    } else {
        // klo materi selanjutnya tidak ada
        $query_cek_kuis = "SELECT COUNT(*) as total FROM pertanyaan WHERE id_kursus = $id_kursus";
        $ada_kuis = mysqli_fetch_assoc(mysqli_query($koneksi, $query_cek_kuis))['total'] > 0;
        
        if ($ada_kuis) {
            // Jika ada kuis, arahkan ke halaman kuis
            $link_tujuan = "quiz.php?id_kursus=$id_kursus";
            $teks_selanjutnya = 'Lanjut ke Kuis Akhir';
        }
    }
}

// Jika pengguna login, aksi akan menandai selesai. Jika tidak, langsung lanjut.
if ($id_pengguna > 0 && $materi_aktif) {
    $link_selanjutnya = "modul-pelatihan.php?aksi=selesai&id_materi=$id_materi_aktif&next=" . urlencode($link_tujuan);
} else {
    $link_selanjutnya = $link_tujuan;
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
                    <div class="bg-white/10 border border-white/20 p-6 rounded-xl min-h-full bg-white/10 border border-white/20 p-6 rounded-xl min-h-full">
                        <h2 class="text-2xl font-bold text-white mb-4"><?php echo htmlspecialchars($materi_aktif['judul'] ?? 'Selamat Datang'); ?></h2>
                        <div class="text-slate-300 leading-relaxed prose prose-invert">
                            <?php echo $materi_aktif ? nl2br(htmlspecialchars($materi_aktif['isi_materi'])) : 'Pilih materi di samping untuk memulai belajar.'; ?>
                        </div>

                        <div class="text-right mt-8">
                            <?php if ($materi_aktif): ?>
                                <a href="<?php echo $link_selanjutnya; ?>" class="bg-green-500 hover:bg-green-600 text-white font-semibold px-6 py-2 rounded-lg">
                                    <?php echo $teks_selanjutnya; ?> <i class="fa-solid fa-arrow-right ml-2"></i>
                                </a>
                            <?php endif; ?>
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