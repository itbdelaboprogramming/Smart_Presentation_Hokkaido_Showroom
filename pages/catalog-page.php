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
                $pdfList = array(
                    "PDF 1" => "Micro_Hydro.pdf",
                    "PDF 2" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 3" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 4" => "Micro_Hydro.pdf",
                    "PDF 5" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 6" => "Micro_Hydro.pdf",
                    "PDF 7" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 8" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 9" => "Micro_Hydro.pdf",
                    "PDF 10" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 11" => "SR_en_ver.2.06_20220523.pdf",
                    "PDF 12" => "Micro_Hydro.pdf"
                );
                foreach ($pdfList as $pdfTitle => $pdfPath) {
            ?>
                <div class="pdf-background">
                    <div class="pdf-card" data-pdf="./files/pdf/<?php echo $pdfPath; ?>">
                        <object data="./files/pdf/<?php echo $pdfPath; ?>#toolbar=0" type="application/pdf">
                            alt : <a href="/files/pdf/<?php echo $pdfPath; ?>"><?php echo $pdfPath; ?></a>
                        </object>
                    </div>
                    <div class="video-title"><?php echo $pdfTitle; ?></div>
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
