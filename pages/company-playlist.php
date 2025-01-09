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
                    <!-- <h3>Playlist</h3> -->
                    <div id="video-playlist"></div>
                </section>
            </div>
        </div>

        <div class="playlist-content-container">
            <?php
                $playlists = array(
                    "1.jpeg" => [
                        "title" => "インフォメーション",
                        "videos" => [
                            ["file" => "砕石フォーラム2024.mp4", "title" => "砕石フォーラム2024", "duration" => "00:00:53"],
                            ["file" => "NHKワールド／中山鉄工所インターン生の活動.mp4", "title" => "NHKワールド／中山鉄工所インターン生の活動", "duration" => "00:08:19"],
                            ["file" => "2022NEW環境展_紹介動画PV.mp4", "title" => "2022NEW環境展_紹介動画PV", "duration" => "00:02:24"],
                            ["file" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4", "title" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像", "duration" => "00:04:09"]
                        ]
                    ],
                    "2.jpeg" => [
                        "title" => "Dendoman",
                        "videos" => [
                            ["file" => "NE100HBJ_PV01_20220607.mp4", "title" => "NE100HBJ_PV01_20220607", "duration" => "00:01:42"],
                            ["file" => "NE200HBJ_PV01_20220607.mp4", "title" => "NE200HBJ_PV01_20220607", "duration" => "00:02:06"],
                            ["file" => "NE200J_油圧緩衝機構PV_03.mp4", "title" => "NE200J_油圧緩衝機構PV_03", "duration" => "00:01:26"],
                            ["file" => "NE100JB_ja_50秒.mp4", "title" => "NE100JB_ja_50秒", "duration" => "00:00:51"],
                            ["file" => "NE100JB_en_30秒.mp4", "title" => "NE100JB_en_30秒", "duration" => "00:00:31"],
                            ["file" => "Dendoman NE100JP.mp4", "title" => "Dendoman NE100JP", "duration" => "00:04:41"],
                            ["file" => "Dendoman series NE100JT.mp4", "title" => "Dendoman series NE100JT", "duration" => "00:02:15"],
                            ["file" => "Dendoman series NE250I.mp4", "title" => "Dendoman series NE250I", "duration" => "00:01:59"],
                            ["file" => "Dendoman　電動自走式クラッシャ　NE200I（インパクトタイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman 電動自走式クラッシャ NE200I (インパクトタイプ)/㈱中山鉄工所", "duration" => "00:02:05"],
                            ["file" => "Dendoman　電動自走式クラッシャ　NE200C（コーンタイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman　電動自走式クラッシャ　NE200C（コーンタイプ）／㈱中山鉄工所", "duration" => "00:06:34"],
                            ["file" => "Dendoman　電動自走式クラッシャ　NE1500WR（ロールタイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman　電動自走式クラッシャ　NE1500WR（ロールタイプ）／㈱中山鉄工所", "duration" => "00:07:55"],
                            ["file" => "Dendoman 電動自走式クラッシャNE280J（ジョータイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman 電動自走式クラッシャNE280J（ジョータイプ）／㈱中山鉄工所", "duration" => "00:03:11"],
                            ["file" => "Dendoman 電動自走式クラッシャ NE250J（ジョータイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman 電動自走式クラッシャ NE250J（ジョータイプ）／㈱中山鉄工所", "duration" => "00:04:28"],
                            ["file" => "Dendoman　電動自走式クラッシャNE100J（ジョータイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman　電動自走式クラッシャNE100J（ジョータイプ）／㈱中山鉄工所", "duration" => "00:05:19"],
                            ["file" => "電動自走式クラッシャＮＥ３００Ｊ _ ㈱中山鉄工所.mp4", "title" => "電動自走式クラッシャＮＥ３００Ｊ _ ㈱中山鉄工所", "duration" => "00:03:56"]
                        ]
                    ],
                    "3.jpeg" => [
                        "title" => "N-LINK",
                        "videos" => [
                            ["file" => "video1.mp4", "title" => "Video 1", "duration" => "00:00:53"],
                            ["file" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4", "title" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像", "duration" => "00:00:53"],
                            ["file" => "Dendoman series NE100JT.mp4", "title" => "Dendoman series NE100JT", "duration" => "00:00:10"],
                            ["file" => "Dendoman series NE250I.mp4", "title" => "Dendoman series NE250I", "duration" => "00:00:53"],
                            ["file" => "N-Link_コスト削減編.mp4", "title" => "N-Link_コスト削減編", "duration" => "00:00:10"],
                            ["file" => "NE100JB_en_30秒.mp4", "title" => "NE100JB_en_30秒", "duration" => "00:00:10"],
                            ["file" => "video8.mp4", "title" => "Video 8", "duration" => "00:00:10"]
                        ]
                    ],
                    "4.jpeg" => [
                        "title" => "Crusher",
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
                    echo '<img src="./files/img/playlists/' . $image . '" alt="' . $playlist["title"] . '">';
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