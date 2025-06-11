<?php
session_start();
require_once '../config/koneksi.php';
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

$pageTitles = [
    'dashboard' => 'Dashboard',
    'anggota' => 'Manajemen Anggota',
    'modul' => 'Manajemen Modul',
    'kuis' => 'Ringkasan Kuis',
    'tambah_modul' => 'Tambah Modul' 
];

$pageTitle = isset($pageTitles[$currentPage]) ? $pageTitles[$currentPage] : 'Halaman Tidak Ditemukan';
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
                        'codemy-purple': '#58287D',
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
            case 'modul':
                include 'modul.php';
                break;
            case 'tambah_modul':
                include 'form/form-modul.php';
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