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
                    <video id="main-video" class="static-size" controls autoplay disablePictureInPicture controlslist="nodownload noplaybackrate">
                        <source src="" id="main-video-source">
                    </video>
                    <h3 id="main-video-title"></h3>
                </section>
                <section class="video-playlist">
                    <h3 id="video-playlist-title">Playlist of</h3>
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
                            ["file" => "2022NEW環境展_紹介動画PV.mp4", "title" => "2022NEW環境展 紹介動画PV", "duration" => "00:02:24"],
                            ["file" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4", "title" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像", "duration" => "00:04:09"]
                        ]
                    ],
                    "2.jpeg" => [
                        "title" => "Dendoman",
                        "videos" => [
                            ["file" => "NE100HBJ_PV01_20220607.mp4", "title" => "NE100HBJ　バッテリー搭載自走式クラッシャ", "duration" => "00:01:42"],
                            ["file" => "NE200HBJ_PV01_20220607.mp4", "title" => "NE200HBJ　バッテリー搭載自走式クラッシャ　CO2排出ゼロ", "duration" => "00:02:06"],
                            ["file" => "NE200J_油圧緩衝機構PV_03.mp4", "title" => "NE200J 油圧緩衝機構", "duration" => "00:01:26"],
                            ["file" => "NE100JB_ja_50秒.mp4", "title" => "NE100JB 完全電動化自走式クラッシャ", "duration" => "00:00:51"],
                            ["file" => "NE100JB_en_30秒.mp4", "title" => "The Battery Crusher NE100JB", "duration" => "00:00:31"],
                            ["file" => "Dendoman NE100JP.mp4", "title" => "Dendoman NE100JP", "duration" => "00:04:41"],
                            ["file" => "Dendoman series NE100JT.mp4", "title" => "Dendoman Series NE100JT", "duration" => "00:02:15"],
                            ["file" => "Dendoman series NE250I.mp4", "title" => "Dendoman Series NE250I", "duration" => "00:01:59"],
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
                        "title" => " Plant Design N-LINK",
                        "videos" => [
                            ["file" => "N-Link_コスト削減編.mp4", "title" => "N-Link コスト削減編", "duration" => "00:00:10"],
                            ["file" => "N-Link_人材不足編.mp4", "title" => "N-Link 人材不足編", "duration" => "00:00:10"],
                            ["file" => "N-Link_環境改善編.mp4", "title" => "N-Link 環境改善編", "duration" => "00:00:10"]
                        ]
                    ],
                    "4.jpeg" => [
                        "title" => "Crusher",
                        "videos" => [
                            ["file" => "不燃物破砕専用機 FRシリーズ　フリッタ.mp4", "title" => "不燃物破砕専用機 FRシリーズ　フリッタ", "duration" => "00:00:53"],
                            ["file" => "VSI_ja_20210610.mp4", "title" => "リバーシブル竪型破砕・整粒機　ジャイロパクタ　NAKAYAMA VSI", "duration" => "00:00:53"],
                            ["file" => "2軸パワーロール破砕機 PRC1500P／2-shaft Power Roll Crusher ｜ (株)中山鉄工所.mp4", "title" => "2軸パワーロール破砕機 PRC1500P／2-shaft Power Roll Crusher ｜ (株)中山鉄工所", "duration" => "00:00:10"],
                            ["file" => "2軸解砕機 パワーロールクラッシャ　PRC1200P／㈱中山鉄工所.mp4", "title" => "2軸解砕機 パワーロールクラッシャ　PRC1200P／㈱中山鉄工所", "duration" => "00:00:53"],
                            ["file" => "PRC1500P エンジン解砕.mp4", "title" => "PRC1500P エンジン解砕", "duration" => "00:00:10"],
                            ["file" => "破砕能力2倍のネオコーン破砕機／Neo Cone Crusher｜ (株)中山鉄工所.mp4", "title" => "破砕能力2倍のネオコーン破砕機／Neo Cone Crusher｜ (株)中山鉄工所", "duration" => "00:00:10"],
                            ["file" => "ジョー破砕機　AC Series／Jaw Crusher｜ (株)中山鉄工所.mp4", "title" => "ジョー破砕機　AC Series／Jaw Crusher｜ (株)中山鉄工所", "duration" => "00:00:10"]
                        ]
                    ],
                    "5.jpeg" => [
                        "title" => "Screen Feeder",
                        "videos" => [
                            ["file" => "リサイクル切替式選別機　NRE4122.mp4", "title" => "リサイクル切替式選別機　NRE4122", "duration" => "00:00:53"],
                            ["file" => "小型選別機　PBS750,PBS900／Screen Kits｜ (株)中山鉄工所.mp4", "title" => "小型選別機　PBS750,PBS900／Screen Kits｜ (株)中山鉄工所", "duration" => "00:00:53"]
                        ]
                    ],
                    "6.jpeg" => [
                        "title" => "Crusher Unit",
                        "videos" => [
                            ["file" => "(株)中山鉄工所／可搬式破砕機　HC320GX.mp4", "title" => "(株)中山鉄工所／可搬式破砕機　HC320GX", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／可搬式破砕機　HFA4M.mp4", "title" => "(株)中山鉄工所／可搬式破砕機　HFA4M", "duration" => "00:00:53"]
                        ]
                    ],
                    "7.jpeg" => [
                        "title" => "Suction Air Separator Unit",
                        "videos" => [
                            ["file" => "(株)中山鉄工所／ゴミ吸引選別ユニット　NAS1200.mp4", "title" => "(株)中山鉄工所／ゴミ吸引選別ユニット　NAS1200", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／ゴミ吸引選別式ユニット　NAS900.mp4", "title" => "(株)中山鉄工所／ゴミ吸引選別式ユニット　NAS900", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／吸選機　AS1500.mp4", "title" => "(株)中山鉄工所／吸選機　AS1500", "duration" => "00:00:10"],
                            ["file" => "(株)中山鉄工所／ゴミ吸引選別　ASシリーズ.mp4", "title" => "(株)中山鉄工所／ゴミ吸引選別　ASシリーズ", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／NASシリーズ.mp4", "title" => "(株)中山鉄工所／NASシリーズ", "duration" => "00:00:10"]
                        ]
                    ],
                    "8.jpeg" => [
                        "title" => "Soil Remediation",
                        "videos" => [
                            ["file" => "(株)中山鉄工所／土壌土質改良機　MGM501E.mp4", "title" => "(株)中山鉄工所／土壌土質改良機　MGM501E", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／土壌土質改良機　ZGMシリーズ.mp4", "title" => "(株)中山鉄工所／土壌土質改良機　ZGMシリーズ", "duration" => "00:00:53"]
                        ]
                    ],
                    "9.jpeg" => [
                        "title" => "Attachment",
                        "videos" => [
                            ["file" => "(株)中山鉄工所／BH70.mp4", "title" => "(株)中山鉄工所／BH70", "duration" => "00:00:53"]
                        ]
                    ],
                    "10.jpeg" => [
                        "title" => "Environment",
                        "videos" => [
                            ["file" => "ND50 2007.mp4", "title" => "ND50 2007", "duration" => "00:00:53"],
                            ["file" => "老竹発電.mp4", "title" => "老竹発電", "duration" => "00:00:53"],
                            ["file" => "松隈小水力.mp4", "title" => "松隈小水力", "duration" => "00:00:10"],
                            ["file" => "(株)中山鉄工所／Ne-Power紹介動画.mp4", "title" => "(株)中山鉄工所／Ne-Power紹介動画", "duration" => "00:00:53"]
                        ]
                    ],
                    "11.jpeg" => [
                        "title" => "3DCG",
                        "videos" => [
                            ["file" => "(株)中山鉄工所／コーンクラッシャ NSCシリーズ.mp4", "title" => "(株)中山鉄工所／コーンクラッシャ NSCシリーズ", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／スクリーン NSRシリーズ.mp4", "title" => "(株)中山鉄工所／スクリーン NSRシリーズ", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／ジャイロパクタ SRシリーズ.mp4", "title" => "(株)中山鉄工所／ジャイロパクタ SRシリーズ", "duration" => "00:00:10"],
                            ["file" => "(株)中山鉄工所／ジョークラッシャ AC、RCシリーズ.mp4", "title" => "(株)中山鉄工所／ジョークラッシャ AC、RCシリーズ", "duration" => "00:00:53"],
                            ["file" => "(株)中山鉄工所／インパクトクラッシャ ACDシリーズ.mp4", "title" => "(株)中山鉄工所／インパクトクラッシャ ACDシリーズ", "duration" => "00:00:10"],
                            ["file" => "(株)中山鉄工所／ロールクラッシャ PRCシリーズ.mp4", "title" => "(株)中山鉄工所／ロールクラッシャ PRCシリーズ", "duration" => "00:00:10"],
                            ["file" => "(株)中山鉄工所／油圧緩衝機構 説明.mp4", "title" => "(株)中山鉄工所／油圧緩衝機構 説明", "duration" => "00:00:10"]
                        ]
                    ],
                    "12.jpeg" => [
                        "title" => "Compact Construction Machine",
                        "videos" => [
                            ["file" => "(株)中山鉄工所／MSD700.mp4", "title" => "(株)中山鉄工所／MSD700", "duration" => "00:00:53"]
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