<!-- Footer diletakkan di luar tag <body> karena akan di-include di dalam body halaman utama -->
<footer class="bg-[#18122b] text-white py-8 px-4 w-full">
    <div class="max-w-screen-xl mx-auto grid grid-cols-2 md:grid-cols-4 gap-8">
        <!-- Kolom Logo -->
        <div class="col-span-2 md:col-span-1">
            <a href="index.php" class="inline-block mb-4">
                <img src="uploads/foto/codemy-logo-white.svg" alt="logo" class="h-8"> <!-- Asumsi ada logo versi putih -->
            </a>
            <p class="text-sm text-gray-400">© <?php echo date("Y"); ?> Codemy.<br>All rights reserved.</p>
        </div>
        
        <!-- Kolom About -->
        <div>
            <h4 class="font-semibold mb-3 text-gray-200">Perusahaan</h4>
            <ul class="text-sm text-gray-400 space-y-2">
                <li><a href="#" class="hover:text-white transition">Tentang Kami</a></li>
                <li><a href="#" class="hover:text-white transition">Kebijakan Privasi</a></li>
                <li><a href="#" class="hover:text-white transition">Syarat & Ketentuan</a></li>
            </ul>
        </div>
      
        <!-- Kolom Layanan -->
        <div>
            <h4 class="font-semibold mb-3 text-gray-200">Layanan</h4>
            <ul class="text-sm text-gray-400 space-y-2">
                <li><a href="#" class="hover:text-white transition">Pusat Bantuan</a></li>
                <li><a href="#" class="hover:text-white transition">Cek Sertifikat</a></li>
            </ul>
        </div>
      
        <!-- Kolom Social Media -->
        <div>
            <h4 class="font-semibold mb-3 text-gray-200">Ikuti Kami</h4>
            <div class="flex gap-4">
                <a href="#" aria-label="Facebook" class="text-gray-400 hover:text-white transition"><i class="fab fa-facebook-f fa-lg"></i></a>
                <a href="#" aria-label="Instagram" class="text-gray-400 hover:text-white transition"><i class="fab fa-instagram fa-lg"></i></a>
                <a href="#" aria-label="Twitter" class="text-gray-400 hover:text-white transition"><i class="fab fa-twitter fa-lg"></i></a>
                <a href="#" aria-label="LinkedIn" class="text-gray-400 hover:text-white transition"><i class="fab fa-linkedin-in fa-lg"></i></a>
            </div>
        </div>
    </div>
</footer>
<!-- Pastikan FontAwesome sudah di-link di halaman utama jika menggunakan ikon -->
<script src="https://kit.fontawesome.com/your-kit-code.js" crossorigin="anonymous"></script> <!-- Ganti dengan kit FontAwesome-mu -->