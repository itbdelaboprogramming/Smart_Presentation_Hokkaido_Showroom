<!DOCTYPE html>
<html lang="en">
<head>
    <title>スマート・プレゼンテーション</title>
    <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/catalog-page.css">
</head>
<body>
    <div>
        <div class="container-top-left">
            <button class="menu-container-back-button" onClick="location.href='home'">
                <img src="./assets/Back-Button.svg"> Back
            </button>
            <div class="page-name-container">
                <div class="page-name-text">Catalog Page​</div>
            </div>
        </div>
        <div class="pdf-content-container">
            <?php
                   for ($i = 0; $i < 5; $i++) {
            ?>
                <div class="pdf-card" data-pdf="./files/pdf/Micro_Hydro.pdf">
                    <!-- <iframe src="./files/pdf/Micro_Hydro.pdf#toolbar=0" width="370" height="486" frameborder="0" ></iframe> -->
                    <object data="./files/pdf/Micro_Hydro.pdf#toolbar=0" type="application/pdf" width="374" height="486">
                        alt : <a href="/files/pdf/Micro_Hydro.pdf">Micro_Hydro.pdf</a>
                    </object>
                    <div class="overlay"></div>
                </div>
                <div class="pdf-card" data-pdf="./files/pdf/SR_en_ver.2.06_20220523.pdf">
                    <!-- <iframe src="./files/pdf/SR_en_ver.2.06_20220523.pdf#toolbar=0" width="370" height="486" frameborder="0" ></iframe> -->
                    <object data="./files/pdf/SR_en_ver.2.06_20220523.pdf#toolbar=0" type="application/pdf" width="374" height="486">
                        alt : <a href="/files/pdf/SR_en_ver.2.06_20220523.pdf#toolbar=0">SR_en_ver.2.06_20220523.pdf</a>
                    </object>
                    <div class="overlay"></div>
                </div>
            <?php
                }
            ?>
        </div>
        <div class="bottom-center">
            <div class="text-page">PDF のタイトルをクリックすると、より見やすくなります。</div>
        </div>
        <div class="container-full-screen-pdf">
            <div class="pdf-pop-up-container" id="pdf-pop-up-container">
                <embed id="pdf-file" type="application/pdf" width="100%" height="100%"/>
            </div>
        </div>
    </div>
    <script type="module" src="./js/catalog-page.js"></script>
</body>
</html>
