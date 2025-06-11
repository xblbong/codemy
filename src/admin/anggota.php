<?php
$query = "SELECT id_pengguna, nama, email, peran, alamat, no_hp from pengguna order by nama asc";
$result = mysqli_query($koneksi, $query);
?>

<div class="bg-white p-6 md:p-8 rounded-xl shadow-md">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
        <div class="flex flex-col items-left gap-2">
            <h2 class="text-2xl font-bold text-codemy-dark mb-2">Data Anggota</h2>
            <div class="flex items-center gap-4 w-full md:w-auto ">
                <div class="relative flex-1 md:flex-initial">
                    <input type="text" placeholder="Cari pengguna..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-hover">
                    <i class="fa-solid fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
        </div>
        <!-- <button class="bg-[#6D00A8] text-white px-4 py-2 rounded-lg font-semibold flex items-center gap-2 hover:bg-primary-hover transition-colors duration-300">
            <span class="hidden md:inline">Tambah Anggota</span>
            <i class="fa-solid fa-plus"></i>
        </button> -->
    </div>

    <!-- Tabel Data Anggota -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="bg-slate-100 text-slate-600 uppercase">
                <tr>
                    <th class="p-4 rounded-l-lg">No</th>
                    <th class="p-4">Nama</th>
                    <th class="p-4">Email</th>
                    <th class="p-4">No. HP</th>
                    <th class="p-4">Role</th>
                    <th class="p-4 text-center rounded-r-lg">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // cek apa query berhasil dan mengembalikan baris data
                if ($result && mysqli_num_rows($result) > 0):
                    $nomor = 1; // variabel untuk nomor urut
                    // loop setiap baris data
                    while ($row = mysqli_fetch_assoc($result)):
                ?>
                        <tr class="border-b border-gray-200 hover:bg-slate-50">
                            <td class="p-4 font-medium text-slate-800"><?php echo $nomor++; ?></td>
                            <td class="p-4 font-medium text-dark">
                                <!-- nampilin nama pengguna -->
                                <?php echo htmlspecialchars($row['nama']); ?>
                            </td>
                            <td class="p-4 text-slate-600">
                                <!-- nampilin email pengguna -->
                                <?php echo htmlspecialchars($row['email']); ?>
                            </td>
                            <td class="p-4 text-slate-600">
                                <!-- nampilin nomor HP, beri tanda '-' jika kosong -->
                                <?php echo $row['no_hp'] ? htmlspecialchars($row['no_hp']) : '-'; ?>
                            </td>
                            <td class="p-4 text-slate-600">
                                <!-- nampilin peran dengan huruf kapital di awal -->
                                <?php if ($row['peran'] == 'admin'): ?>
                                    <span class="bg-primary/10 text-primary font-semibold text-xs px-2 py-1 rounded-full">
                                        <?php echo ucfirst($row['peran']); ?>
                                    </span>
                                <?php else: ?>
                                    <span class="bg-slate-100 text-slate-600 font-semibold text-xs px-2 py-1 rounded-full">
                                        <?php echo ucfirst($row['peran']); ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center">
                                <a href="edit_anggota.php?id=<?php echo $row['id_pengguna']; ?>" class="text-blue-500 hover:text-blue-700 mr-4" title="Edit">
                                    <i class="fa-solid fa-pencil"></i>
                                </a>
                                <a href="hapus_anggota.php?id=<?php echo $row['id_pengguna']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini? Semua data terkait akan ikut terhapus.');" class="text-danger hover:text-red-700" title="Hapus">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                    endwhile;
                else:
                    ?>
                    <!-- Tampilan jika tidak ada data sama sekali -->
                    <tr>
                        <td colspan="6" class="p-8 text-center text-slate-500">
                            Belum ada data pengguna.
                        </td>
                    </tr>
                <?php
                endif;
                ?>
            </tbody>
        </table>
    </div>
</div>