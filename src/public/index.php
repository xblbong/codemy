<?php
session_start();
require_once '../config/koneksi.php';

$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);
$kategori_aktif_id = null;
if (isset($_GET['kat'])) {
    $kategori_aktif_id = (int)$_GET['kat'];
} else {
    // ambil id kategori pertama dari hasil query
    $first_category = mysqli_fetch_assoc($result_kategori);
    if ($first_category) {
        $kategori_aktif_id = $first_category['id_kategori'];
        // Kembalikan pointer ke awal agar loop nanti bisa berjalan normal
        mysqli_data_seek($result_kategori, 0);
    }
}

$where_clause = "";
if ($kategori_aktif_id) {
    $where_clause = "WHERE k.id_kategori = " . $kategori_aktif_id;
}

$query_kursus = "
    SELECT 
        k.id_kursus, 
        k.judul, 
        k.deskripsi, 
        k.gambar_banner, 
        kat.nama_kategori,
         p.nama AS pembuat
    FROM 
        kursus k
     JOIN 
        pengguna p ON k.dibuat_oleh = p.id_pengguna
        left join
        kategori kat ON k.id_kategori = kat.id_kategori
    $where_clause
    ORDER BY k.judul ASC
";
$result_kursus = mysqli_query($koneksi, $query_kursus);
?>

<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
        <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 mb-12 gap-x-16 items-center">
            <img src="uploads/foto/18907.png" alt="hero-image" class="w-full h-full object-cover container">
            <div>
                <h1 class="text-4xl font-bold mb-6">Hello, Welcome To Codemy!</h1>
                <p class="mb-4 font-light">Platform pelatihan coding dan IT terbaik nomer 1 di Indonesia.
                    Ayo! mulai perjalananmu dengan codemy untuk membantu melatih hard skill di bidang Teknologi Digital.</p>
            </div>
        </div>
    </div>

    <!-- Categories -->
    <div class="container mx-auto max-w-6xl py-8 px-4 mb-16">
        <nav class="flex justify-left border-b pb-[5px] border-[#fff] mb-12">
            <ul class="flex flex-wrap gap-8 text-lg font-medium">
                <?php if ($result_kategori && mysqli_num_rows($result_kategori) > 0): ?>
                    <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                        <li>
                            <a href="index.php?kat=<?php echo $kategori['id_kategori']; ?>"
                                class="<?php
                                        if ($kategori['id_kategori'] == $kategori_aktif_id) {
                                            echo 'capitalize text-[#FFB800] font-bold border-b-2 border-[#FFB800]';
                                        } else {
                                            echo 'capitalize font-light text-white hover:text-[#FFB800]';
                                        }
                                        ?> pb-2 transition">
                                <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                            </a>
                        </li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-12 gap-y-8 mb-7">
            <?php
            // Cek apakah query berhasil dan ada data kursus yang ditemukan
            if ($result_kursus && mysqli_num_rows($result_kursus) > 0):
                // Loop melalui setiap baris data kursus
                while ($kursus = mysqli_fetch_assoc($result_kursus)):
            ?>
                    <!-- Card Dinamis -->
                    <div class="bg-gradient-to-b from-[#2B174C] to-[#31004C] rounded-2xl p-4 hover:shadow-2xl hover:shadow-[#7F60A8] flex flex-col transition-all duration-300 transform hover:-translate-y-1">

                        <!-- Gambar Banner Dinamis -->
                        <img src="../public/uploads/banners/<?php echo htmlspecialchars($kursus['gambar_banner']); ?>"
                            alt="Banner <?php echo htmlspecialchars($kursus['judul']); ?>"
                            class="w-full h-40 object-cover rounded-xl mb-4">

                        <div class="px-2 flex flex-col flex-grow">
                            <!-- Nama Kategori Dinamis -->
                            <span class="text-xs font-bold text-[#FFB800] mb-2 uppercase">
                                <?php echo htmlspecialchars($kursus['nama_kategori'] ?? 'Umum'); ?>
                            </span>

                            <!-- Judul Kursus Dinamis -->
                            <h3 class="font-bold text-white text-lg mb-1 leading-tight">
                                <?php echo htmlspecialchars($kursus['judul']); ?>
                            </h3>

                            <!-- Deskripsi Dinamis (Dipotong) -->
                            <p class="text-[#E0D7F3] text-sm mb-4">
                               
                            </p>

                            <!-- Bagian Bawah Kartu -->
                            <span class="capitalize text-xs text-[#E0D7F3] flex items-center gap-1">
                                <i class="fa-solid fa-book"></i> dibuat oleh:
                            </span>
                            <div class="flex justify-between items-center">
                                <span class="capitalize text-xs text-[#E0D7F3] flex items-center gap-1">
                                    <i class="fa-solid fa-book"></i> <?php echo htmlspecialchars($kursus['pembuat'] ?? 'Umum'); ?>
                                </span>
                                <!-- Link Dinamis ke Halaman Detail -->
                                <a href="detail-pelatihan.php?id=<?php echo $kursus['id_kursus']; ?>"
                                    class="px-4 py-1 rounded bg-[#A259FF] text-white font-semibold text-sm hover:bg-[#FFB800] transition">
                                    Mulai Belajar
                                </a>
                            </div>
                        </div>
                    </div>
                <?php
                endwhile; // Akhir dari loop while
            else:
                ?>
                <!-- Tampilan jika tidak ada kursus yang ditemukan -->
                <div class="col-span-1 md:col-span-3 text-center py-16">
                    <i class="fa-solid fa-folder-open text-4xl text-white/50 mb-4"></i>
                    <p class="text-white text-lg">Belum ada kursus untuk kategori ini.</p>
                </div>
            <?php
            endif;
            ?>

        </div>
    </div>

    <!-- Trusted By Section -->
    <div class="w-full flex flex-col items-center mb-16">
        <span class="text-center text-md text-white mb-3">Dipercaya Oleh</span>
        <div class="bg-white rounded-2xl px-8 py-16 flex flex-wrap justify-center items-center gap-10 shadow-lg w-full max-w-4xl">
            <img src="uploads/foto/google.png" alt="Google" class="h-16 object-contain">
            <img src="uploads/foto/microsoft.png" alt="Microsoft" class="h-16 object-contain">
            <img src="uploads/foto/aws.png" alt="AWS" class="h-16 object-contain">
            <img src="uploads/foto/bangkit.png" alt="Bangkit" class="h-16 object-contain">
        </div>
    </div>

    <!-- Footer -->
    <?php include('./includes/footer.php') ?>
</body>

</html>