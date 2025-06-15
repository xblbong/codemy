<?php
session_start();
require_once '../config/koneksi.php';

define('BASE_URL', 'http://localhost/project-kampus/codemy/src/');
if (isset($_SESSION['id_pengguna'])) {
    if ($_SESSION['peran'] == 'admin') {
        header("Location: " . BASE_URL . "admin/dashboard.php");
    } else {
        header("Location: " . BASE_URL . "public/index.php");
    }
    exit();
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email_or_username = trim($_POST['email_or_username']);
    $password = $_POST['password'];

    if (empty($email_or_username) || empty($password)) {
        $errors[] = "Email/Username dan Password harus diisi.";
    } else {
        $query = "SELECT id_pengguna, nama, password, peran, status FROM pengguna WHERE email = ? OR username = ?";
        $stmt = mysqli_prepare($koneksi, $query);
        mysqli_stmt_bind_param($stmt, 'ss', $email_or_username, $email_or_username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                if ($user['peran'] !== 'user') {
                    $errors[] = "Akses ditolak. Silakan login melalui halaman login admin.";
                } elseif ($user['status'] !== 'aktif') {
                    $errors[] = "Akun Anda tidak aktif atau diblokir.";
                } else {
                    $_SESSION['id_pengguna'] = $user['id_pengguna'];
                    $_SESSION['nama_pengguna'] = $user['nama'];
                    $_SESSION['peran'] = $user['peran'];
                    
                    // --- 3. GUNAKAN BASE_URL UNTUK REDIRECT ---
                    header("Location: " . BASE_URL . "public/index.php");
                    exit();
                }
            } else {
                $errors[] = "Email/Username atau Password salah.";
            }
        } else {
            $errors[] = "Email/Username atau Password salah.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Codemy</title>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <script>
    tailwind.config = {
      theme: {
        extend: {
          fontFamily: { sans: ['Lexend', 'sans-serif'] },
          colors: {
            'codemy-purple': '#58287D',
            'codemy-dark': '#31004C',
            'codemy-black': '#0C0B17',
            'codemy-yellow': '#FFB800',
            'codemy-active': '#A259FF',
          }
        }
      }
    }
  </script>
</head>
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#58287D] via-[#31004C] to-[#0C0B17] font-sans">
  <div class="w-full max-w-4xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-8 flex flex-col md:flex-row items-center gap-10 mt-10 mb-10">
    
    <!-- Gambar -->
    <div class="hidden md:flex w-1/2 justify-center items-center">
      <img src="uploads/foto/18907.png" alt="Ilustrasi" class="w-full max-w-sm rounded-xl shadow-lg bg-white">
    </div>

    <!-- Formulir Login -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="w-full md:w-1/2 space-y-5">
      <h2 class="text-3xl font-bold text-white mb-6">Login</h2>
      
      <!-- Menampilkan pesan error jika ada -->
       <?php if (!empty($errors)): ?>
        <div class="bg-red-500/20 border border-red-500/30 text-white p-3 rounded-lg text-sm">
            <?php foreach ($errors as $error): ?>
                <p><?php echo $error; ?></p>
            <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="bg-green-500/20 border border-green-500/30 text-white p-3 rounded-lg text-sm">
            <p><?php echo $_SESSION['pesan_sukses']; ?></p>
        </div>
        <?php unset($_SESSION['pesan_sukses']); ?>
      <?php endif; ?>
      <div>
        <label for="email_or_username" class="block text-sm font-semibold text-white mb-1">Email atau Username</label>
        <input type="text" id="email_or_username" name="email_or_username" placeholder="Masukkan email atau username" required
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition">
      </div>
      <div class="relative">
        <label for="password" class="block text-sm font-semibold text-white mb-1">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required
               class="w-full px-4 py-2 pr-10 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition">
        <button type="button" onclick="togglePassword('password')" class="absolute top-8 right-3 text-white/70 hover:text-white">
            <i class="fa-solid fa-eye"></i>
        </button>
      </div>
      <button type="submit" class="w-full bg-codemy-active hover:bg-codemy-yellow text-white hover:text-codemy-dark font-semibold py-3 rounded-lg mt-4 transition text-lg shadow-md">Login</button>
      <p class="text-center text-sm font-light text-white">Belum punya akun? <a href="register.php" class="text-codemy-yellow hover:text-white transition font-semibold">Daftar sekarang</a></p>
    </form>
  </div>
  
  <script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
  </script>
</body>
</html>