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
<?php include('./includes/header.php') ?>

<!-- Hero Section -->
<div class="text-white py-16">
    <div class="container mx-auto">
        <!-- Gambar -->
        <div class="flex flex-col justify-center mb-8">
          <h1 class="text-3xl font-bold mb-8 text-left text-[#fff]">Judul Pelatihan</h1>
            <img src="foto/18907.png" alt="hero-image" class="h-[393px] object-cover rounded-xl">
        </div>
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Sidebar Modul -->
            <?php include('./components/modules.php') ?>
            <!-- Main Content -->
            <main class="flex-1">
                <div class="bg-[#58287D] p-6 rounded-xl">
                    <p class="text-[#E0D7F3] leading-relaxed">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include('./includes/footer.php') ?>
</body>
</html>