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
                <img src="./assets/Back-Button.svg" alt="back"> Back
            </button>
            <div class="page-name-container">
                <div class="page-name-text">カタログ一覧​</div>
            </div>
        </div>
        <div class="pdf-content-container">
            <?php
                $pdfList = array(
                    "会社案内" => "会社案内.pdf",
                    "総合カタログ" => "総合カタログ.pdf",
                    "Dendomanシリーズ" => "Dendomanシリーズ.pdf",
                    "プラント-唐津砕石殿" => "Dendomanプラント-唐津砕石殿.pdf",
                    "プラント-熊礦石材殿" => "Dendomanプラント-熊礦石材殿.pdf",
                    "IoT高機能型プラント" => "IoT高機能型リサイクルプラント.pdf",
                    "N-Link" => "N-Link.pdf",
                    "MSD700" => "MSD700.pdf",
                    "NE750J" => "NE750J.pdf",
                    "NePower" => "NePower.pdf",
                    "小水力発電システム" => "小水力発電システム.pdf",
                    "VSI" => "SR_en_ver.2.06_20220523.pdf",
                );
                foreach ($pdfList as $pdfTitle => $pdfPath) {
            ?>
                <div class="pdf-background">
                    <div class="pdf-card" data-pdf="./files/pdf/<?php echo $pdfPath; ?>">
                        <img width="100%" src="./files/pdf_preview/<?php echo $pdfTitle; ?>.jpg" alt="pdf-icon"></img>
                    </div>
                    <div class="video-title"><?php echo $pdfTitle; ?></div>
                    <div class="overlay"></div>
                </div>
            <?php
                }
            ?>
        </div>
        <div class="bottom-center">
            <div class="text-page">PDFのタイトルをクリックすると、より見やすくなります。</div>
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
