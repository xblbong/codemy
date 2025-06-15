<?php
session_start();
require_once '../config/koneksi.php';

// Inisialisasi variabel untuk menampung error dan data input lama
$errors = [];
$old_input = [];

// Cek apakah form telah disubmit menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

  // Simpan semua input untuk ditampilkan kembali jika ada error
  $old_input = $_POST;

  //tampung data yang diinput dari form ke variabel
  $nama = trim($_POST['nama']);
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);
  $no_hp = trim($_POST['telepon']);
  $alamat = trim($_POST['alamat']);
  $password = $_POST['password'];
  $konfirmasi_password = $_POST['konfirmasi_password'];
  $peran_input = $_POST['role'];

  // atur validasi untuk setiap input
  // Validasi Nama
  if (empty($nama)) {
    $errors['nama'] = "Nama lengkap harus diisi.";
  }

  // validasi username
  if (empty($username)) {
    $errors['username'] = "Username harus diisi.";
  } elseif (!preg_match('/^[a-zA-Z0-9_]{5,20}$/', $username)) {
    $errors['username'] = "Username harus 5-20 karakter, hanya huruf, angka, dan underscore (_).";
  } else {
    // Cek duplikasi username
    $query_cek_username = "SELECT id_pengguna FROM pengguna WHERE username = ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek_username);
    mysqli_stmt_bind_param($stmt_cek, 's', $username);
    mysqli_stmt_execute($stmt_cek);
    mysqli_stmt_store_result($stmt_cek);
    if (mysqli_stmt_num_rows($stmt_cek) > 0) {
      $errors['username'] = "Username ini sudah digunakan. Silakan pilih yang lain.";
    }
    mysqli_stmt_close($stmt_cek);
  }

  // Validasi Email
  if (empty($email)) {
    $errors['email'] = "Email harus diisi.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Format email tidak valid.";
  } else {
    //cek apakah email sudah terdaftar
    $query_cek_email = "SELECT id_pengguna FROM pengguna WHERE email = ?";
    $stmt_cek = mysqli_prepare($koneksi, $query_cek_email);
    mysqli_stmt_bind_param($stmt_cek, 's', $email);
    mysqli_stmt_execute($stmt_cek);
    mysqli_stmt_store_result($stmt_cek);
    if (mysqli_stmt_num_rows($stmt_cek) > 0) {
      $errors['email'] = "Email ini sudah terdaftar. Silakan gunakan email lain.";
    }
    mysqli_stmt_close($stmt_cek);
  }

  // validasi password
  if (empty($password)) {
    $errors['password'] = "Password harus diisi.";
  } elseif (strlen($password) < 8) {
    $errors['password'] = "Password minimal harus 8 karakter.";
  }

  // Validasi Konfirmasi Password
  if ($password !== $konfirmasi_password) {
    $errors['konfirmasi_password'] = "Konfirmasi password tidak cocok.";
  }

  //validasi peran
  $allowed_roles = ['user', 'admin'];
  $peran = in_array($peran_input, $allowed_roles) ? $peran_input : 'user'; // Default ke 'user' jika tidak valid

  // Jika tidak ada error
  if (empty($errors)) {

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Buat query INSERT menggunakan Prepared Statement untuk keamanan
    $query_insert = "INSERT INTO pengguna (nama, username, email, no_hp, alamat, password, peran, status) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, 'aktif')";

    $stmt_insert = mysqli_prepare($koneksi, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, 'sssssss', $nama, $username, $email, $no_hp, $alamat, $hashed_password, $peran);

    // Jalankan query
    if (mysqli_stmt_execute($stmt_insert)) {
      // Jika berhasil, siapkan pesan sukses di session
      $_SESSION['pesan_sukses'] = "Pendaftaran berhasil! Silakan login dengan akun Anda.";

      // Redirect ke halaman login  
      if ($peran === 'admin') {
        header("Location: ../admin/index.php");
        header("Location: login.php");
      }
      exit();
    } else {
      // Jika terjadi error saat menyimpan ke database
      $errors['database'] = "Terjadi kesalahan pada sistem. Silakan coba lagi nanti.";
    }
    mysqli_stmt_close($stmt_insert);
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun Baru - Codemy</title>
  <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
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
            'codemy-active': '#A259FF',
          }
        }
      }
    }
  </script>
</head>

<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#58287D] via-[#31004C] to-[#0C0B17] font-sans">

  <!-- Bagian Gambar Ilustrasi -->
  <div class="hidden md:flex w-1/2 justify-center items-center p-8 md:p-10">
    <!-- Pastikan path ke gambar ini benar -->
    <img src="uploads/foto/18907.png" alt="Ilustrasi" class="w-full rounded-xl shadow-lg">
  </div>

  <!-- Bagian Formulir -->
  <div class="w-full md:w-1/2 flex flex-col justify-center px-8 py-10 bg-white/95">
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST" class="space-y-4">
      <h2 class="text-3xl font-bold text-codemy-dark mb-2">Daftar Akun Baru</h2>

      <?php if (isset($errors['database'])): ?>
        <div class="bg-red-100 text-red-700 p-3 rounded-lg text-sm"><?php echo $errors['database']; ?></div>
      <?php endif; ?>

      <!-- Input Nama Lengkap -->
      <div>
        <label for="nama" class="block text-sm font-semibold text-codemy-dark mb-1">Nama Lengkap</label>
        <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($old_input['nama'] ?? ''); ?>" placeholder="Nama Lengkap" required
          class="w-full px-4 py-2 rounded-lg border <?php echo isset($errors['nama']) ? 'border-red-500 ring-1 ring-red-500' : 'border-codemy-dark/10'; ?> bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        <?php if (isset($errors['nama'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['nama']}</p>"; ?>
      </div>

      <div>
        <label for="username" class="block text-sm font-semibold text-codemy-dark mb-1">Username</label>
        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($old_input['username'] ?? ''); ?>" placeholder="Pilih username unik" required class="w-full px-4 py-2 rounded-lg border <?php echo isset($errors['username']) ? 'border-red-500 ring-1 ring-red-500' : 'border-codemy-dark/10'; ?> bg-white ...">
        <?php if (isset($errors['username'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['username']}</p>"; ?>
      </div>

      <!-- Input Email -->
      <div>
        <label for="email" class="block text-sm font-semibold text-codemy-dark mb-1">Email</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($old_input['email'] ?? ''); ?>" placeholder="Email" required
          class="w-full px-4 py-2 rounded-lg border <?php echo isset($errors['email']) ? 'border-red-500 ring-1 ring-red-500' : 'border-codemy-dark/10'; ?> bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        <?php if (isset($errors['email'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['email']}</p>"; ?>
      </div>

      <!-- Input Nomor Telepon -->
      <div>
        <label for="telepon" class="block text-sm font-semibold text-codemy-dark mb-1">Nomor Telepon</label>
        <input type="tel" id="telepon" name="telepon" value="<?php echo htmlspecialchars($old_input['telepon'] ?? ''); ?>" placeholder="Nomor Telepon"
          class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
      </div>

      <!-- Input Alamat -->
      <div>
        <label for="alamat" class="block text-sm font-semibold text-codemy-dark mb-1">Alamat</label>
        <textarea id="alamat" name="alamat" placeholder="Alamat"
          class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base"><?php echo htmlspecialchars($old_input['alamat'] ?? ''); ?></textarea>
      </div>

      <!-- Dropdown Pilihan Role -->
      <div>
        <label for="role" class="block text-sm font-semibold text-codemy-dark mb-1">Daftar Sebagai</label>
        <select name="role" id="role" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
          <option value="user" <?php if (!isset($old_input['role']) || $old_input['role'] == 'user') echo 'selected'; ?>>Pengguna</option>
          <option value="admin" <?php if (isset($old_input['role']) && $old_input['role'] == 'admin') echo 'selected'; ?>>Admin</option>
        </select>
      </div>

      <!-- Input Password -->
      <div class="relative">
        <label for="password" class="block text-sm font-semibold text-codemy-dark mb-1">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required
          class="w-full px-4 py-2 pr-10 rounded-lg border <?php echo isset($errors['password']) ? 'border-red-500 ring-1 ring-red-500' : 'border-codemy-dark/10'; ?> bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        <button type="button" onclick="togglePassword('password')" class="absolute top-8 right-3 text-codemy-dark/60 hover:text-codemy-purple">
          <i class="fa-solid fa-eye"></i>
        </button>
        <?php if (isset($errors['password'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['password']}</p>"; ?>
      </div>

      <!-- Input Konfirmasi Password -->
      <div class="relative">
        <label for="konfirmasi_password" class="block text-sm font-semibold text-codemy-dark mb-1">Konfirmasi Password</label>
        <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Ketik ulang password" required
          class="w-full px-4 py-2 pr-10 rounded-lg border <?php echo isset($errors['konfirmasi_password']) ? 'border-red-500 ring-1 ring-red-500' : 'border-codemy-dark/10'; ?> bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        <button type="button" onclick="togglePassword('konfirmasi_password')" class="absolute top-8 right-3 text-codemy-dark/60 hover:text-codemy-purple">
          <i class="fa-solid fa-eye"></i>
        </button>
        <?php if (isset($errors['konfirmasi_password'])) echo "<p class='text-red-500 text-xs mt-1'>{$errors['konfirmasi_password']}</p>"; ?>
      </div>

      <button type="submit" class="w-full bg-codemy-active hover:bg-codemy-yellow text-white hover:text-codemy-dark font-semibold py-3 rounded-lg mt-2 transition text-base shadow-md">Daftar</button>
      <p class="text-center text-sm font-light text-codemy-dark/80 mt-2">Sudah punya akun? <a href="login.php" class="text-codemy-yellow hover:text-codemy-dark transition font-semibold">Login sekarang</a></p>
    </form>
  </div>

  <!-- Javascript untuk fungsi Show/Hide Password -->
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