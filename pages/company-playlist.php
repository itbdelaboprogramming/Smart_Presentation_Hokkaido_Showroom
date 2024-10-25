<!DOCTYPE html>
<html lang="en">
<head>
    <title>スマート・プレゼンテーション</title>
    <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/company-playlist.css">
</head>
<body>
    <script type="module" src="./js/company-playlist.js"></script>
    <div id="playlist-overlay" class="hidden">
        <div id="playlist-container"></div>
    </div>
    <div class="company-page">
        <div class="container-top-left">
            <button class="menu-container-back-button" onClick="location.href='home'">
                <img src="./assets/Back-Button.svg"> Back
            </button>
            <div class="page-name-container">
                <div class="page-name-text">Lorem Ipsum</div>
            </div>
        </div>
        <div class="playlist-content-container">
            <?php
                $playlists = array(
                    "playlist-card-1" => [
                        "image" => "2.png",
                        "title" => "インフォメーション",
                        "iframe" => '<iframe class="playlist-iframe" src="https://www.youtube.com/embed/videoseries?si=o6u2D9KPEwrqaV8V&amp;list=PLxI9XPCxiURH7h_E6QMvjYRZSEKwX2CFJ&autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
                    ],
                    "playlist-card-2" => [
                        "image" => "1.png",
                        "title" => "Dendoman",
                        "iframe" => '<iframe class="playlist-iframe" src="https://www.youtube.com/embed/videoseries?si=PWMCUEK1A77sk__S&amp;list=PLxI9XPCxiUREQLEkfxpPXVD923L_SHh_N&autoplay=1" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
                    ],
                );
                foreach ($playlists as $id => $data) {
                    $image = $data["image"];
                    $title = $data["title"];
                    $iframe = $data["iframe"];
                    echo <<<EOD
                    <div class="playlist-card" id="$id" data-iframe='$iframe'>
                        <img class="playlist-thumbnail" src="/files/img/playlists/$image"/>
                        <div class="playlist-title">$title</div>
                    </div>
                    EOD;
                }
            ?>
        </div>
        <div class="bottom-center">
            <div class="text-page">Lorem ipsum odor amet, consectetuer adipiscing elit.</div>
        </div>
    </div>
</body>
</html>
