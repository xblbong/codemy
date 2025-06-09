<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
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
    
    <div class="w-full max-w-5xl mx-auto mt-12 p-4">
        <h2 class="text-xl md:text-2xl font-bold text-white mb-1">Quiz: Chatbot Technology</h2>
        <p class="text-[#E0D7F3] text-sm mb-6">Kerjakan soal berikut untuk menguji pemahamanmu tentang materi ini.</p>
        <div class="bg-[#211B36] border border-[#A259FF] rounded-xl flex flex-col md:flex-row overflow-hidden shadow-lg">
            <!-- Kiri: Soal & Pilihan -->
            <div class="flex-1 p-8 flex flex-col justify-between">
                <!-- Judul & Deskripsi Quiz -->
                <div>
                    <div class="text-white text-base md:text-lg font-medium mb-4 flex items-start gap-2">
                        <span>1.</span>
                        <span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin condimentum aliquet arcu, sit amet eleifend tortor. Donec elementum enim quis ligula laoreet convallis. Fusce sodales ligula</span>
                    </div>
                    <div class="flex flex-col gap-3 mt-6">
                        <label class="flex items-center gap-2 cursor-pointer text-white text-base">
                            <input type="radio" name="jawaban" class="accent-[#A259FF]" />
                            <span>Pilihan jawaban A</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-white text-base">
                            <input type="radio" name="jawaban" class="accent-[#A259FF]" />
                            <span>Pilihan jawaban B</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-white text-base">
                            <input type="radio" name="jawaban" class="accent-[#A259FF]" />
                            <span>Pilihan jawaban C</span>
                        </label>
                    </div>
                </div>
                <div class="flex justify-end mt-8">
                    <button class="flex items-center gap-2 bg-white text-[#31004C] border border-[#31004C] px-4 py-1.5 rounded transition hover:bg-[#A259FF] hover:text-white font-semibold text-sm">
                        Selanjutnya
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 12l-6.5 6.5m0 0l6.5-6.5m-6.5 6.5V5.75" /></svg>
                    </button>
                    <button onclick="window.location.href='skor.php'" class="flex items-center gap-2 bg-white text-[#31004C] border border-[#31004C] px-4 py-1.5 rounded transition hover:bg-[#A259FF] hover:text-white font-semibold text-sm">
                        Finish
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 12l-6.5 6.5m0 0l6.5-6.5m-6.5 6.5V5.75" /></svg>
                    </button>
                </div>
            </div>
            <!-- Kanan: Timer & Nomor Soal -->
            <div class="w-full md:w-64 border-l border-[#A259FF] flex flex-col items-center justify-start p-8 bg-[#211B36]">
                <div class="text-white text-lg font-semibold mb-4">03:25</div>
                <div class="grid grid-cols-4 gap-2">
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">1</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">2</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">3</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">4</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">5</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">6</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">7</button>
                    <button class="w-10 h-10 border border-white rounded bg-transparent text-white font-semibold">8</button>
                </div>
            </div>
        </div>
    </div>
</body>
</html>