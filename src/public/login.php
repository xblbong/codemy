<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
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
  <div class="w-full max-w-4xl mx-auto bg-white/10 backdrop-blur-md rounded-2xl shadow-2xl p-8 flex flex-col md:flex-row items-center gap-10 mt-10 mb-10">
    <!-- Gambar -->
    <div class="w-full md:w-1/2 flex justify-center items-center">
      <img src="foto/18907.png" alt="Ilustrasi" class="w-[320px] md:w-[360px] rounded-xl shadow-lg bg-white">
    </div>
    <!-- Formulir Login -->
    <form action="" method="POST" class="w-full md:w-1/2 space-y-5">
      <h2 class="text-3xl font-bold text-white mb-6">Login</h2>
      <div>
        <label for="email" class="block text-sm font-semibold text-white mb-1">Email</label>
        <input type="email" id="email" name="email" placeholder="Email"
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition">
      </div>
      <div>
        <label for="password" class="block text-sm font-semibold text-white mb-1">Password</label>
        <input type="password" id="password" name="password" placeholder="Password"
               class="w-full px-4 py-2 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition">
      </div>
      <button type="submit" class="w-full bg-[#A259FF] hover:bg-codemy-yellow text-white hover:text-codemy-dark font-semibold py-2 rounded-lg mt-4 transition text-lg shadow-md">Login</button>
      <p class="text-center text-sm font-light text-white">Belum punya akun? <a href="register.php" class="text-codemy-yellow hover:text-white transition">Daftar sekarang</a></p>
    </form>
  </div>
</body>
</html>