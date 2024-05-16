<!DOCTYPE html>
<html lang="en">
    <head>
        <title>スマート・プレゼンテーション</title>
        <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="./style/company-video.css" >
    </head>
    <body>
        <div class="company-page">
            <div class="container-top-left">
                <button class="menu-container-back-button" onClick="location.href='home'">
                    <img src="./assets/Back-Button.svg">
                    Back
                </button>
                <div class="page-name-container">
                    <div class="page-name-text">動画一覧​</div>
                </div>
            </div>
            <div class="video-content-container">
                <?php
                    // // Array of video file names and corresponding titles
                    // $videoFiles = array(
                    //     "1. Dendomanシリーズ.mp4" => "Dendomanシリーズ",
                    //     "2. NE100HBJ.mp4" => "NE100HBJ",
                    //     "3. NE100JB.mp4" => "NE100JB",
                    //     "4. NE200HBJ.mp4" => "NE200HBJ",
                    //     "5. NE200J.mp4" => "NE200J",
                    //     "6. VSI.mp4" => "VSI",
                    //     "7. ゴミ吸引選別_ASシリーズ.mp4" => "ゴミ吸引選別_ASシリーズ",
                    //     "8. バケットハンマーBH70.mp4" => "バケットハンマーBH70"
                    // );

                    // Array of video file names and corresponding titles
                    $videoFiles = array(
                        "1. Dendomanシリーズ.mp4" => "Dendomanシリーズ",
                        "2. NE100HBJ.mp4" => "NE100HBJ",
                        "3. NE100JB.mp4" => "NE100JB",
                        "4. NE200HBJ.mp4" => "NE200HBJ",
                        "5. NE200J.mp4" => "NE200J",
                        "6. VSI.mp4" => "VSI",
                        "7. ゴミ吸引選別_ASシリーズ.mp4" => "ゴミ吸引選別_ASシリーズ",
                        "中山鉄工所ジャイロパクタ SRシリーズ.mp4" => "バケットハンマーBH70"
                    );

                    // Loop to generate HTML for each video card
                    foreach ($videoFiles as $fileName => $title) {
                        // Generating file path for each video
                        $filePath = "./files/video/" . $fileName;
                ?>
                        <div class="video-card">
                            <video src="<?php echo $filePath; ?>" width="100%" height="auto" type="video/mp4" controls disablePictureInPicture controlslist="nodownload noplaybackrate"></video>
                            <img style="display:none;" class="information-close" src="./assets/x_btn.svg" />
                            <div class="video-title"><?php echo $title; ?></div>
                        </div>
                <?php
                    }
                ?>
            </div>
            <div class="bottom-center">
                <div class="text-page">ビデオのタイトルをクリックすると、より見やすくなります。</div>
            </div>
        </div>
        <script type="module" src="./js/company-video.js"></script>
    </body>
</html>
