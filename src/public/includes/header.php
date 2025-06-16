<?php
require_once '../config/koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah ada parameter 'aksi=logout' di URL
if (isset($_GET['aksi']) && $_GET['aksi'] == 'logout') {
    $_SESSION = array();

    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Hancurkan session
    session_destroy();

    // Arahkan kembali ke halaman utama (index.php) setelah logout
    header("Location: index.php");
    exit();
}

// Cek status login untuk menentukan tampilan header
$is_logged_in = isset($_SESSION['id_pengguna']);
$nama_pengguna = $_SESSION['nama_pengguna'] ?? 'Tamu';
?>
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

<body style="font-family: 'Lexend', sans-serif;">
    <header class="flex justify-between items-center bg-white px-4 md:px-16 lg:px-44 py-3 shadow-md border-b border-gray-100">
    <!-- Logo -->
    <a href="index.php" class="flex gap-3 items-center">
        <img src="uploads/foto/logo-header.svg" alt="logo" class="w-9">
        <img src="uploads/foto/codemy.svg" alt="codemy" class="w-28">
    </a>

    <!-- Navigasi -->
    <nav class="flex items-center gap-8">
        <a href="pelatihan.php" class="font-semibold text-[#1a365d] hover:text-[#620097]">Pelatihan</a>
        
        <?php if ($is_logged_in): ?>
            <div class="relative group">
                <button class="flex items-center gap-2 font-semibold text-[#1a365d] hover:text-[#620097] focus:outline-none">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-[#31004C] text-white">
                        <img src="uploads/foto/user1.svg" alt="user"> <!-- Anda bisa ganti ini dengan avatar dinamis nanti -->
                    </span>
                    <span><?php echo htmlspecialchars(explode(' ', $nama_pengguna)[0]); // Ambil nama pertama saja ?></span>
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <!-- Dropdown menu -->
                <div class="absolute right-0 mt-2 w-48 bg-[#31004C] text-white rounded-lg shadow-lg py-2 opacity-0 group-hover:opacity-100 group-focus-within:opacity-100 pointer-events-none group-hover:pointer-events-auto group-focus-within:pointer-events-auto transition z-50">
                    <a href="progress.php" class="block px-5 py-2 hover:bg-[#58287D] transition">Progress Belajar</a>
                    <div class="border-t border-white/20 my-1"></div>
                    <a href="?aksi=logout" onclick="return confirm('Apakah Anda yakin ingin keluar?');" class="block px-5 py-2 hover:bg-[#58287D] transition">Log Out</a>
                </div>
            </div>
        <?php else: ?>
            <div class="flex items-center gap-4">
                <a href="login.php" class="font-semibold text-[#1a365d] hover:text-[#620097]">Masuk</a>
                <a href="register.php" class="bg-codemy-purple hover:bg-codemy-dark text-white font-semibold px-5 py-2 rounded-lg transition">Daftar</a>
            </div>
        <?php endif; ?>

    </nav>
</header>
</body>

</html>