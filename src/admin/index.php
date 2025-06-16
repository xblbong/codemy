<?php
session_start();
require_once '../config/koneksi.php';
if (isset($_SESSION['id_pengguna']) && $_SESSION['peran'] == 'admin') {
  header("Location: dashboard.php?page=dashboard");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  $username = trim($_POST['username']);
  $password = $_POST['password'];

  // Validasi dasar
  if (empty($username) || empty($password)) {
    $errors[] = "Username dan Password harus diisi.";
  } else {
    $query = "SELECT id_pengguna, nama, password, peran, status FROM pengguna WHERE email = ? OR username = ?";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, 'ss', $username, $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Cek apakah pengguna ditemukan
    if ($user = mysqli_fetch_assoc($result)) {
      // Verifikasi password
      if (password_verify($password, $user['password'])) {

        if ($user['peran'] !== 'admin') {
          $errors[] = "Akses ditolak. Anda tidak memiliki hak sebagai admin.";
        } elseif ($user['status'] !== 'aktif') {
          $errors[] = "Akun Anda saat ini tidak aktif atau diblokir.";
        } else {
          // Jika semua benar, masuk session dan redirect ke dashboard admin
          $_SESSION['id_pengguna'] = $user['id_pengguna'];
          $_SESSION['nama_pengguna'] = $user['nama'];
          $_SESSION['peran'] = $user['peran'];

          header("Location: dashboard.php?page=dashboard");
          exit();
        }
      } else {
        $errors[] = "Username atau Password salah.";
      }
    } else {
      // Jika pengguna tidak ditemukan
      $errors[] = "Username atau Password salah.";
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
  <title>Login Admin - Codemy</title>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../css/style.css">
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

<body style="background-image: url(../public/uploads/foto/bg.svg); background-size: cover; background-position: center; background-repeat: no-repeat;" class="min-h-screen flex items-center justify-center font-sans">
  <div class="w-full max-w-4xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-12 flex flex-col md:flex-row items-center gap-10 mt-10 mb-10">
    <!-- Logo -->
    <div class="w-full md:w-1/2 flex justify-center items-center">
      <img src="../public/uploads/foto/foto.svg" alt="codemy logo" class="w-[100px] md:w-[200px] rounded-xl shadow-lg bg-white p-6">
    </div>
    <!-- Formulir Login Admin -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="w-full md:w-1/2 space-y-5">
      <h2 class="text-2xl font-bold text-white mb-6">Login Admin</h2>
      <!-- pesan error -->
      <?php if (!empty($errors)): ?>
        <div class="bg-red-500/20 border border-red-500/30 text-white p-3 rounded-lg text-sm">
          <?php foreach ($errors as $error): ?>
            <p><?php echo $error; ?></p>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
      <!-- pesan sukses -->
      <?php if (isset($_SESSION['pesan_sukses'])): ?>
        <div class="bg-green-500/20 border border-green-500/30 text-white p-3 rounded-lg text-sm">
          <p><?php echo $_SESSION['pesan_sukses']; ?></p>
        </div>
        <?php unset($_SESSION['pesan_sukses']); ?>
      <?php endif; ?>
      <div>
        <label for="username" class="block text-sm font-light text-white mb-1">Username</label>
        <input id="username" name="username" type="text" placeholder="Masukan Username" class="font-light text-sm w-full px-4 py-3 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition" required>
      </div>
      <div class="relative">
        <label for="password" class="block text-sm font-light text-white mb-1">Password</label>
        <input id="password" name="password" type="password" placeholder="Masukkan password" class="font-light text-sm w-full px-4 py-3 pr-10 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition" required>
        <button type="button" onclick="togglePassword('password')" class="absolute top-9 right-3 text-white/70 hover:text-white">
          <i class="fa-solid fa-eye"></i>
        </button>
      </div>
      <button type="submit" class="w-full bg-[#A259FF] hover:bg-codemy-yellow text-white hover:text-codemy-dark font-light py-2 rounded-lg mt-4 transition text-lg shadow-md">Login</button>
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