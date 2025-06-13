<?php
$pesan_error = '';
$pesan_sukses = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $errors = [];
    $judul_kursus = mysqli_real_escape_string($koneksi, $_POST['judul_kursus']);
    $deskripsi_kursus = mysqli_real_escape_string($koneksi, $_POST['deskripsi_kursus']);
    $id_kategori = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : 0;

    $judul_materi = mysqli_real_escape_string($koneksi, $_POST['judul_materi']);
    $url_video = mysqli_real_escape_string($koneksi, $_POST['url_video']);
    $isi_materi = mysqli_real_escape_string($koneksi, $_POST['isi_materi']);

    $dibuat_oleh = $_SESSION['id_admin_login'] ?? 1;

    if (empty($judul_kursus)) {
        $errors['judul_kursus'] = "Judul kursus harus diisi.";
    } elseif (strlen($judul_kursus) < 7) {
        $errors['judul_kursus'] = "Judul kursus minimal harus 10 karakter.";
    }
    if (empty($deskripsi_kursus)) {
        $errors['deskripsi_kursus'] = "Deskripsi kursus harus diisi.";
    } elseif (strlen($deskripsi_kursus) < 15) {
        $errors['deskripsi_kursus'] = "Deskripsi kursus minimal harus 50 karakter.";
    }
    if (empty($id_kategori)) {
        $errors['kategori'] = "Kategori harus dipilih.";
    }

    if (empty($judul_materi)) {
        $errors['judul_materi'] = "Judul materi harus diisi.";
    } elseif (strlen($judul_materi) < 7) {
        $errors['judul_materi'] = "Judul materi minimal harus 10 karakter.";
    }
    if (empty($isi_materi)) {
        $errors['isi_materi'] = "Deskripsi materi harus diisi.";
    } elseif (strlen($isi_materi) < 15) {
        $errors['isi_materi'] = "Deskripsi materi minimal harus 50 karakter.";
    }

    // validasi url video
    if (empty($url_video)) {
        $errors['url_video'] = "URL Video harus diisi.";
    } elseif (!filter_var($url_video, FILTER_VALIDATE_URL)) {
        $errors['url_video'] = "Format URL video tidak valid.";
    }

    //validasi file banner
    $nama_file_banner_baru = null;
    if (isset($_FILES['gambar_banner']) && $_FILES['gambar_banner']['error'] == UPLOAD_ERR_OK) {
        $max_file_size = 500 * 1024 * 1024; //500MB
        if ($_FILES['gambar_banner']['size'] > $max_file_size) {
            $errors['banner'] = "Ukuran gambar banner tidak boleh lebih dari 2 MB.";
        } else {
            $file_ext = strtolower(pathinfo($_FILES['gambar_banner']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['png', 'jpg', 'jpeg'];
            if (!in_array($file_ext, $allowed_ext)) {
                $errors['banner'] = "Format banner tidak sesuai! (Hanya PNG, JPG, JPEG)";
            }
        }
    } else {
        switch ($_FILES['gambar_banner']['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors['banner'] = "Ukuran file terlalu besar melebihi batas server.";
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors['banner'] = "Gambar banner harus diupload.";
                break;
            default:
                $errors['banner'] = "Terjadi kesalahan saat mengupload gambar banner.";
                break;
        }
    }

    if (empty($errors)) {
        // validasi sebelum simpan
        mysqli_begin_transaction($koneksi);

        try {
            // upload file banner
            $public_path = __DIR__ . '/../../public/';
            $upload_dir = $public_path . 'uploads/banners/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $nama_file_banner_baru = uniqid('banner_', true) . '.' . $file_ext;
            if (!move_uploaded_file($_FILES['gambar_banner']['tmp_name'], $upload_dir . $nama_file_banner_baru)) {
                throw new Exception("Gagal mengupload file banner.");
            }

            //insert data kursus
            $query_kursus = "INSERT INTO kursus (judul, deskripsi, dibuat_oleh, id_kategori, gambar_banner) 
                             VALUES ('$judul_kursus', '$deskripsi_kursus', '$dibuat_oleh', '$id_kategori', '$nama_file_banner_baru')";
            if (!mysqli_query($koneksi, $query_kursus)) {
                throw new Exception("Gagal menyimpan data kursus.");
            }

            // kita dapatkan id_kursus baru
            $id_kursus_baru = mysqli_insert_id($koneksi);

            // insert data materi
            $query_materi = "INSERT INTO materi (id_kursus, judul, isi_materi, url_video, nomor_urut) 
                             VALUES ('$id_kursus_baru', '$judul_materi', '$isi_materi', '$url_video', 1)";
            if (!mysqli_query($koneksi, $query_materi)) {
                throw new Exception("Gagal menyimpan materi.");
            }

            //simpan perubahan ke database
            mysqli_commit($koneksi);

            // pesan sukses dan kembali ke halaman modul
            $_SESSION['pesan_sukses'] = "Modul baru '<strong>" . htmlspecialchars($judul_kursus) . "</strong>' dan materinya berhasil ditambahkan!";
            echo "<script>window.location.href='dashboard.php?page=modul';</script>";
            exit();
        } catch (Exception $e) {
            // jika ada kesalahan, rollback ke transaksi
            mysqli_rollback($koneksi);
            $errors['database'] = $e->getMessage();
        }
    }
}

// ambil data kategori untuk ditampilkan di dropdown
$query_kategori = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
$result_kategori = mysqli_query($koneksi, $query_kategori);
?>


<div class="max-w-3xl mx-auto">
    <div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
        <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-200">
            <a href="dashboard.php?page=modul" class="text-primary hover:text-primary-hover" title="Kembali ke Manajemen Modul">
                <i class="fa-solid fa-arrow-left fa-lg"></i>
            </a>
            <h1 class="text-2xl font-bold text-dark">Tambah Modul Baru</h1>
        </div>

        <?php if (!empty($pesan_error)): ?>
            <div class="bg-red-100 border-l-4 border-danger text-red-700 p-4 mb-6 rounded-md" role="alert">
                <p class="font-bold">Terjadi Kesalahan</p>
                <p><?php echo $pesan_error; ?></p>
            </div>
        <?php endif; ?>

        <form action="dashboard.php?page=tambah_modul" method="POST" enctype="multipart/form-data">
            <div class="space-y-6">
                <div class="border-b pb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Kursus (Modul)</h2>
                    <div class="space-y-4">
                        <!-- Judul Kursus -->
                        <div>
                            <label for="judul_kursus" class="block text-sm font-medium text-gray-800 mb-2">Judul Kursus</label>
                            <input type="text" name="judul_kursus" id="judul_kursus" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['judul_kursus']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-hover'; ?>" placeholder="Masukkan judul kursus" value="<?php echo htmlspecialchars($_POST['judul_kursus'] ?? ''); ?>">
                            <?php if (isset($errors['judul_kursus'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['judul_kursus']}</p>"; ?>
                        </div>

                        <!-- Kategori -->
                        <div class="relative">
                            <label for="id_kategori" class="block text-sm font-medium text-gray-800 mb-2">Kategori Modul</label>
                            <select name="id_kategori" id="id_kategori" class="capitalize w-full px-4 py-2 pr-10 border rounded-lg focus:outline-none focus:ring-2 appearance-none cursor-pointer <?php echo isset($errors['kategori']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-hover'; ?>">
                                <option value="" disabled selected>-- Pilih Kategori --</option>
                                <?php mysqli_data_seek($result_kategori, 0); // Reset pointer hasil query 
                                ?>
                                <?php while ($kat = mysqli_fetch_assoc($result_kategori)): ?>
                                    <option value="<?php echo $kat['id_kategori']; ?>" <?php echo (isset($_POST['id_kategori']) && $_POST['id_kategori'] == $kat['id_kategori']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($kat['nama_kategori']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 top-6 right-0 flex items-center px-2 text-gray-700">
                                <svg class="fill-current h-4 w-4" viewBox="0 0 20 20">
                                    <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                                </svg>
                            </div>
                            <?php if (isset($errors['kategori'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['kategori']}</p>"; ?>
                        </div>

                        <!-- Banner -->
                        <div>
                            <label for="gambar_banner" class="block text-sm font-medium text-gray-800 mb-2">Gambar Banner</label>
                            <input type="file" name="gambar_banner" id="gambar_banner" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100 <?php echo isset($errors['banner']) ? 'ring-2 ring-red-500 rounded-lg' : ''; ?>">
                            <?php if (isset($errors['banner'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['banner']}</p>"; ?>
                        </div>

                        <!-- Deskripsi Kursus -->
                        <div>
                            <label for="deskripsi_kursus" class="block text-sm font-medium text-gray-800 mb-2">Deskripsi Kursus</label>
                            <textarea name="deskripsi_kursus" id="deskripsi_kursus" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['deskripsi_kursus']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-hover'; ?>" rows="4" placeholder="Masukkan deskripsi kursus"><?php echo htmlspecialchars($_POST['deskripsi_kursus'] ?? ''); ?></textarea>
                            <?php if (isset($errors['deskripsi_kursus'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['deskripsi_kursus']}</p>"; ?>
                        </div>
                    </div>
                </div>

                <!-- === Materi === -->
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Materi Pertama</h2>
                    <div class="space-y-4">
                        <!-- Judul Materi -->
                        <div>
                            <label for="judul_materi" class="block text-sm font-medium text-gray-800 mb-2">Judul Materi</label>
                            <input type="text" name="judul_materi" id="judul_materi" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['judul_materi']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-hover'; ?>" placeholder="Masukkan judul materi" value="<?php echo htmlspecialchars($_POST['judul_materi'] ?? ''); ?>">
                            <?php if (isset($errors['judul_materi'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['judul_materi']}</p>"; ?>
                        </div>
                        <!-- URL Video -->
                        <div>
                            <label for="url_video" class="block text-sm font-medium text-gray-800 mb-2">URL Video YouTube</label>
                            <input type="url" name="url_video" id="url_video" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['url_video']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-hover'; ?>" placeholder="https://www.youtube.com/watch?v=..." value="<?php echo htmlspecialchars($_POST['url_video'] ?? ''); ?>">
                            <?php if (isset($errors['url_video'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['url_video']}</p>"; ?>
                        </div>
                        <!-- Deskripsi Materi -->
                        <div>
                            <label for="isi_materi" class="block text-sm font-medium text-gray-800 mb-2">Deskripsi Singkat Materi</label>
                            <textarea name="isi_materi" id="isi_materi" rows="4" class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 <?php echo isset($errors['isi_materi']) ? 'border-red-500 focus:ring-red-500' : 'border-gray-300 focus:ring-primary-hover'; ?>" placeholder="Masukkan deskripsi singkat materi"><?php echo htmlspecialchars($_POST['isi_materi'] ?? ''); ?></textarea>
                            <?php if (isset($errors['isi_materi'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['isi_materi']}</p>"; ?>
                        </div>
                    </div>
                </div>
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