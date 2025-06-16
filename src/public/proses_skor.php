<?php
session_start();
require_once '../config/koneksi.php';

// Enable error reporting untuk debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Cek login
if (!isset($_SESSION['id_pengguna'])) {
    header("Location: login.php");
    exit();
}
$id_pengguna = $_SESSION['id_pengguna'];

// Cek apakah ada sesi kuis berlangsung
if (!isset($_SESSION['kuis_berlangsung'])) {
    // Redirect ke halaman kursus jika tidak ada sesi, mungkin lebih baik daripada halaman error
    header("Location: kursus.php?status=no_quiz_session");
    exit();
}

$kuis_data = $_SESSION['kuis_berlangsung'];
$id_kursus = $kuis_data['id_kursus'];
$jawaban_pengguna = $kuis_data['jawaban_pengguna'];
$daftar_id_soal = $kuis_data['daftar_id_soal'];

// [PENTING] Proses jawaban terakhir jika ada yang dikirim melalui POST dari halaman quiz.php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_soal_sekarang'])) {
    // Cek jika ada jawaban yang dipilih
    if (isset($_POST['jawaban'])) {
        $id_soal_terakhir = (int)$_POST['id_soal_sekarang'];
        $jawaban_terakhir = (int)$_POST['jawaban'];
        $_SESSION['kuis_berlangsung']['jawaban_pengguna'][$id_soal_terakhir] = $jawaban_terakhir;
        $jawaban_pengguna = $_SESSION['kuis_berlangsung']['jawaban_pengguna']; // Update variabel lokal
    }
}

// Hitung skor
$total_soal = count($daftar_id_soal);
$jumlah_benar = 0;

// Mulai transaksi
mysqli_begin_transaction($koneksi);

try {
    // 1. Simpan ke tabel riwayat_kuis dengan skor sementara 0
    $query_riwayat = "INSERT INTO riwayat_kuis (id_pengguna, id_kursus, skor, status, waktu_pengerjaan) VALUES (?, ?, 0, 'tidak_lulus', NOW())";
    $stmt_riwayat = mysqli_prepare($koneksi, $query_riwayat);
    
    if (!$stmt_riwayat) {
        throw new Exception("Error preparing statement (riwayat_kuis): " . mysqli_error($koneksi));
    }
    
    mysqli_stmt_bind_param($stmt_riwayat, 'ii', $id_pengguna, $id_kursus);
    
    if (!mysqli_stmt_execute($stmt_riwayat)) {
        throw new Exception("Error executing riwayat query: " . mysqli_stmt_error($stmt_riwayat));
    }
    
    $id_riwayat = mysqli_insert_id($koneksi);

    // 2. Proses setiap jawaban dan simpan ke detail_jawaban
    foreach ($daftar_id_soal as $id_soal) {
        // [PERBAIKAN 1] Gunakan NULL jika jawaban tidak ada, bukan 0.
        // Null-coalescing operator (??) adalah cara singkat untuk ini.
        $id_pilihan_dipilih = $jawaban_pengguna[$id_soal] ?? NULL;
        
        // Ambil jawaban yang benar untuk soal ini
        $query_jawaban_benar = "SELECT id_pilihan FROM pilihan_jawaban WHERE id_pertanyaan = ? AND cek_jawaban = 1";
        $stmt_jawaban_benar = mysqli_prepare($koneksi, $query_jawaban_benar);
        
        if (!$stmt_jawaban_benar) {
            throw new Exception("Error preparing jawaban benar statement: " . mysqli_error($koneksi));
        }
        
        mysqli_stmt_bind_param($stmt_jawaban_benar, 'i', $id_soal);
        mysqli_stmt_execute($stmt_jawaban_benar);
        $result_jawaban_benar = mysqli_stmt_get_result($stmt_jawaban_benar);
        $jawaban_benar_row = mysqli_fetch_assoc($result_jawaban_benar);
        
        $is_correct = 0;
        // [PERBAIKAN 2] Cek apakah jawaban pengguna sama dengan jawaban yang benar.
        // Hanya hitung benar jika pengguna menjawab ($id_pilihan_dipilih tidak NULL).
        if ($jawaban_benar_row && $id_pilihan_dipilih !== NULL && $id_pilihan_dipilih == $jawaban_benar_row['id_pilihan']) {
            $is_correct = 1;
            $jumlah_benar++;
        }
        
        // Simpan detail jawaban
        $query_detail = "INSERT INTO detail_jawaban (id_riwayat, id_pertanyaan, id_pilihan_dipilih, is_correct) VALUES (?, ?, ?, ?)";
        $stmt_detail = mysqli_prepare($koneksi, $query_detail);
        
        if (!$stmt_detail) {
            throw new Exception("Error preparing detail statement: " . mysqli_error($koneksi));
        }
        
        // Tipe 'i' untuk id_pilihan_dipilih akan otomatis menangani nilai NULL dengan benar.
        mysqli_stmt_bind_param($stmt_detail, 'iiii', $id_riwayat, $id_soal, $id_pilihan_dipilih, $is_correct);
        
        if (!mysqli_stmt_execute($stmt_detail)) {
            // Jika ada error di sini, kemungkinan karena FK constraint.
            throw new Exception("Error executing detail query for soal $id_soal: " . mysqli_stmt_error($stmt_detail));
        }
    }
    
    // 3. Hitung dan update skor final serta status
    $skor_final = ($total_soal > 0) ? ($jumlah_benar / $total_soal) * 100 : 0;
    $status = ($skor_final >= 70) ? 'lulus' : 'tidak_lulus'; // Batas kelulusan 70
    
    $query_update_skor = "UPDATE riwayat_kuis SET skor = ?, status = ? WHERE id_riwayat = ?";
    $stmt_update_skor = mysqli_prepare($koneksi, $query_update_skor);
    
    if (!$stmt_update_skor) {
        throw new Exception("Error preparing update statement: " . mysqli_error($koneksi));
    }
    
    mysqli_stmt_bind_param($stmt_update_skor, 'dsi', $skor_final, $status, $id_riwayat);
    
    if (!mysqli_stmt_execute($stmt_update_skor)) {
        throw new Exception("Error executing update query: " . mysqli_stmt_error($stmt_update_skor));
    }
    
    // Commit transaksi
    mysqli_commit($koneksi);
    
    // Hapus sesi kuis
    unset($_SESSION['kuis_berlangsung']);
    
    // Redirect ke halaman skor dengan id_riwayat
    header("Location: skor.php?id_riwayat=" . $id_riwayat);
    exit();
    
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($koneksi);
    // Tampilkan pesan error yang lebih user-friendly
    error_log("Error dalam proses_skor.php: " . $e->getMessage());
    echo "Terjadi kesalahan saat memproses kuis Anda. Silakan coba lagi. <br>";
    echo "Pesan Error: " . htmlspecialchars($e->getMessage()); // Tampilkan pesan untuk debugging
    echo "<br><a href='index.php'>Kembali ke halaman utama</a>";
    exit();
}
?>