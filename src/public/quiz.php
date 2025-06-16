<?php
session_start();
require_once '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
$id_pengguna = $_SESSION['id_pengguna'];

// cek id_kursus dari URL
if (!isset($_GET['id_kursus']) || !is_numeric($_GET['id_kursus'])) {
    header("Location: index.php");
    exit();
}
$id_kursus = (int)$_GET['id_kursus'];

// Jika kuis baru dimulai (ditandai dengan parameter ?aksi=mulai atau sesi kuis belum ada)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'mulai' || !isset($_SESSION['kuis_berlangsung']) || $_SESSION['kuis_berlangsung']['id_kursus'] != $id_kursus) {

    //select semua pertanyaan untuk kuis ini
    $query_semua_id_soal = "SELECT id_pertanyaan FROM pertanyaan WHERE id_kursus = $id_kursus ORDER BY RAND()";
    $hasil_semua_id_soal = mysqli_query($koneksi, $query_semua_id_soal);

    $daftar_id_soal = [];
    if ($hasil_semua_id_soal) {
        while ($baris = mysqli_fetch_assoc($hasil_semua_id_soal)) {
            $daftar_id_soal[] = $baris['id_pertanyaan'];
        }
    }

    // Jika tidak ada soal sama sekali, kembalikan pengguna
    if (empty($daftar_id_soal)) {
        header("Location: modul-pelatihan.php?id=$id_kursus&status=no_quiz");
        exit();
    }

    $_SESSION['kuis_berlangsung'] = [
        'id_kursus'   => $id_kursus,
        'daftar_id_soal' => $daftar_id_soal, // Untuk menyimpan daftar id_soal
        'jawaban_pengguna' => [],           // Untuk menyimpan jawaban
        'soal_sekarang_idx' => 0,            // Mulai dari soal pertama (index 0)
        'waktu_mulai' => time()             // Catat waktu mulai
    ];
}

// Proses jawaban yang disubmit dari form
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_soal_sekarang'])) {
    $id_soal_sekarang = (int)$_POST['id_soal_sekarang'];
    $id_pilihan_jawaban = isset($_POST['jawaban']) ? (int)$_POST['jawaban'] : 0;

    // Simpan jawaban pengguna ke session
    $_SESSION['kuis_berlangsung']['jawaban_pengguna'][$id_soal_sekarang] = $id_pilihan_jawaban;

    // Logika navigasi
    if (isset($_POST['tombol_selanjutnya'])) {
        // Pindah ke soal berikutnya jika belum soal terakhir
        if ($_SESSION['kuis_berlangsung']['soal_sekarang_idx'] < count($_SESSION['kuis_berlangsung']['daftar_id_soal']) - 1) {
            $_SESSION['kuis_berlangsung']['soal_sekarang_idx']++;
        }
    } elseif (isset($_POST['tombol_navigasi_soal'])) {
        // Pindah ke soal yang diklik di navigasi kanan
        $_SESSION['kuis_berlangsung']['soal_sekarang_idx'] = (int)$_POST['tombol_navigasi_soal'];
    }
}

//untuk soal aktif saat ini
$index_soal_sekarang = $_SESSION['kuis_berlangsung']['soal_sekarang_idx'];
$id_soal_aktif = $_SESSION['kuis_berlangsung']['daftar_id_soal'][$index_soal_sekarang];

// Query untuk mengambil detail soal yang aktif
$query_soal_aktif = "SELECT * FROM pertanyaan WHERE id_pertanyaan = $id_soal_aktif";
$soal_aktif = mysqli_fetch_assoc(mysqli_query($koneksi, $query_soal_aktif));

// Query untuk mengambil pilihan jawaban dari soal yang aktif
$query_pilihan_jawaban = "SELECT * FROM pilihan_jawaban WHERE id_pertanyaan = $id_soal_aktif";
$hasil_pilihan_jawaban = mysqli_query($koneksi, $query_pilihan_jawaban);

// Query untuk info kursus
$query_kursus = "SELECT judul FROM kursus WHERE id_kursus = $id_kursus";
$kursus = mysqli_fetch_assoc(mysqli_query($koneksi, $query_kursus));

// Variabel bantu untuk tampilan
$total_soal = count($_SESSION['kuis_berlangsung']['daftar_id_soal']);
$apakah_soal_terakhir = ($index_soal_sekarang == $total_soal - 1);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
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

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#58287D] via-[#31004C] to-[#0C0B17] font-sans">

    <div class="w-full max-w-5xl mx-auto mt-12 p-4">
        <h2 class="text-xl md:text-2xl font-bold text-white mb-1">Kuis: <?php echo htmlspecialchars($kursus['judul']); ?></h2>
        <p class="text-[#E0D7F3] text-sm mb-6">Kerjakan soal berikut untuk menguji pemahamanmu tentang materi ini.</p>
        <form id="formKuis" action="quiz.php?id_kursus=<?php echo $id_kursus; ?>" method="POST">
            <input type="hidden" name="id_soal_sekarang" value="<?php echo $id_soal_aktif; ?>">
            <div class="bg-[#211B36] border border-[#A259FF] rounded-xl flex flex-col md:flex-row overflow-hidden shadow-lg">
                <!-- Kiri: Soal & Pilihan -->
                <div class="flex-1 p-8 flex flex-col justify-between">
                    <!-- Judul & Deskripsi Quiz -->
                    <div>
                        <div class="text-white text-base md:text-lg font-medium mb-4 flex items-start gap-2">
                            <span><?php echo $index_soal_sekarang + 1; ?>.</span>
                            <span><?php echo nl2br(htmlspecialchars($soal_aktif['teks_pertanyaan'])); ?>
                        </div>
                        <div class="flex flex-col gap-3 mt-6">
                            <?php
                            $jawaban_tersimpan = $_SESSION['kuis_berlangsung']['jawaban_pengguna'][$id_soal_aktif] ?? 0;
                            while ($pilihan = mysqli_fetch_assoc($hasil_pilihan_jawaban)):
                            ?>
                                <label class="flex items-center gap-2 cursor-pointer text-white text-base">
                                    <input type="radio" name="jawaban" value="<?php echo $pilihan['id_pilihan']; ?>" <?php if ($pilihan['id_pilihan'] == $jawaban_tersimpan) echo 'checked'; ?> class="accent-[#A259FF]" />
                                    <span><?php echo htmlspecialchars($pilihan['teks_pilihan']); ?></span>
                                </label>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <div class="flex justify-end mt-8">
                        <?php if (!$apakah_soal_terakhir): ?>
                            <button type="submit" name="tombol_selanjutnya" value="1" class="flex items-center gap-2 bg-white text-[#31004C] px-4 py-1.5 rounded font-semibold text-sm hover:bg-[#A259FF] hover:text-white transition">
                                Selanjutnya <i class="fa-solid fa-arrow-right"></i>
                            </button>
                        <?php else: ?>
                            <!-- Tombol Finish hanya muncul di soal terakhir -->
                            <button type="submit" formaction="skor.php" onclick="return confirm('Apakah Anda yakin ingin menyelesaikan kuis?');" class="flex items-center gap-2 bg-green-500 text-white px-4 py-1.5 rounded font-semibold text-sm hover:bg-green-600 transition">
                                Finish <i class="fa-solid fa-check-circle"></i>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Kanan: Timer & Nomor Soal -->
                <div class="w-full md:w-64 border-l border-[#A259FF] flex flex-col items-center justify-start p-8 bg-[#211B36]">
                    <div class="text-center mb-4">
                        <p class="text-sm text-slate-400">Sisa Waktu</p>
                        <div id="timer" class="text-white text-2xl font-bold">05.00</div>
                    </div>
                    <div class="grid grid-cols-4 gap-2">
                        <?php foreach ($_SESSION['kuis_berlangsung']['daftar_id_soal'] as $index => $id_soal_nav): ?>
                            <?php
                            $sudah_dijawab = isset($_SESSION['kuis_berlangsung']['jawaban_pengguna'][$id_soal_nav]) && $_SESSION['kuis_berlangsung']['jawaban_pengguna'][$id_soal_nav] != 0;
                            $sedang_aktif = ($index == $index_soal_sekarang);

                            $class_tombol = 'bg-transparent text-white border-white/30';
                            if ($sedang_aktif) $class_tombol = 'bg-yellow-500 text-black border-yellow-500';
                            elseif ($sudah_dijawab) $class_tombol = 'bg-blue-500 text-white border-blue-500';
                            ?>
                            <button type="submit" name="tombol_navigasi_soal" value="<?php echo $index; ?>" class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold <?php echo $class_tombol; ?>"> <?php echo $index + 1; ?></button><?php endforeach; ?>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const waktuMulai = <?php echo time(); ?>; // dari PHP
        const durasiKuis = 10; // 5 menit
        const elemenTimer = document.getElementById('timer');

        function updateTimer() {
            const waktuSekarang = Math.floor(Date.now() / 1000);
            const waktuBerlalu = waktuSekarang - waktuMulai;
            const sisaWaktu = durasiKuis - waktuBerlalu;

            if (sisaWaktu <= 0) {
                elemenTimer.textContent = "00:00";
                document.getElementById('formKuis').setAttribute('action', 'skor.php');
                document.getElementById('formKuis').submit();
                clearInterval(timerInterval);
                return;
            }

            const menit = Math.floor(sisaWaktu / 60);
            const detik = sisaWaktu % 60;
            elemenTimer.textContent = String(menit).padStart(2, '0') + ":" + String(detik).padStart(2, '0');
        }

        const timerInterval = setInterval(updateTimer, 1000);
        updateTimer();
    </script>
</body>

</html>