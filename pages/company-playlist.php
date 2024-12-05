<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Presentation - Playlist</title>
    <link rel="stylesheet" href="./style/company-playlist.css">
    <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
</head>
<body>
    <div class="company-page">
        <div class="container-top-left">
            <button class="menu-container-back-button" onclick="location.href='company-video'">
                <img src="./assets/Back-Button.svg"> Back
            </button>
            <div class="page-name-container">
                <div class="page-name-text">Video Playlist</div>
            </div>
        </div>

        <div class="video-player-overlay hidden" id="video-player-overlay">
            <div class="video-player-container">
                <!-- <button class="close-button" id="close-button">X</button> -->
                <section class="main-video">
                    <video id="main-video" class="static-size" controls autoplay disablePictureInPicture controlslist="nodownload noplaybackrate nofullscreen">
                        <source src="" id="main-video-source">
                    </video>
                    <h3 id="main-video-title"></h3>
                </section>
                <section class="video-playlist">
                    <h3>Playlist</h3>
                    <div id="video-playlist"></div>
                </section>
            </div>
        </div>

        <div class="playlist-content-container">
            <?php
                $playlists = array(
                    "1.jpg" => [
                        "title" => "Playlist 1",
                        "videos" => [
                            ["file" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4", "title" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像", "duration" => "00:00:53"],
                            ["file" => "Dendoman series NE100JT.mp4", "title" => "Dendoman series NE100JT", "duration" => "00:00:10"],
                            ["file" => "Dendoman series NE250I.mp4", "title" => "Dendoman series NE250I", "duration" => "00:00:53"],
                            ["file" => "N-Link_コスト削減編.mp4", "title" => "N-Link_コスト削減編", "duration" => "00:00:10"],
                            ["file" => "NE100JB_en_30秒.mp4", "title" => "NE100JB_en_30秒", "duration" => "00:00:10"],
                            ["file" => "vコンパクト小水力発電システム Mission to Zero-emission Micro Hydro System.mp4", "title" => "コンパクト小水力発電システム Mission to Zero-emission Micro Hydro System", "duration" => "00:00:10"],
                            ["file" => "video7.mp4", "title" => "Video 7", "duration" => "00:00:10"],
                            ["file" => "video8.mp4", "title" => "Video 8", "duration" => "00:00:10"]
                        ]
                    ],
                    "2.jpg" => [
                        "title" => "Playlist 2",
                        "videos" => [
                            ["file" => "video1.mp4", "title" => "Video 1", "duration" => "00:00:53"],
                            ["file" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4", "title" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像", "duration" => "00:00:53"],
                            ["file" => "Dendoman series NE100JT.mp4", "title" => "Dendoman series NE100JT", "duration" => "00:00:10"],
                            ["file" => "Dendoman series NE250I.mp4", "title" => "Dendoman series NE250I", "duration" => "00:00:53"],
                            ["file" => "N-Link_コスト削減編.mp4", "title" => "N-Link_コスト削減編", "duration" => "00:00:10"],
                            ["file" => "NE100JB_en_30秒.mp4", "title" => "NE100JB_en_30秒", "duration" => "00:00:10"],
                            ["file" => "video8.mp4", "title" => "Video 8", "duration" => "00:00:10"]
                        ]
                    ]
                );

                foreach ($playlists as $image => $playlist) {
                    echo '<div class="playlist-card" data-videos=\'' . json_encode($playlist["videos"]) . '\'>';
                    echo '<div class="card-content">';
                    echo '<img src="./files/img/' . $image . '" alt="' . $playlist["title"] . '">';
                    echo '<div class="playlist-title">' . $playlist["title"] . '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            ?>
        </div>

    </div>

    <script src="./js/company-playlist.js"></script>
</body>
</html>
