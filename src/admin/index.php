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
    <form action="" method="POST" class="w-full md:w-1/2 space-y-5">
      <h2 class="text-2xl font-bold text-white mb-6">Login Admin</h2>
      <div>
        <label for="username" class="block text-sm font-light text-white mb-1">Username/Email</label>
        <input id="username" name="username" type="text" placeholder="Masukan Username/Email" class="font-light text-sm w-full px-4 py-3 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition" required>
      </div>
      <div>
        <label for="password" class="block text-sm font-light text-white mb-1">Password</label>
        <input id="password" name="password" type="password" placeholder="Masukan password" class="font-light text-sm w-full px-4 py-3 rounded-lg border border-white/30 bg-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-codemy-yellow transition" required>
      </div>
      <button type="submit" class="w-full bg-[#A259FF] hover:bg-codemy-yellow text-white hover:text-codemy-dark font-light py-2 rounded-lg mt-4 transition text-lg shadow-md">Login</button>
    </form>
  </div>
</body>
</html>