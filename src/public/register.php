<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Akun Baru</title>
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
<body class="min-h-screen flex items-center justify-center bg-gradient-to-br from-[#58287D] via-[#31004C] to-[#0C0B17] font-sans">
    <!-- Gambar -->
    <div class="w-full md:w-1/2 flex justify-center items-center p-8 md:p-10">
      <img src="foto/18907.png" alt="Ilustrasi" class="w-[220px] md:w-[320px] rounded-xl shadow-lg">
    </div>
    <!-- Formulir -->
    <div class="w-full md:w-1/2 flex flex-col justify-center px-8 py-10 bg-white/95">
      <form action="" method="POST" class="space-y-4">
        <h2 class="text-3xl font-bold text-codemy-dark mb-2">Daftar Akun Baru</h2>
        <input type="hidden" name="role" value="user">
        <div>
          <label for="nama" class="block text-sm font-semibold text-codemy-dark mb-1">Nama Lengkap</label>
          <input type="text" id="nama" name="nama" placeholder="Nama Lengkap" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        </div>
        <div>
          <label for="email" class="block text-sm font-semibold text-codemy-dark mb-1">Email</label>
          <input type="email" id="email" name="email" placeholder="Email" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        </div>
        <div>
          <label for="telepon" class="block text-sm font-semibold text-codemy-dark mb-1">Nomor Telepon</label>
          <input type="tel" id="telepon" name="telepon" placeholder="Nomor Telepon" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        </div>
        <div>
          <label for="alamat" class="block text-sm font-semibold text-codemy-dark mb-1">Alamat</label>
          <textarea id="alamat" name="alamat" placeholder="Alamat" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base"></textarea>
        </div>
        <div>
          <label for="password" class="block text-sm font-semibold text-codemy-dark mb-1">Password</label>
          <input type="password" id="password" name="password" placeholder="Password" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        </div>
        <div>
          <label for="konfirmasi_password" class="block text-sm font-semibold text-codemy-dark mb-1">Konfirmasi Password</label>
          <input type="password" id="konfirmasi_password" name="konfirmasi_password" placeholder="Konfirmasi Password" class="w-full px-4 py-2 rounded-lg border border-codemy-dark/10 bg-white text-codemy-dark placeholder-codemy-dark/40 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition text-base">
        </div>
        <button type="submit" class="w-full bg-[#A259FF] hover:bg-codemy-yellow text-white hover:text-codemy-dark font-semibold py-2 rounded-lg mt-2 transition text-base shadow-md">Daftar</button>
        <p class="text-center text-xs font-light text-codemy-dark/80 mt-2">Sudah punya akun? <a href="login.php" class="text-codemy-yellow hover:text-codemy-dark transition font-semibold">Login sekarang</a></p>
      </form>
    </div>
</body>
</html>
