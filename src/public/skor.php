<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Skor Quiz</title>
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
<body class="min-h-screen flex flex-col bg-gradient-to-br from-[#58287D] via-[#31004C] to-[#0C0B17] font-sans">
    <div class="flex-1 flex items-center justify-center py-16">
        <div class="w-full max-w-3xl mx-auto">
            <div class="bg-[#211B36] border border-[#A259FF] rounded-xl p-8 md:p-12 flex flex-col items-center shadow-lg">
                <div class="w-full text-center mb-8">
                    <span class="text-white text-base md:text-lg">Tanggal Ujian
                        <span class="ml-2 text-[#FFB800] font-semibold">
                            <?php echo date('d M Y \p\u\k\u\l H:i:s'); ?>
                        </span>
                    </span>
                </div>
                <div class="w-full grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-0 text-center">
                    <div>
                        <div class="text-[#E0D7F3] text-lg md:text-xl mb-2">Total Soal</div>
                        <div class="text-white text-5xl md:text-6xl font-bold">
                            <?php echo isset($total_soal) ? $total_soal : 8; ?>
                        </div>
                    </div>
                    <div>
                        <div class="text-[#E0D7F3] text-lg md:text-xl mb-2">Skor</div>
                        <div class="text-white text-5xl md:text-6xl font-bold">
                            <?php echo isset($skor) ? $skor : 7; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>