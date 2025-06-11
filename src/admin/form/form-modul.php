<?php
// variabel untuk pesan feedback
$pesan_error = '';
$pesan_sukses = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // ambil datanya dari form dan escape untuk keamanan
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    
    $dibuat_oleh = $_SESSION['id_admin_login'] ?? 1; // dflt ke 1 klo session tidak ada

    // klo input kosong
    if (empty($judul) || empty($deskripsi)) {
        $pesan_error = "Judul dan Deskripsi modul tidak boleh kosong.";
    } else {
        // masukin data kursus ke database
        $query = "INSERT into kursus (judul, deskripsi, dibuat_oleh) values ('$judul', '$deskripsi', '$dibuat_oleh')";
        
        //jalanin
        if (mysqli_query($koneksi, $query)) {
            // klo berhasil, siapkan pesan sukses di session dan arahkan kembali ke halaman modul
            $_SESSION['pesan_sukses'] = "Modul baru '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil ditambahkan!";
            // arahin redirect ke halaman modul
            echo "<script>window.location.href='dashboard.php?page=modul';</script>";
            exit();
        } else {
            // klo query gagal, tampilin pesan error database
            $pesan_error = "Gagal menambahkan modul: " . mysqli_error($koneksi);
        }
    }
}
?>

<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <!-- Header Halaman Formulir -->
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-200">
            <a href="dashboard.php?page=modul" class="text-primary hover:text-primary-hover" title="Kembali ke Manajemen Modul">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-dark">Tambah Modul Baru</h1>
        </div>

        <!-- Tampilkan Pesan Error jika ada -->
        <?php if (!empty($pesan_error)): ?>
        <div class="bg-red-100 border-l-4 border-danger text-red-700 p-4 mb-6 rounded-md" role="alert">
            <p class="font-bold">Terjadi Kesalahan</p>
            <p><?php echo $pesan_error; ?></p>
        </div>
        <?php endif; ?>

        <!-- Formulir Tambah Modul -->
        <form action="dashboard.php?page=tambah_modul" method="POST">
            <div class="space-y-6">
                <!-- Input Judul Modul -->
                <div>
                    <label for="judul" class="block text-sm font-medium text-slate-700 mb-2">Judul Modul</label>
                    <input 
                        type="text" 
                        name="judul" 
                        id="judul" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-hover"
                        placeholder="Contoh: Belajar PHP Dasar untuk Pemula">
                </div>

                <!-- Input Deskripsi Modul -->
                <div>
                    <label for="deskripsi" class="block text-sm font-medium text-slate-700 mb-2">Deskripsi</label>
                    <textarea 
                        name="deskripsi" 
                        id="deskripsi" 
                        rows="6" 
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-hover"
                        placeholder="Jelaskan secara singkat mengenai isi dari modul ini..."></textarea>
                    <p class="text-xs text-slate-500 mt-1">Deskripsi ini akan tampil di halaman utama modul untuk dilihat pengguna.</p>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 mt-6">
                    <a href="dashboard.php?page=modul" class="bg-slate-200 text-slate-800 px-6 py-2 rounded-lg font-semibold hover:bg-slate-300 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="bg-codemy-purple text-white px-6 py-2 rounded-lg font-semibold hover:bg-[#6D00A8] hover:shadow-md transition-colors">
                        <i class="fa-solid fa-save mr-2"></i>
                        Simpan Modul
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>