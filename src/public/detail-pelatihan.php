<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pelatihan</title>
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
<body>
<?php include('./includes/header.php') ?>
<div class="text-white py-16">
    <div class="container mx-auto">
        <!-- Gambar -->
        <div class="flex flex-col justify-center mb-8">
          <h1 class="text-3xl font-bold mb-8 text-left text-[#fff]">Judul Pelatihan</h1>
            <img src="foto/18907.png" alt="hero-image" class="h-[393px] object-cover rounded-xl">
        </div>
        <div class="bg-white/10 border border-white/20 rounded-lg p-4 flex flex-wrap gap-6 items-center mt-4 mb-5">
            <div class="flex-1 min-w-[200px]">
                <ul class="text-white text-sm space-y-1 list-disc list-inside">
                    <li>4 menit total video pembelajaran</li>
                    <li>5 bahan bacaan</li>
                    <li>5 konten dapat diunduh</li>
                    <li>Kuis yang dapat dikerjakan</li>
                </ul>
            </div>
        </div>
        <div class="flex justify-end items-center gap-3 mb-8">
            <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-instagram"></i></a>
            <a href="#" class="text-white hover:text-[#FFB800] text-xl"><i class="fab fa-telegram"></i></a>
        </div>
        <p class="text-[#E0D7F3] text-sm leading-relaxed tracking-wide mb-6 border-y-2 border-white/20 py-4">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin condimentum aliquet arcu, sit amet eleifend tortor. Donec elementum enim quis ligula laoreet convallis. Fusce sodales ligula ut nibh rhoncus, ut ornare odio interdum. Etiam ac sapien aliquam, fringilla lacus eget, tempus risus. Suspendisse ullamcorper, nulla eu malesuada pulvinar, magna nisl porttitor montes, nascetur ridiculus mus. Fusce aliquet malesuada quam, ut hendrerit risus eleifend in. Nulla facilisi. 
            </p>
            <div class="flex flex-col md:flex-row justify-end md:justify-end">
                <a href="modul-pelatihan.php" class="px-8 py-2 rounded-lg bg-[#A259FF] hover:bg-[#FFB800] text-white hover:text-[#31004C] font-semibold text-base transition shadow-md">Mulai Belajar</a>
            </div>
    </div>
</div>
<?php include('./includes/footer.php') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</body>
</html>