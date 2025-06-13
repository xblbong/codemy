<?php
$mainMenu = 'unknown'; // default jika tidak ada yang cocok
//peta halaman kiri nama menu kanan nama halaman
$menuMap = [
    'dashboard' => ['dashboard'],
    'anggota'   => ['anggota', 'edit_anggota'],
    'modul'     => ['modul', 'tambah_modul','edit_modul', 'kelola_modul', 'tambah_materi', 'tambah_kuis', 'edit_materi', 'edit_kuis'],
    'kuis'      => ['kuis', 'kelola_kuis']
];
$currentPage = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

//loop pkai menumap untuk menemukan menumain dari halaman saat ini
foreach ($menuMap as $menu => $subPages) {
  if (in_array($currentPage, $subPages)) {
    $mainMenu = $menu;
    break; // hentikan loop kalo udh ketemu
  }
}

// class yang ada di semua link
$baseClass = "flex items-center gap-3 px-4 py-3 rounded-lg font-semibold transition";
// class untuk link yang aktif
$activeClass = "text-white bg-[#6D00A8] shadow";
// class untuk link yang tidak aktif
$inactiveClass = "text-[#1a365d] hover:bg-[#6D00A8] hover:text-white";


?>
<script src="https://cdn.tailwindcss.com"></script>
<aside class="w-64 min-h-screen bg-[#E9D9F2] items-center flex flex-col py-8 px-6">
  <div class="flex items-center gap-3 mb-10">
    <img src="../public/uploads/foto/logo-header.svg" alt="logo" class="w-10">
    <span class="text-2xl font-extrabold text-[#231F42] tracking-wide">codemy</span>
  </div>
  <nav class="flex flex-col gap-2">
    <a href="dashboard.php?page=dashboard" class="<?php echo $baseClass; ?> <?php echo ($mainMenu == 'dashboard') ? $activeClass : $inactiveClass; ?>">
      <i class="fa-solid fa-gauge"></i> 
      Dashboard
    </a>
    <a href="dashboard.php?page=anggota" class="<?php echo $baseClass; ?> <?php echo ($mainMenu == 'anggota') ? $activeClass : $inactiveClass; ?>">
      <i class="fa-regular fa-user"></i> 
      Anggota
    </a>
    <a href="dashboard.php?page=modul" class="<?php echo $baseClass; ?> <?php echo ($mainMenu == 'modul') ? $activeClass : $inactiveClass; ?>">
      <i class="fa-regular fa-file-lines"></i> 
      Modul
    </a>
    <a href="dashboard.php?page=kuis" class="<?php echo $baseClass; ?> <?php echo ($mainMenu == 'kuis') ? $activeClass : $inactiveClass; ?>">
      <i class="fa-regular fa-book"></i> 
      Kuis
    </a>
  </nav>
</aside>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />