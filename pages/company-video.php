

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
                    for ($i = 0; $i < 8; $i++) {
                ?>
                        <div class="video-card">
                            <video src="./files/video/中山鉄工所ジャイロパクタ SRシリーズ.mp4" width="100%" height="auto" type="video/mp4" controls disablePictureInPicture controlslist="nodownload noplaybackrate"></video>
                        </div>
                <?php
                    }
                ?>

            </div>
        </div>
    </body>
</html>