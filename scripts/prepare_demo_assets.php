<?php
// scripts/prepare_demo_assets.php

$baseHero = 'C:/Users/Mrinmoy/.gemini/antigravity-ide/brain/3852848b-ce52-487e-8f28-fd1bb79a562d/azure_resort_hero_1790752492408.jpg';
$baseLobby = 'C:/Users/Mrinmoy/.gemini/antigravity-ide/brain/3852848b-ce52-487e-8f28-fd1bb79a562d/grand_azure_lobby_1790752523838.jpg';
$basePool = 'C:/Users/Mrinmoy/.gemini/antigravity-ide/brain/3852848b-ce52-487e-8f28-fd1bb79a562d/azure_infinity_pool_1790752549255.jpg';

$destHotels = 'storage/app/public/hotels/1';
$destMenu = 'storage/app/public/menu/1';
$destRooms = 'storage/app/public/rooms';

@mkdir($destHotels, 0755, true);
@mkdir($destMenu, 0755, true);
@mkdir("$destRooms/101", 0755, true);
@mkdir("$destRooms/102", 0755, true);
@mkdir("$destRooms/201", 0755, true);

// 1. Copy Master Hotel Assets
if (file_exists($baseHero)) {
    copy($baseHero, "$destHotels/hero_cover.jpg");
}
if (file_exists($baseLobby)) {
    copy($baseLobby, "$destHotels/lobby.jpg");
}
if (file_exists($basePool)) {
    copy($basePool, "$destHotels/pool.jpg");
}

// 2. Generate Derived Luxury Hotel Gallery Images with GD
function cropAndSave($srcPath, $destPath, $targetW, $targetH, $filter = null, $tint = null) {
    if (!file_exists($srcPath)) return;
    $src = imagecreatefromjpeg($srcPath);
    if (!$src) return;

    $origW = imagesx($src);
    $origH = imagesy($src);

    $dst = imagecreatetruecolor($targetW, $targetH);
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $targetW, $targetH, $origW, $origH);

    if ($filter !== null) {
        imagefilter($dst, $filter, 10);
    }
    if ($tint) {
        imagefilter($dst, IMG_FILTER_COLORIZE, $tint[0], $tint[1], $tint[2], $tint[3] ?? 0);
    }

    imagejpeg($dst, $destPath, 92);
    imagedestroy($src);
    imagedestroy($dst);
}

// Generate Spa (warm ambient tone from lobby/pool)
cropAndSave($baseLobby, "$destHotels/spa.jpg", 1200, 675, IMG_FILTER_SMOOTH, [30, 15, -10, 0]);

// Generate Dining (rich restaurant ambiance from hero)
cropAndSave($baseHero, "$destHotels/dining.jpg", 1200, 675, null, [20, 10, -5, 0]);

// Generate Penthouse / Suite (luxury suite from pool/ocean terrace)
cropAndSave($basePool, "$destHotels/penthouse.jpg", 1200, 675, null, [10, 10, 15, 0]);

// Room specific covers
copy("$destHotels/pool.jpg", "$destRooms/101/cover.jpg");
copy("$destHotels/lobby.jpg", "$destRooms/102/cover.jpg");
copy("$destHotels/penthouse.jpg", "$destRooms/201/cover.jpg");

// 3. Generate Fine Dining Culinary Cards for Menu Items
function createDishCard($destPath, $title, $sub, $bgColors, $accentColor) {
    $w = 800;
    $h = 600;
    $img = imagecreatetruecolor($w, $h);

    // Gradient Background
    for ($y = 0; $y < $h; $y++) {
        $ratio = $y / $h;
        $r = (int)($bgColors[0][0] + $ratio * ($bgColors[1][0] - $bgColors[0][0]));
        $g = (int)($bgColors[0][1] + $ratio * ($bgColors[1][1] - $bgColors[0][1]));
        $b = (int)($bgColors[0][2] + $ratio * ($bgColors[1][2] - $bgColors[0][2]));
        $lineColor = imagecolorallocate($img, $r, $g, $b);
        imageline($img, 0, $y, $w, $y, $lineColor);
    }

    // Concentric Luxury Rings / Platter Motif
    $gold = imagecolorallocate($img, $accentColor[0], $accentColor[1], $accentColor[2]);
    $white = imagecolorallocate($img, 255, 255, 255);
    $slate = imagecolorallocate($img, 203, 213, 225);
    $glow = imagecolorallocatealpha($img, $accentColor[0], $accentColor[1], $accentColor[2], 95);

    imagefilledellipse($img, 400, 240, 360, 360, $glow);
    imagearc($img, 400, 240, 320, 320, 0, 360, $gold);
    imagearc($img, 400, 240, 300, 300, 0, 360, $gold);
    imagearc($img, 400, 240, 240, 240, 0, 360, $white);

    // Cloche / Dish Icon Graphic
    imagearc($img, 400, 250, 160, 140, 180, 360, $gold);
    imageline($img, 310, 250, 490, 250, $gold);
    imagefilledellipse($img, 400, 175, 24, 24, $gold);

    // Subtle Stars / Diamonds
    imagefilledellipse($img, 340, 210, 6, 6, $gold);
    imagefilledellipse($img, 460, 210, 6, 6, $gold);

    // Category Eyebrow
    imagestring($img, 4, 330, 410, "AZURE IN-ROOM DINING", $gold);

    // Dish Title
    $titleX = max(40, (int)(400 - (strlen($title) * 9 * 0.7)));
    imagestring($img, 5, $titleX, 445, strtoupper($title), $white);

    // Subtitle / Notes
    $subX = max(40, (int)(400 - (strlen($sub) * 8 * 0.6)));
    imagestring($img, 4, $subX, 480, $sub, $slate);

    // Price / Quality Guarantee
    imagestring($img, 3, 315, 520, "★ MICHELIN RECOGNIZED ★", $gold);

    imagejpeg($img, $destPath, 92);
    imagedestroy($img);
}

createDishCard(
    "$destMenu/burger.jpg",
    "Prime Wagyu Cheeseburger",
    "Aged Cheddar, Caramelised Onions & Truffle Fries",
    [[15, 23, 42], [30, 41, 59]],
    [217, 119, 6]
);

createDishCard(
    "$destMenu/seabass.jpg",
    "Pan-Seared Chilean Sea Bass",
    "Saffron Risotto, Asparagus & Lemon Beurre Blanc",
    [[15, 23, 42], [13, 148, 136]],
    [234, 179, 8]
);

createDishCard(
    "$destMenu/calamari.jpg",
    "Crispy Calamari & Lime Aioli",
    "Flash-Fried with Smoked Paprika Salt & Citrus Dip",
    [[24, 24, 27], [63, 63, 70]],
    [245, 158, 11]
);

createDishCard(
    "$destMenu/fries.jpg",
    "Truffle & Parmesan Pommes Frites",
    "White Truffle Oil, Aged Pecorino & Fresh Herbs",
    [[24, 24, 27], [88, 28, 135]],
    [217, 119, 6]
);

createDishCard(
    "$destMenu/mocktail.jpg",
    "Tropical Azure Mocktail",
    "Passion Fruit, Cold-Pressed Pineapple & Coconut",
    [[15, 23, 42], [2, 132, 199]],
    [56, 189, 248]
);

echo "All demo media assets successfully generated and prepared in storage/app/public!\n";
