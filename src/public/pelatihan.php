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
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codemy</title>
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
    <div class="text-white pt-12 py-8">
        <div class="container mx-auto flex justify-end mb-8">
            <div class="relative w-96">
                <input type="text"
                    placeholder="Cari pelatihan..."
                    class="w-full px-4 py-3 pl-12 rounded-xl bg-white/10 backdrop-blur-md border border-white/30 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-[#FFB800] transition shadow-lg">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="w-5 h-5 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
        </div>
        <div class="container mx-auto mb-3">
            <h1 class="inline-block px-7 py-2 rounded-lg bg-[#6D00A8] text-white font-medium text-lg">Daftar Materi Pembelajaran</h1>
        </div>
    </div>

    <!-- Categories -->
    <div class="container mx-auto max-w-6xl py-8 px-4 mb-16">
        <nav class="flex justify-left border-b pb-[5px] border-[#fff] mb-12">
            <ul class="flex flex-wrap gap-8 text-lg font-medium">
                <?php if ($result_kategori && mysqli_num_rows($result_kategori) > 0): ?>
                    <?php while ($kategori = mysqli_fetch_assoc($result_kategori)): ?>
                        <li>
                            <a href="?kat=<?php echo $kategori['id_kategori']; ?>"
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
                                <?php
                                $deskripsi = $kursus['deskripsi'];
                                // Potong deskripsi jika lebih dari 80 karakter, lalu tambahkan '...'
                                echo htmlspecialchars(strlen($deskripsi) > 80 ? substr($deskripsi, 0, 80) . '...' : $deskripsi);
                                ?>
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

    <!-- Footer -->
    <?php include('./includes/footer.php') ?>
</body>

</html>