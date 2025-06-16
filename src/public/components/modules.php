<?php
$user_progress = 0;
if ($id_pengguna > 0) {
    // Logika untuk menghitung progress pengguna
    $query_total = "SELECT COUNT(*) as total FROM materi WHERE id_kursus = $id_kursus";
    $total_materi_count = mysqli_fetch_assoc(mysqli_query($koneksi, $query_total))['total'];

    $query_selesai = "SELECT COUNT(*) as total FROM progress_materi WHERE id_pengguna = $id_pengguna AND id_materi IN (SELECT id_materi FROM materi WHERE id_kursus = $id_kursus)";
    $materi_selesai_count = mysqli_fetch_assoc(mysqli_query($koneksi, $query_selesai))['total'];

    $user_progress = ($total_materi_count > 0) ? ($materi_selesai_count / $total_materi_count) * 100 : 0;
}
$query_ada_kuis = "SELECT COUNT(*) AS total FROM pertanyaan WHERE id_kursus = $id_kursus";
$ada_kuis = mysqli_fetch_assoc(mysqli_query($koneksi, $query_ada_kuis))['total'] > 0;
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
    <!-- Sidebar Modul -->
    <aside class="w-full md:w-[300px] flex-shrink-0 border-2 border-gray-300 rounded-sm bg-[#31004C]">

        <!-- Progress Bar (Tetap sama) -->
        <div class="flex flex-col gap-2 mb-3 py-3 px-4">
            <h4 class="text-white font-semibold mb-2">Progress Belajar</h4>
            <!-- Progress bar hanya ditampilkan jika login, jika tidak, tampilkan ajakan login -->
            <?php if ($id_pengguna > 0): ?>
                <progress class="w-full h-2 [&::-webkit-progress-bar]:rounded [&::-webkit-progress-value]:rounded [&::-webkit-progress-bar]:bg-gray-300 [&::-webkit-progress-value]:bg-[#FFF]" value="<?php echo $user_progress; ?>" max="100"></progress>
            <?php else: ?>
                <a href="login.php" class="bg-yellow-500 text-black text-center text-sm font-semibold p-2 rounded-md hover:bg-yellow-400">Login untuk Simpan Progress</a>
            <?php endif; ?>
        </div>

        <!-- Daftar Materi Dinamis -->
        <div class="mb-3">
            <!-- Judul "Daftar Materi" bisa dibuat statis atau dinamis -->
            <h3 class="text-[#FFF] font-semibold mb-2 bg-[#6D00A8] rounded-sm py-3 px-4">Daftar Materi</h3>
            <div class="flex flex-col gap-2 py-3 px-4">
                <?php
                // Cek apakah ada materi yang ditemukan
                if ($result_all_materi && mysqli_num_rows($result_all_materi) > 0):
                    mysqli_data_seek($result_all_materi, 0); // Pastikan pointer kembali ke awal
                    // Loop untuk setiap materi dari database
                    while ($materi = mysqli_fetch_assoc($result_all_materi)):
                        // Tentukan apakah materi ini sedang aktif
                        $isActive = ($materi['id_materi'] == $id_materi_aktif);
                ?>
                        <!-- Tombol ini sekarang adalah link dinamis -->
                        <a href="?id=<?php echo $id_kursus; ?>&materi=<?php echo $materi['id_materi']; ?>"
                            class="text-left py-2 px-4 rounded cursor-pointer transition text-sm 
                              <?php
                                // Beri style berbeda jika materi aktif
                                if ($isActive) {
                                    echo 'bg-[#FFB800] text-codemy-dark font-bold'; // Style untuk yang aktif
                                } else {
                                    echo 'bg-[#fff] hover:bg-[#58287D] text-[#1A1732] hover:text-[#fff]'; // Style default
                                }
                                ?>">
                            <?php echo htmlspecialchars($materi['judul']); ?>
                        </a>
                    <?php
                    endwhile; // Akhir loop
                else:
                    ?>
                    <p class="text-slate-400 text-sm text-center">Belum ada materi untuk kursus ini.</p>
                <?php
                endif;
                ?>
            </div>
        </div>


        <!-- Final Quiz (Hanya ditampilkan jika kuis ada) -->
        <?php if ($ada_kuis): ?>
            <div class="mb-3">
                <h3 class="text-[#FFF] font-semibold mb-2 bg-[#6D00A8] rounded-sm py-3 px-4">Final Quiz</h3>
                <div class="flex flex-col gap-2 py-3 px-4">
                    <a href="quiz.php?id_kursus=<?php echo $id_kursus; ?>&aksi=mulai" onclick="return confirm('Memulai kuis akan me-reset progress kuis sebelumnya (jika ada). Lanjutkan?');" class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Quiz Akhir</a>
                </div>
            </div>
        <?php endif; ?>
    </aside>

</body>

</html>