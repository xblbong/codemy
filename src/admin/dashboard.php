<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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
            <h1 class="text-3xl font-extrabold text-codemy-dark">Dashboard</h1>
            <div class="flex items-center gap-3">
            <?php include('includes/header.php'); ?>
            </div>
        </div>
        <!-- Card Pelatihan Saya -->
        <div class="border border-codemy-active rounded-xl bg-white p-6 mb-8">
            <h1 class="text-codemy-dark text-2xl font-bold mb-4">Pelatihan Saya</h1>
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1 bg-codemy-sidebar rounded-lg flex flex-col items-center justify-center py-16">
                    <span class="text-3xl font-bold text-codemy-dark mb-1">5</span>
                    <span class="text-codemy-dark text-sm">Total Semua Pelatihan</span>
                </div>
                <div class="flex-1 bg-codemy-sidebar rounded-lg flex flex-col items-center justify-center py-16">
                    <span class="text-3xl font-bold text-codemy-dark mb-1">1</span>
                    <span class="text-codemy-dark text-sm">Pelatihan Sedang Berjalan</span>
                </div>
                <div class="flex-1 bg-codemy-sidebar rounded-lg flex flex-col items-center justify-center py-16">
                    <span class="text-3xl font-bold text-codemy-dark mb-1">4</span>
                    <span class="text-codemy-dark text-sm">Pelatihan Selesai</span>
                </div>
            </div>
        </div>
        <!-- Grafik dan Total Pelatihan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Grafik Jumlah Pelatihan Anggota -->
            <div class="border border-codemy-active rounded-xl bg-white p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-codemy-dark">Grafik Jumlah Pelatihan Anggota</span>
                    <button class="bg-codemy-active text-white text-xs px-3 py-1 rounded hover:bg-codemy-dark transition">Unduh Gambar</button>
                </div>
                <img src="https://quickchart.io/chart?c={type:'line',data:{labels:['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'],datasets:[{label:'Kategori 1',data:[3,5,4,6,7,5,6],borderColor:'#a78bfa',fill:false},{label:'Kategori 2',data:[2,3,2,4,3,2,1],borderColor:'#f472b6',fill:false},{label:'Kategori 3',data:[1,2,3,2,1,3,2],borderColor:'#facc15',fill:false}]}}" alt="Line Chart" class="w-full h-48 object-contain bg-white rounded">
            </div>
            <!-- Total Pelatihan -->
            <div class="border border-codemy-active rounded-xl bg-white p-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="font-semibold text-codemy-dark">Total Pelatihan</span>
                    <button class="bg-codemy-active text-white text-xs px-3 py-1 rounded hover:bg-codemy-dark transition">Unduh Gambar</button>
                </div>
                <img src="https://quickchart.io/chart?c={type:'doughnut',data:{labels:['Beginner','Intermediate','Advance'],datasets:[{data:[10,7,5],backgroundColor:['#a3e635','#facc15','#f472b6']}]} }" alt="Pie Chart" class="w-full h-48 object-contain bg-white rounded mb-2">
                <div class="flex gap-4 justify-center text-sm">
                    <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full bg-lime-400"></span>Beginner</span>
                    <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full bg-yellow-300"></span>Intermediate</span>
                    <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full bg-pink-400"></span>Advance</span>
                </div>
            </div>
        </div>
    </main>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</body>
</html>