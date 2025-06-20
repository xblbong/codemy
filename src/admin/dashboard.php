<?php
session_start();
require_once '../config/koneksi.php';

//logut
if (isset($_GET['page']) && $_GET['page'] == 'logout') {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
    header("Location: index.php");
    exit();
}


//hapus modul
if (isset($_GET['page']) && $_GET['page'] == 'modul' && isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {

    $id_kursus_to_delete = (int)$_GET['id'];

    mysqli_begin_transaction($koneksi);

    try {
        $query_get_banner = "SELECT gambar_banner, judul FROM kursus WHERE id_kursus = $id_kursus_to_delete";
        $result_banner = mysqli_query($koneksi, $query_get_banner);
        $banner_file = '';
        $nama_kursus_dihapus = '';
        if ($row_banner = mysqli_fetch_assoc($result_banner)) {
            $banner_file = $row_banner['gambar_banner'];
            $nama_kursus_dihapus = $row_banner['judul'];
        } else {
            // klo kursus tidak ditemukan, batalkan transaksi
            throw new Exception("Kursus tidak ditemukan.");
        }

        // hapus dari detail_jawaban (terkait dengan riwayat_kuis)
        mysqli_query($koneksi, "DELETE FROM detail_jawaban WHERE id_riwayat IN (SELECT id_riwayat FROM riwayat_kuis WHERE id_kursus = $id_kursus_to_delete)");

        // hapus dari riwayat_kuis
        mysqli_query($koneksi, "DELETE FROM riwayat_kuis WHERE id_kursus = $id_kursus_to_delete");

        // hapus dari pilihan_jawaban (terkait dengan pertanyaan)
        mysqli_query($koneksi, "DELETE FROM pilihan_jawaban WHERE id_pertanyaan IN (SELECT id_pertanyaan FROM pertanyaan WHERE id_kursus = $id_kursus_to_delete)");

        // hapus dari pertanyaan
        mysqli_query($koneksi, "DELETE FROM pertanyaan WHERE id_kursus = $id_kursus_to_delete");

        // hapus dari progress_materi (terkait dengan materi)
        mysqli_query($koneksi, "DELETE FROM progress_materi WHERE id_materi IN (SELECT id_materi FROM materi WHERE id_kursus = $id_kursus_to_delete)");

        // hapus dari materi
        mysqli_query($koneksi, "DELETE FROM materi WHERE id_kursus = $id_kursus_to_delete");

        //hapus dari kursus
        $sql_delete_kursus = "DELETE FROM kursus WHERE id_kursus = $id_kursus_to_delete";
        if (!mysqli_query($koneksi, $sql_delete_kursus)) {
            throw new Exception(mysqli_error($koneksi));
        }

        // hapus file banner
        $path_to_banner = "../public/uploads/banners/" . $banner_file;
        if (file_exists($path_to_banner) && !is_dir($path_to_banner)) {
            unlink($path_to_banner);
        }

        //commit semua perubahan jika tidak ada error
        mysqli_commit($koneksi);

        // set pesan sukses
        $_SESSION['pesan_sukses'] = "Modul '{$nama_kursus_dihapus}' dan materinya berhasil dihapus.";
    } catch (Exception $e) {
        // rollback semua perubahan
        mysqli_rollback($koneksi);
        // set pesan error
        $_SESSION['pesan_error'] = "Gagal menghapus modul: " . $e->getMessage();
    }

    header("Location: dashboard.php?page=modul");
    exit;
}

// --- PROSES HAPUS MATERI, PERTANYAAN ---
if (isset($_GET['page']) && isset($_GET['aksi'])) {

    // hapus materi
    if ($_GET['page'] == 'hapus_materi' && $_GET['aksi'] == 'proses' && isset($_GET['id_materi'])) {
        $id_materi = (int)$_GET['id_materi'];

        // ambil id_kursus untuk redirect kembali
        $query_get_kursus = "SELECT id_kursus FROM materi WHERE id_materi = $id_materi";
        $result_get_kursus = mysqli_query($koneksi, $query_get_kursus);
        $id_kursus_redirect = mysqli_fetch_assoc($result_get_kursus)['id_kursus'];

        // hapus progress_materi
        mysqli_query($koneksi, "DELETE FROM progress_materi WHERE id_materi = $id_materi");

        // hapus materi
        $query_hapus = "DELETE FROM materi WHERE id_materi = $id_materi";
        if (mysqli_query($koneksi, $query_hapus)) {
            $_SESSION['pesan_sukses'] = "Materi berhasil dihapus.";
        } else {
            $_SESSION['pesan_error'] = "Gagal menghapus materi.";
        }

        // redirect ke halaman kelola modul
        header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus_redirect);
        exit();
    }

    // hapus pertanyaan
    if ($_GET['page'] == 'hapus_pertanyaan' && $_GET['aksi'] == 'proses' && isset($_GET['id_pertanyaan'])) {
        $id_pertanyaan = (int)$_GET['id_pertanyaan'];

        // ambil id_kursus untuk redirect kembali
        $query_get_kursus = "SELECT id_kursus FROM pertanyaan WHERE id_pertanyaan = $id_pertanyaan";
        $result_get_kursus = mysqli_query($koneksi, $query_get_kursus);
        $id_kursus_redirect = mysqli_fetch_assoc($result_get_kursus)['id_kursus'];

        //gunakan transaksi untuk memastikan semua query berhasil
        mysqli_begin_transaction($koneksi);
        try {
            //hapus pilihan jawaban
            mysqli_query($koneksi, "DELETE FROM pilihan_jawaban WHERE id_pertanyaan = $id_pertanyaan");

            // hapus pertanyaan
            $query_hapus = "DELETE FROM pertanyaan WHERE id_pertanyaan = $id_pertanyaan";
            if (!mysqli_query($koneksi, $query_hapus)) {
                throw new Exception("Gagal menghapus pertanyaan.");
            }

            mysqli_commit($koneksi);
            $_SESSION['pesan_sukses'] = "Pertanyaan berhasil dihapus.";
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
        }

        //direct ke halaman kelola modul
        header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus_redirect);
        exit();
    }
}


$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$pageTitles = [
    'dashboard' => 'Dashboard',
    'anggota' => 'Manajemen Anggota',
    'detail_anggota' => 'Detail Anggota',
    'modul' => 'Manajemen Modul',
    'kuis' => 'Ringkasan Kuis',
    'tambah_modul' => 'Tambah Modul',
    'edit_modul' => 'Edit Modul',
    'kelola_modul' => 'Kelola Modul',
    'kelola_kuis' => 'Kelola Kuis',
    'tambah_materi' => 'Tambah Materi',
    'tambah_pertanyaan' => 'Tambah Kuis',
    'edit_materi' => 'Edit Materi',
    'edit_pertanyaan' => 'Edit Kuis',
];

$pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : 'Halaman Tidak Ditemukan';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_GET['page'])) {

    // Proses untuk form tambah materi
    if ($_GET['page'] == 'tambah_materi' && isset($_GET['id_kursus'])) {
        $id_kursus = (int)$_GET['id_kursus'];

        // Ambil data dan siapkan array error
        $errors = [];
        $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
        $isi_materi = mysqli_real_escape_string($koneksi, $_POST['isi_materi']);
        $url_video = mysqli_real_escape_string($koneksi, $_POST['url_video']);
        $nomor_urut = (int)$_POST['nomor_urut'];

        // Validasi
        if (empty($judul)) $errors['judul'] = "Judul materi harus diisi.";
        if (empty($isi_materi)) $errors['isi_materi'] = "Isi materi harus diisi.";
        if ($nomor_urut <= 0) $errors['nomor_urut'] = "Nomor urut harus lebih dari 0.";
        if (!empty($url_video) && !filter_var($url_video, FILTER_VALIDATE_URL)) {
            $errors['url_video'] = "Format URL video tidak valid.";
        }

        if (empty($errors)) {
            $query = "INSERT INTO materi (id_kursus, judul, isi_materi, url_video, nomor_urut) 
                      VALUES ('$id_kursus', '$judul', '$isi_materi', '$url_video', '$nomor_urut')";

            if (mysqli_query($koneksi, $query)) {
                $_SESSION['pesan_sukses'] = "Materi baru '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil ditambahkan!";
                // Redirect yang benar
                header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus);
                exit();
            } else {
                // Simpan error database ke session untuk ditampilkan di form
                $_SESSION['form_errors'] = ['database' => 'Gagal menyimpan materi: ' . mysqli_error($koneksi)];
            }
        } else {
            // Jika ada error validasi, simpan error dan data input lama ke session
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
        }

        // Redirect kembali ke halaman form untuk menampilkan error
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
        //proses untuk edit materi
    } else if ($_GET['page'] == 'edit_modul' && isset($_GET['id'])) {
        // 1. Ambil ID kursus yang akan diedit dari URL
        $id_kursus_to_edit = (int)$_GET['id'];

        // Siapkan array untuk menampung error
        $errors = [];

        // 2. Ambil semua data dari form yang disubmit
        $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
        $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
        $id_kategori = (int)$_POST['id_kategori'];
        $banner_lama = $_POST['banner_lama']; // Ini adalah nama file banner yang sudah ada sebelumnya

        // 3. Lakukan validasi dasar untuk input teks
        if (empty($judul)) {
            $errors['judul'] = "Judul modul tidak boleh kosong.";
        } elseif (strlen($judul) < 7) {
            $errors['judul'] = "Judul modul minimal harus 7 karakter.";
        }

        if (empty($deskripsi)) {
            $errors['deskripsi'] = "Deskripsi modul tidak boleh kosong.";
        } elseif (strlen($deskripsi) < 20) {
            $errors['deskripsi'] = "Deskripsi modul minimal harus 20 karakter.";
        }

        if (empty($id_kategori)) {
            $errors['kategori'] = "Kategori harus dipilih.";
        }

        // untuk banner
        $nama_file_banner_baru = $banner_lama; //default jika tidak ada upload
        if (isset($_FILES['gambar_banner']) && $_FILES['gambar_banner']['error'] == UPLOAD_ERR_OK) {

            // Aturan validasi file
            $max_file_size = 2 * 1024 * 1024; // 2 MB
            $allowed_ext = ['png', 'jpg', 'jpeg', 'webp'];

            // Info file yang diupload
            $file_size = $_FILES['gambar_banner']['size'];
            $file_name = $_FILES['gambar_banner']['name'];
            $file_tmp_name = $_FILES['gambar_banner']['tmp_name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

            // Validasi ukuran
            if ($file_size > $max_file_size) {
                $errors['banner'] = "Ukuran gambar tidak boleh lebih dari 2 MB.";
            }

            // Validasi format/ekstensi
            if (!in_array($file_ext, $allowed_ext)) {
                $errors['banner'] = "Format gambar tidak valid. Hanya izinkan: " . implode(', ', $allowed_ext);
            }

            // Jika validasi file lolos
            if (!isset($errors['banner'])) {
                // Buat nama file yang unik untuk menghindari konflik
                $nama_file_banner_baru = uniqid('banner_', true) . '.' . $file_ext;
                $upload_path = __DIR__ . '/../public/uploads/banners/' . $nama_file_banner_baru;

                // Pindahkan file yang diupload ke folder tujuan
                if (move_uploaded_file($file_tmp_name, $upload_path)) {
                    // Jika upload file BARU berhasil, HAPUS file banner LAMA
                    if (!empty($banner_lama) && file_exists(__DIR__ . '/../public/uploads/banners/' . $banner_lama)) {
                        unlink(__DIR__ . '/../public/uploads/banners/' . $banner_lama);
                    }
                } else {
                    $errors['banner'] = "Gagal memindahkan file yang diupload.";
                }
            }
        }

        // 5. Jika tidak ada error sama sekali setelah semua validasi
        if (empty($errors)) {
            // Buat query UPDATE untuk memperbarui data di database
            $query_update = "UPDATE kursus SET 
                           judul = '$judul', 
                           deskripsi = '$deskripsi', 
                           id_kategori = '$id_kategori',
                           gambar_banner = '$nama_file_banner_baru'
                         WHERE id_kursus = $id_kursus_to_edit";

            if (mysqli_query($koneksi, $query_update)) {
                // Jika berhasil, siapkan pesan sukses dan redirect ke halaman modul
                $_SESSION['pesan_sukses'] = "Modul '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil diperbarui.";
                header("Location: dashboard.php?page=modul");
                exit();
            } else {
                // Jika query gagal
                $errors['database'] = "Gagal memperbarui data di database: " . mysqli_error($koneksi);
            }
        }

        // 6. Jika ada error (baik dari validasi input atau file), lakukan ini:
        // Simpan array errors dan data input lama ke session
        $_SESSION['form_errors'] = $errors;
        $_SESSION['old_input'] = $_POST;
        // Redirect kembali ke halaman form edit untuk menampilkan pesan error
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else if ($_GET['page'] == 'edit_materi' && isset($_GET['id_materi'])) {
        $id_materi = (int)$_GET['id_materi'];

        // Ambil data dan siapkan array error
        $errors = [];
        $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
        $isi_materi = mysqli_real_escape_string($koneksi, $_POST['isi_materi']);
        $url_video = mysqli_real_escape_string($koneksi, $_POST['url_video']);
        $nomor_urut = (int)$_POST['nomor_urut'];
        $id_kursus = (int)$_POST['id_kursus'];

        // validasi
        if (empty($judul)) $errors['judul'] = "Judul materi harus diisi.";
        if (empty($isi_materi)) $errors['isi_materi'] = "Isi materi harus diisi.";
        if ($nomor_urut <= 0) $errors['nomor_urut'] = "Nomor urut harus lebih dari 0.";
        if (!empty($url_video) && !filter_var($url_video, FILTER_VALIDATE_URL)) {
            $errors['url_video'] = "Format URL video tidak valid.";
        }

        if (empty($errors)) {
            // pakai query update untuk memperbarui materi
            $query = "UPDATE materi SET 
                        judul = '$judul', 
                        isi_materi = '$isi_materi', 
                        url_video = '$url_video', 
                        nomor_urut = '$nomor_urut' 
                      WHERE id_materi = $id_materi";

            if (mysqli_query($koneksi, $query)) {
                $_SESSION['pesan_sukses'] = "Materi '<strong>" . htmlspecialchars($judul) . "</strong>' berhasil diperbarui!";
                //redirect ke halaman kelola modul
                header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus);
                exit();
            } else {
                $_SESSION['form_errors'] = ['database' => 'Gagal memperbarui materi: ' . mysqli_error($koneksi)];
            }
        } else {
            //error ditampung ke session
            $_SESSION['form_errors'] = $errors;
            $_SESSION['old_input'] = $_POST;
        }

        //redirect ke halaman form untuk menampilkan error
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();

        //tambah pertanyaan
    } else if ($_GET['page'] == 'tambah_pertanyaan' && isset($_GET['id_kursus'])) {
        $id_kursus = (int)$_GET['id_kursus'];

        // Gunakan transaction karena melibatkan > 1 tabel
        mysqli_begin_transaction($koneksi);
        try {
            // Ambil data & validasi
            $teks_pertanyaan = mysqli_real_escape_string($koneksi, $_POST['teks_pertanyaan']);
            if (empty($teks_pertanyaan)) throw new Exception("Teks pertanyaan tidak boleh kosong.");

            // 1. Simpan pertanyaan
            $query_pertanyaan = "INSERT INTO pertanyaan (id_kursus, teks_pertanyaan, penjelasan) VALUES ('$id_kursus', '$teks_pertanyaan', ?)";
            $stmt = mysqli_prepare($koneksi, $query_pertanyaan);
            mysqli_stmt_bind_param($stmt, 's', $_POST['penjelasan']);
            mysqli_stmt_execute($stmt);
            $id_pertanyaan_baru = mysqli_insert_id($koneksi);

            // 2. Simpan pilihan jawaban
            $pilihan_jawaban = $_POST['pilihan_jawaban']; // array
            $jawaban_benar_index = $_POST['jawaban_benar']; // index (0-3)

            foreach ($pilihan_jawaban as $index => $teks_pilihan) {
                if (empty($teks_pilihan)) continue; // Lewati jika kosong
                $apakah_benar = ($index == $jawaban_benar_index) ? 1 : 0;
                $query_pilihan = "INSERT INTO pilihan_jawaban (id_pertanyaan, teks_pilihan, cek_jawaban) VALUES (?, ?, ?)";
                $stmt_pilihan = mysqli_prepare($koneksi, $query_pilihan);
                mysqli_stmt_bind_param($stmt_pilihan, 'isi', $id_pertanyaan_baru, $teks_pilihan, $apakah_benar);
                mysqli_stmt_execute($stmt_pilihan);
            }

            // Jika semua berhasil, commit
            mysqli_commit($koneksi);
            $_SESSION['pesan_sukses'] = "Pertanyaan baru berhasil ditambahkan.";
            header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus);
            exit();
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['pesan_error'] = "Gagal menambah pertanyaan: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }

        // edit pertanyaan
    } else if ($_GET['page'] == 'edit_pertanyaan' && isset($_GET['id_pertanyaan'])) {
        $id_pertanyaan = (int)$_GET['id_pertanyaan'];
        $id_kursus = (int)$_POST['id_kursus']; // Ambil dari hidden input

        mysqli_begin_transaction($koneksi);
        try {
            // Ambil data & validasi
            $teks_pertanyaan = mysqli_real_escape_string($koneksi, $_POST['teks_pertanyaan']);
            if (empty($teks_pertanyaan)) throw new Exception("Teks pertanyaan tidak boleh kosong.");

            // 1. Update pertanyaan utama
            $query_update_pertanyaan = "UPDATE pertanyaan SET teks_pertanyaan = ?, penjelasan = ? WHERE id_pertanyaan = ?";
            $stmt = mysqli_prepare($koneksi, $query_update_pertanyaan);
            mysqli_stmt_bind_param($stmt, 'ssi', $teks_pertanyaan, $_POST['penjelasan'], $id_pertanyaan);
            mysqli_stmt_execute($stmt);

            // 2. Update pilihan jawaban
            $pilihan_ids = $_POST['pilihan_id']; // array id pilihan
            $pilihan_teks = $_POST['pilihan_teks']; // array teks pilihan
            $jawaban_benar_id = $_POST['jawaban_benar_id']; // id pilihan yang benar

            foreach ($pilihan_ids as $index => $id_pilihan) {
                $teks = mysqli_real_escape_string($koneksi, $pilihan_teks[$index]);
                $apakah_benar = ($id_pilihan == $jawaban_benar_id) ? 1 : 0;
                $query_update_pilihan = "UPDATE pilihan_jawaban SET teks_pilihan = ?, cek_jawaban = ? WHERE id_pilihan = ?";
                $stmt_pilihan = mysqli_prepare($koneksi, $query_update_pilihan);
                mysqli_stmt_bind_param($stmt_pilihan, 'sii', $teks, $apakah_benar, $id_pilihan);
                mysqli_stmt_execute($stmt_pilihan);
            }

            mysqli_commit($koneksi);
            $_SESSION['pesan_sukses'] = "Pertanyaan berhasil diperbarui.";
            header("Location: dashboard.php?page=kelola_modul&id_kursus=" . $id_kursus);
            exit();
        } catch (Exception $e) {
            mysqli_rollback($koneksi);
            $_SESSION['pesan_error'] = "Gagal memperbarui pertanyaan: " . $e->getMessage();
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    }
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo ucfirst($currentPage); ?></title>
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
                        'codemy-purple': '#6D00A8',
                        'codemy-dark': '#31004C',
                        'codemy-black': '#0C0B17',
                        'codemy-yellow': '#FFB800',
                        'codemy-soft': '#F5F2FA',
                        'codemy-sidebar': '#E9DDF6',
                        'codemy-active': '#A259FF',
                        'success': '#00c217',
                        'danger': '#e11d48',
                        'light': '#f0e6f6',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-codemy-soft min-h-screen font-sans flex">
    <!-- Main Content -->
    <?php include('includes/sidebar.php'); ?>
    <main class="flex-1 p-10">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-extrabold text-codemy-dark"><?php echo $pageTitle; ?></h1>

            <div class="flex items-center gap-3">
                <?php include('includes/header.php'); ?>
            </div>
        </div>
        <!-- set up kondisi untuk memuat file konten berdasarkan halaman yang dipilih -->
        <?php
        switch ($currentPage) {
            case 'anggota':
                include 'anggota.php';
                break;
            case 'detail_anggota':
                include 'form/detail_anggota.php';
                break;
            case 'modul':
                include 'modul.php';
                break;
            case 'tambah_modul':
                include 'form/form-modul.php';
                break;
            case 'edit_modul':
                include 'form/edit-modul.php';
                break;
            case 'kelola_modul':
                include 'form/kelola-modul.php';
                break;
            case 'kelola_kuis':
                include 'form/kelola-kuis.php';
                break;
            case 'tambah_materi':
                include 'form/tambah-materi.php';
                break;
            case 'edit_materi':
                include 'form/edit-materi.php';
                break;
            case 'tambah_pertanyaan':
                include 'form/tambah-pertanyaan.php';
                break;
            case 'edit_pertanyaan':
                include 'form/edit-pertanyaan.php';
                break;
            case 'kuis':
                include 'kuis.php';
                break;
            case 'dashboard':
            default:
        ?>
                <!-- Card Pelatihan Saya -->
                <?php
                // query untuk menghitung total anggota
                $query_users = "SELECT count(id_pengguna) as total from pengguna where peran = 'user'";
                $result_users = mysqli_query($koneksi, $query_users);
                $total_users = mysqli_fetch_assoc($result_users)['total'];

                // query untuk menghitung total modul/kursus
                $query_kursus = "SELECT count(id_kursus) as total from kursus";
                $result_kursus = mysqli_query($koneksi, $query_kursus);
                $total_kursus = mysqli_fetch_assoc($result_kursus)['total'];

                // query untuk menghitung total pengerjaan kuis
                $query_kuis = "SELECT count(id_riwayat) as total from riwayat_kuis";
                $result_kuis = mysqli_query($koneksi, $query_kuis);
                $total_kuis_selesai = mysqli_fetch_assoc($result_kuis)['total'];

                // query untuk aktivitas terbaru
                $query_aktivitas = "SELECT nama, peran FROM pengguna ORDER BY id_pengguna";
                $result_aktivitas = mysqli_query($koneksi, $query_aktivitas);

                ?>
                <div class="border border-codemy-active rounded-xl bg-white p-6 mb-8">
                    <h1 class="text-codemy-dark text-2xl font-bold mb-4">Pelatihan Saya</h1>
                    <div class="flex flex-col md:flex-row gap-4">

                        <div class="flex-1 bg-codemy-sidebar rounded-lg flex flex-col items-center justify-center py-16">
                            <span class="text-3xl font-bold text-codemy-dark mb-1"><?php echo $total_users; ?></span>
                            <span class="text-codemy-dark text-sm">Total Pengguna</span>
                        </div>
                        <div class="flex-1 bg-codemy-sidebar rounded-lg flex flex-col items-center justify-center py-16">
                            <span class="text-3xl font-bold text-codemy-dark mb-1"><?php echo $total_kursus; ?></span>
                            <span class="text-codemy-dark text-sm">Total Modul</span>
                        </div>
                        <div class="flex-1 bg-codemy-sidebar rounded-lg flex flex-col items-center justify-center py-16">
                            <span class="text-3xl font-bold text-codemy-dark mb-1"><?php echo $total_kuis_selesai; ?></span>
                            <span class="text-codemy-dark text-sm">Total Kuis Selesai</span>
                        </div>
                    </div>
                </div>

                <?php
                $query_grafik = "SELECT DATE(dibuat_pada) AS tanggal, COUNT(id_pengguna) AS jumlah FROM pengguna WHERE dibuat_pada >= CURDATE() - INTERVAL 6 DAY GROUP BY DATE(dibuat_pada) ORDER BY tanggal ASC";
                $result_grafik = mysqli_query($koneksi, $query_grafik);

                $labels = [];
                $data_points = [];
                if ($result_grafik) {
                    while ($row = mysqli_fetch_assoc($result_grafik)) {
                        // ubah format tanggal 'YYYY-MM-DD' jadi 'DD Mon' (misal: 25 Okt)
                        $labels[] = date('d M', strtotime($row['tanggal']));
                        $data_points[] = $row['jumlah'];
                    }
                }

                // ubah array jadi string 
                $chart_labels = "'" . implode("','", $labels) . "'";
                $chart_data = implode(",", $data_points);

                // buat url grafik jadi dinamis
                $chart_url = "https://quickchart.io/chart?c=" . urlencode("{ type:'line', data:{ labels:[{$chart_labels}], datasets:[{ label:'Pengguna Baru', data:[{$chart_data}], fill:true, backgroundColor:'rgba(109,0,168,0.1)', borderColor:'#6D00A8', pointRadius:4, pointBackgroundColor:'#6D00A8' }]}, options: { legend: { display: false }, scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] } } }");
                ?>
                <!-- Grafik dan Total Pelatihan -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Grafik Pendaftaran Pengguna Baru -->
                    <div class="lg:col-span-2 border border-codemy-active rounded-xl bg-white p-4">
                        <h2 class="text-lg font-bold text-dark mb-4">Pendaftaran Pengguna Baru (7 Hari Terakhir)</h2>
                        <!-- Tampilkan grafik dengan URL yang sudah dinamis -->
                        <img src="<?php echo $chart_url; ?>" alt="Grafik Pendaftaran Pengguna" class="w-full">
                    </div>

                    <!-- Pendaftar Terbaru -->
                    <div class="border border-codemy-active rounded-xl bg-white p-4">
                        <h2 class="text-lg font-bold text-dark mb-4">Pendaftar Terbaru</h2>
                        <div class="space-y-4">
                            <?php if ($result_aktivitas && mysqli_num_rows($result_aktivitas) > 0): ?>
                                <?php while ($aktivitas = mysqli_fetch_assoc($result_aktivitas)): ?>
                                    <div class="flex items-center gap-4">
                                        <div class="bg-green-100 p-2 rounded-full"><i class="fa-solid fa-user-plus text-green-600"></i></div>
                                        <div>
                                            <p class="text-sm font-medium text-slate-800"><b><?php echo htmlspecialchars($aktivitas['nama']); ?></b> baru saja mendaftar.</p>
                                            <p class="text-xs text-slate-500">Peran: <?php echo ucfirst($aktivitas['peran']); ?></p>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-sm text-slate-500">Belum ada pendaftar baru</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
    </main>
<?php
                break;
        }
?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</body>

</html>