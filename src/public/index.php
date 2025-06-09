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
            <div class="container mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 mb-12 gap-x-16 items-center">
                <img src="foto/18907.png" alt="hero-image" class="w-full h-full object-cover container">
                <div>
                    <h1 class="text-4xl font-bold mb-6">Hello, Welcome To Codemy!</h1>
                    <p class="mb-4 font-light">Platform pelatihan coding dan IT terbaik nomer 1 di Indonesia.
                    Ayo! mulai perjalananmu dengan codemy untuk membantu melatih hard skill di bidang Teknologi Digital.</p>
                </div>
            </div>
    </div>

    <!-- Categories -->
    <div class="container mx-auto max-w-6xl py-8 px-4 mb-16">
        <nav class="flex justify-left border-b pb-[5px] border-[#fff] mb-12">
            <ul class="flex flex-wrap gap-8 text-lg font-medium">
                <li><a href="#" class="text-[#FFB800] font-bold border-b-2 border-[#FFB800] pb-2">category1</a></li>
                <li><a href="#" class="font-light text-white hover:text-[#FFB800] pb-2 transition">category2</a></li>
                <li><a href="#" class="font-light text-white hover:text-[#FFB800] pb-2 transition">category3</a></li>
                <li><a href="#" class="font-light text-white hover:text-[#FFB800] pb-2 transition">category4</a></li>
                <li><a href="#" class="font-light text-white hover:text-[#FFB800] pb-2 transition">category5</a></li>
                <li><a href="#" class="font-light text-white hover:text-[#FFB800] pb-2 transition">category6</a></li>
            </ul>
        </nav>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-12 mb-7">
            <!-- Card 1 -->
            <div class="bg-gradient-to-b from-[#2B174C] to-[#31004C] rounded-2xl px-4 pt-6 pb-8 hover:shadow-2xl hover:shadow-[#7F60A8] flex flex-col min-h-[340px]">
                <img src="foto/18907.png" alt="Chatbot" class="w-full h-36 object-cover rounded-xl mb-4">
                <span class="text-xs font-bold text-[#FFB800] mb-2">CHATBOT TECHNOLOGY</span>
                <h3 class="font-bold text-white text-lg mb-1">Chat bot 2</h3>
                <p class="text-[#E0D7F3] text-sm mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <div class="mt-auto flex justify-between items-center">
                    <span class="text-xs text-[#E0D7F3]">UI/UX</span>
                    <a href="detail-pelatihan.php" class="px-4 py-1 rounded bg-[#A259FF] text-white font-semibold text-sm hover:bg-[#FFB800] transition">Mulai Belajar</a>
                </div>
            </div>
            <!-- Card 2 -->
            <div class="bg-gradient-to-b from-[#2B174C] to-[#31004C] rounded-2xl px-4 pt-6 pb-8 hover:shadow-2xl hover:shadow-[#7F60A8] flex flex-col min-h-[340px]">
                <img src="foto/18907.png" alt="Chatbot" class="w-full h-36 object-cover rounded-xl mb-4">
                <span class="text-xs font-bold text-[#FFB800] mb-2">CHATBOT TECHNOLOGY</span>
                <h3 class="font-bold text-white text-lg mb-1">Chat bot 2</h3>
                <p class="text-[#E0D7F3] text-sm mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <div class="mt-auto flex justify-between items-center">
                    <span class="text-xs text-[#E0D7F3]">UI/UX</span>
                    <a href="detail-pelatihan.php" class="px-4 py-1 rounded bg-[#A259FF] text-white font-semibold text-sm hover:bg-[#FFB800] transition">Mulai Belajar</a>
                </div>
            </div>
            <!-- Card 3 -->
            <div class="bg-gradient-to-b from-[#2B174C] to-[#31004C] rounded-2xl px-4 pt-6 pb-8 hover:shadow-2xl hover:shadow-[#7F60A8] flex flex-col min-h-[340px]">
                <img src="foto/18907.png" alt="AI" class="w-full h-36 object-cover rounded-xl mb-4">
                <span class="text-xs font-bold text-[#FFB800] mb-2">ARTIFICIAL INTELLIGENCE</span>
                <h3 class="font-bold text-white text-lg mb-1">Artificial Intelligence</h3>
                <p class="text-[#E0D7F3] text-sm mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <div class="mt-auto flex justify-between items-center">
                    <span class="text-xs text-[#E0D7F3]">UI/UX</span>
                    <a href="detail-pelatihan.php" class="px-4 py-1 rounded bg-[#A259FF] text-white font-semibold text-sm hover:bg-[#FFB800] transition">Mulai Belajar</a>
                </div>
            </div>
            <div class="bg-gradient-to-b from-[#2B174C] to-[#31004C] rounded-2xl px-4 pt-6 pb-8 hover:shadow-2xl hover:shadow-[#7F60A8] flex flex-col min-h-[340px]">
                <img src="foto/18907.png" alt="AI" class="w-full h-36 object-cover rounded-xl mb-4">
                <span class="text-xs font-bold text-[#FFB800] mb-2">ARTIFICIAL INTELLIGENCE</span>
                <h3 class="font-bold text-white text-lg mb-1">Artificial Intelligence</h3>
                <p class="text-[#E0D7F3] text-sm mb-4">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                <div class="mt-auto flex justify-between items-center">
                    <span class="text-xs text-[#E0D7F3]">UI/UX</span>
                    <a href="detail-pelatihan.php" class="px-4 py-1 rounded bg-[#A259FF] text-white font-semibold text-sm hover:bg-[#FFB800] transition">Mulai Belajar</a>
                </div>
            </div>

        </div>
    </div>

        <!-- Trusted By Section -->
        <div class="w-full flex flex-col items-center mb-16">
            <span class="text-center text-md text-white mb-3">Dipercaya Oleh</span>
            <div class="bg-white rounded-2xl px-8 py-16 flex flex-wrap justify-center items-center gap-10 shadow-lg w-full max-w-4xl">
                <img src="foto/google.png" alt="Google" class="h-16 object-contain">
                <img src="foto/microsoft.png" alt="Microsoft" class="h-16 object-contain">
                <img src="foto/aws.png" alt="AWS" class="h-16 object-contain">
                <img src="foto/bangkit.png" alt="Bangkit" class="h-16 object-contain">
            </div>
        </div>

    <!-- Footer -->
    <?php include('./includes/footer.php') ?>
</body>
</html>