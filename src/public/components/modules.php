<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Codemy</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Lexend:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'dark-blue': '#1a365d',
                    },
                    fontFamily: {
                        sans: ['Lexend', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body>
         <!-- Sidebar Modul -->
         <aside class="w-full md:w-[300px] flex-shrink-0 border-2 border-gray-300 rounded-sm bg-[#31004C]">
                <div class="flex flex-col gap-2 mb-3 py-3 px-4">
                    <h4 class="text-white font-semibold mb-2">Progress Belajar</h4>
                    <progress class="w-full h-2 [&::-webkit-progress-bar]:rounded [&::-webkit-progress-value]:rounded [&::-webkit-progress-bar]:bg-gray-300 [&::-webkit-progress-value]:bg-[#FFF]" value="<?php echo $user_progress; ?>" max="100"></progress>
                </div>
                <!-- Modul 1 -->
                <div class="mb-3">
                    <h3 class="text-[#FFF] font-semibold mb-2 bg-[#6D00A8] rounded-sm py-3 px-4">Modul 1</h3>
                    <div class="flex flex-col gap-2 py-3 px-4">
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 1</button>
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 2</button>
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 3</button>
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 4</button>
                    </div>
                </div>
                <!-- Modul 2 -->
                <div class="mb-3">
                    <h3 class="text-[#FFF] font-semibold mb-2 bg-[#6D00A8] rounded-sm py-3 px-4">Modul 2</h3>
                    <div class="flex flex-col gap-2 py-3 px-4">
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 1</button>
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 2</button>
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 3</button>
                        <button class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Sub Modul 4</button>
                    </div>
                </div>
                <!-- Final Quiz -->
                <div class="mb-3">
                    <h3 class="text-[#FFF] font-semibold mb-2 bg-[#6D00A8] rounded-sm py-3 px-4">Final Quiz</h3>
                    <div class="flex flex-col gap-2 py-3 px-4">
                        <button onclick="window.location.href='quiz.php'" class="text-left bg-[#fff] hover:bg-[#58287D] py-2 px-4 rounded cursor-pointer transition text-[#1A1732] hover:text-[#fff] text-sm">Quiz Akhir</button>
                    </div>
                </div>
            </aside>

</body>
</html>