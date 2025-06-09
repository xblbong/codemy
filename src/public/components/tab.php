<?php
// You could add PHP logic here for dynamic content
$categories = ['category1', 'category2', 'category3', 'category4', 'category5', 'category6'];
$categorieData = [
    ['title' => 'Categorie', 'items' => ['2019-2020', '2020-2021']],
    ['title' => 'Categorie', 'items' => ['2019-2020', '2020-2021']],
    ['title' => 'Categorie', 'items' => ['2020-2020', '2021-2021']]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Same head content as above -->
</head>
<body class="bg-codemy-light font-sans">
    <!-- Hero Section (same as above) -->
    
    <!-- Dynamic Categories -->
    <div class="container mx-auto max-w-4xl py-8 px-4">
        <div class="flex flex-wrap gap-4 mb-8">
            <?php foreach ($categories as $category): ?>
                <span class="bg-white px-4 py-2 rounded shadow"><?= $category ?></span>
            <?php endforeach; ?>
        </div>

        <h2 class="text-2xl font-bold mb-6 text-codemy-dark">CARABOT</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($categorieData as $categorie): ?>
                <div class="bg-white p-4 rounded-lg shadow">
                    <h3 class="font-bold mb-2"><?= $categorie['title'] ?></h3>
                    <?php foreach ($categorie['items'] as $item): ?>
                        <p><?= $item ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>