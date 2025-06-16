    <?php
    session_start();
    require_once '../config/koneksi.php';

    // Pastikan pengguna sudah login
    if (!isset($_SESSION['id_pengguna'])) {
        header("Location: login.php");
        exit();
    }
    $id_pengguna = $_SESSION['id_pengguna'];


    // ambil data dasar pengguna
    $query_pengguna = "SELECT nama, email, dibuat_pada FROM pengguna WHERE id_pengguna = ?";
    $stmt_pengguna = mysqli_prepare($koneksi, $query_pengguna);
    mysqli_stmt_bind_param($stmt_pengguna, 'i', $id_pengguna);
    mysqli_stmt_execute($stmt_pengguna);
    $pengguna = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_pengguna));

    // ambil statistik umum
    // Total kursus yang diikuti (minimal pernah mengerjakan 1 materi atau kuis)
    $query_total_kursus = "
        SELECT COUNT(DISTINCT id_kursus) AS total
        FROM (
            -- Ambil id_kursus dari materi yang progress-nya ada
            SELECT m.id_kursus 
            FROM progress_materi pm
            JOIN materi m ON pm.id_materi = m.id_materi
            WHERE pm.id_pengguna = $id_pengguna
            
            UNION -- Gabungkan dengan id_kursus dari riwayat kuis
            
            SELECT id_kursus 
            FROM riwayat_kuis 
            WHERE id_pengguna = $id_pengguna
        ) AS kursus_diikuti
    ";
    $total_kursus_diikuti = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total_kursus))['total'];

    // Total sertifikat (kursus yang status kuisnya 'lulus')
    $query_sertifikat = "SELECT COUNT(DISTINCT id_kursus) AS total FROM riwayat_kuis WHERE id_pengguna = $id_pengguna AND status = 'lulus'";
    $total_sertifikat = mysqli_fetch_assoc(mysqli_query($koneksi, $query_sertifikat))['total'];

    // ambil detail progress per kursus
    // query ini sedikit kompleks, menggabungkan banyak data
    $query_progress = "
    SELECT 
        k.id_kursus,
        k.judul,
        k.gambar_banner,
        -- Hitung total materi dalam kursus
        (SELECT COUNT(*) FROM materi WHERE id_kursus = k.id_kursus) AS total_materi,
        -- Hitung materi yang sudah diselesaikan pengguna
        (SELECT COUNT(*) FROM progress_materi pm JOIN materi m ON pm.id_materi = m.id_materi WHERE pm.id_pengguna = $id_pengguna AND m.id_kursus = k.id_kursus) AS materi_selesai,
        -- Ambil skor kuis terakhir
        (SELECT skor FROM riwayat_kuis WHERE id_pengguna = $id_pengguna AND id_kursus = k.id_kursus ORDER BY skor DESC, waktu_pengerjaan DESC LIMIT 1) AS skor_tertinggi,
        -- [MODIFIKASI] Cek status kelulusan kuis terakhir (nilai tertinggi)
        (SELECT status FROM riwayat_kuis WHERE id_pengguna = $id_pengguna AND id_kursus = k.id_kursus ORDER BY skor DESC, waktu_pengerjaan DESC LIMIT 1) AS status_kuis
    FROM 
        kursus k
    -- Hanya tampilkan kursus yang pernah diikuti oleh pengguna (logika ini bisa disederhanakan)
    WHERE EXISTS (
        SELECT 1 FROM progress_materi pm JOIN materi m ON pm.id_materi = m.id_materi WHERE pm.id_pengguna = $id_pengguna AND m.id_kursus = k.id_kursus
    ) OR EXISTS (
        SELECT 1 FROM riwayat_kuis rk WHERE rk.id_pengguna = $id_pengguna AND rk.id_kursus = k.id_kursus
    )
    GROUP BY
        k.id_kursus
    ";
    $result_progress = mysqli_query($koneksi, $query_progress);
    ?>

    <!DOCTYPE html>
    <html lang="id" class="h-full">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Progress Belajar Saya - Codemy</title>
        <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
        <link rel="stylesheet" href="../css/style.css">
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

    <body class="bg-codemy-dark text-white flex flex-col min-h-full">
        <?php include 'includes/header.php'; ?>

        <main class="container mx-auto max-w-5xl py-12 px-4 flex-grow">
            <!-- header profil -->
            <div class="mt-16 flex flex-col md:flex-row items-center gap-6 mb-12">
                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($pengguna['nama']); ?>&size=96&background=A259FF&color=fff&bold=true" alt="Avatar" class="w-24 h-24 rounded-full border-4 border-codemy-yellow">
                <div>
                    <h1 class="text-3xl font-bold"><?php echo htmlspecialchars($pengguna['nama']); ?></h1>
                    <p class="text-slate-400">Bergabung sejak <?php echo date('F Y', strtotime($pengguna['dibuat_pada'])); ?></p>
                </div>
            </div>

            <!-- card Statistik Umum -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-12">
                <div class="bg-white/10 p-6 rounded-xl flex items-center gap-6">
                    <i class="fa-solid fa-book-bookmark text-4xl text-codemy-yellow"></i>
                    <div>
                        <p class="text-3xl font-bold"><?php echo $total_kursus_diikuti; ?></p>
                        <p class="text-slate-300">Kursus Diikuti</p>
                    </div>
                </div>
                <div class="bg-white/10 p-6 rounded-xl flex items-center gap-6">
                    <i class="fa-solid fa-certificate text-4xl text-codemy-yellow"></i>
                    <div>
                        <p class="text-3xl font-bold"><?php echo $total_sertifikat; ?></p>
                        <p class="text-slate-300">Sertifikat Diperoleh</p>
                    </div>
                </div>
            </div>

            <!-- daftar Kursus yang Diikuti -->
            <div>
                <h2 class="text-2xl font-bold mb-6">Kursus yang Sedang dan Telah Diikuti</h2>
                <div class="space-y-6">
    <?php if ($result_progress && mysqli_num_rows($result_progress) > 0): ?>
        <?php while ($progress = mysqli_fetch_assoc($result_progress)): ?>
            <?php
            // Hitung persentase progress materi
            $persentase = ($progress['total_materi'] > 0) ? ($progress['materi_selesai'] / $progress['total_materi']) * 100 : 0;
            
            // [LOGIKA BARU] Tentukan status kursus
            $semua_materi_selesai = ($progress['materi_selesai'] >= $progress['total_materi']);
            $kuis_lulus = ($progress['status_kuis'] == 'lulus');
            
            $status_kursus_selesai = ($semua_materi_selesai && $kuis_lulus);
            
            // Tentukan teks dan style tombol berdasarkan status
            if ($status_kursus_selesai) {
                $teks_tombol = "Lihat Sertifikat"; // Atau "Kursus Selesai"
                $style_tombol = "bg-green-600 hover:bg-green-700 text-white";
                $link_tombol = "sertifikat.php?id_kursus=" . $progress['id_kursus']; // Arahkan ke halaman sertifikat
            } else {
                $teks_tombol = "Lanjutkan Belajar";
                $style_tombol = "bg-codemy-yellow hover:bg-yellow-500 text-codemy-dark";
                $link_tombol = "modul-pelatihan.php?id=" . $progress['id_kursus'];
            }
            ?>
            <!-- Kartu Progress per Kursus -->
            <div class="bg-white/10 border border-white/20 rounded-xl p-6 flex flex-col md:flex-row items-center gap-6 relative">
                <!-- [BARU] Badge Selesai -->
                <?php if ($status_kursus_selesai): ?>
                    <div class="absolute top-0 right-0 bg-green-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-xl">
                        <i class="fa-solid fa-check-circle"></i> Selesai
                    </div>
                <?php endif; ?>
                
                <img src="../public/uploads/banners/<?php echo htmlspecialchars($progress['gambar_banner']); ?>" alt="Banner" class="w-full md:w-48 h-28 object-cover rounded-lg">
                <div class="flex-1 w-full">
                    <h3 class="font-bold text-lg"><?php echo htmlspecialchars($progress['judul']); ?></h3>
                    <!-- Progress Bar Materi -->
                    <div class="mt-2">
                        <div class="flex justify-between text-sm mb-1">
                            <span>Progress Materi</span>
                            <span class="font-semibold"><?php echo round($persentase); ?>%</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2.5">
                            <div class="bg-green-500 h-2.5 rounded-full" style="width: <?php echo $persentase; ?>%"></div>
                        </div>
                    </div>
                    <!-- Info Skor Kuis -->
                    <div class="text-sm mt-3">
                        <span>Skor Kuis Tertinggi: </span>
                        <span class="font-bold text-yellow-400"><?php echo $progress['skor_tertinggi'] !== null ? number_format($progress['skor_tertinggi'], 0) : 'Belum dikerjakan'; ?></span>
                    </div>
                </div>
                <!-- Tombol Dinamis -->
                <a href="<?php echo $link_tombol; ?>" class="<?php echo $style_tombol; ?> font-semibold px-6 py-2 rounded-lg w-full md:w-auto text-center transition-colors duration-300">
                    <?php echo $teks_tombol; ?>
                </a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="text-center py-16 bg-white/5 rounded-xl">
            <p class="text-slate-400">Anda belum mengikuti kursus apapun.</p>
            <a href="index.php" class="mt-4 inline-block text-codemy-yellow font-semibold">Mulai jelajahi kursus!</a>
        </div>
    <?php endif; ?>
</div>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>

    </html>