<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codemy Header</title>
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

<body style="font-family: 'Lexend', sans-serif;">
    <header class="flex justify-between items-center bg-white px-44 py-3 shadow-md border-b border-gray-100">
        <!-- Logo -->
        <a href="index.php" class="flex gap-3 items-center">
            <img src="foto/logo-header.svg" alt="logo" class="w-9">
            <img src="foto/codemy.svg" alt="codemy" class="w-28">
        </a>
        <!-- Navigation -->
        <nav class="flex items-center gap-8">
            <a href="pelatihan.php" class="font-semibold text-[#1a365d] hover:text-[#620097]">Pelatihan</a>
            <!-- <a href="#" class="font-semibold text-[#1a365d] hover:text-[#620097]">Tentang</a> -->
            <!-- User Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-2 font-semibold text-[#1a365d] hover:text-[#620097] focus:outline-none">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#31004C] text-white"><img src="foto/user1.svg" alt="user"></span>
                    <span>Sabillah</span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <!-- Dropdown menu -->
                <div class="absolute right-0 mt-2 w-48 bg-[#31004C] text-white rounded-lg shadow-lg py-2 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 pointer-events-none group-hover:pointer-events-auto group-focus-within:pointer-events-auto transition z-50">
                    <a href="#" class="block px-5 py-2 hover:bg-[#58287D] transition">Profile</a>
                    <a href="#" class="block px-5 py-2 hover:bg-[#58287D] transition">Dashboard</a>
                    <a href="#" class="block px-5 py-2 hover:bg-[#58287D] transition">Progress Belajar</a>
                    <div class="border-t border-white/20 my-1"></div>
                    <a href="#" class="block px-5 py-2 hover:bg-[#58287D] transition">Log Out</a>
                </div>
            </div>
        </nav>
    </header>
</body>

</html>