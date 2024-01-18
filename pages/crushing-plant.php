<!DOCTYPE html>
<html lang="en">
    <head>
        <title>スマート・プレゼンテーション</title>
        <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
        <meta charset="UTF-8">

        <script src="https://cdn.jsdelivr.net/npm/gsap@3.2.4/dist/gsap.js"></script>
        <link rel="stylesheet" href="./style/style.css" >
        <script async src="https://unpkg.com/es-module-shims@1.6.3/dist/es-module-shims.js"></script>

        <script type="importmap">
            {
                "imports": {
                "three": "https://unpkg.com/three@0.154.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.154.0/examples/jsm/"
                }
            }
        </script>
    </head>
    <body>
        <div class="home-page">
            <canvas id="myCanvas">    </canvas>
            <!-- line annotation -->
            <!-- <canvas id="lineCanvas" style="position: absolute; top: 0; left: 0;"></canvas> -->
            <div class="container-top-left">
                <div class="pdf_container">
                    <button class="menu-container-back-button" onClick="location.href='home'">
                        <img src="./assets/Back-Button.svg">
                    </button>
                </div>
                <div class="page-name-container">
                    <div class="page-name-text">破砕プラント</div>
                </div>
            </div>

            <div class="information-container" id="information-container" style="display:none;">
                <div class="information-description">
                    <p class="information-description-title">
                        Hokkaido Crushing Full Plant
                    </p>
                    <p class="information-description-description">-</p>
                    <!-- <p class="information-description-specification-detail" >Processing performance depends on quality of material, feeding chunks and particle size. <br><br> This machine’s spec and dimension might be changed without prior-notice for the improvement.</p> -->
                    <img class="information-specification-img" />
                    <!-- <img class="information-specification-img" src="./files/dimension_vsi_platform.png" /> -->
                    <!-- <img class="information-specification-img" src="./files/specification_2.png" /> -->
                    <!-- <img class="information-specification-img" src="./files/specification.png" /> -->
                    <!-- <img class="information-specification-img" src="./files/specification_3.png" /> -->
                </div>
                <div class="information-footer">
                    <div  class="menu-pdf">
                        <img src="./assets/Pdf.svg">
                        カタログ​
                    </div>
                    <div class="menu-video">
                        <img src="./assets/Video.svg">
                        動画​
                    </div>
                </div>
                <!-- <a class="information-link" target="_blank" href="https://www.ncjpn.com/en/products/crushers/">Hokkaido Crushing Full Plant | Nakayama Iron Works (ncjpn.com)</a> -->
            </div>

            <div class="catalogue-container-2" id="catalogue-container-2" style="display:flex;">
                <!-- <div>
                    <p class="catalogue-description-title-2">北海道ショールーム</p>
                </div> -->
                <div class="catalogue-description-2">
                    <div class="catalogue-product-list-2 active" id="model_name_1">
                        <div class="catalogue-product-list-text-2"> Hokkaido Crushing Full Plant </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/Hokkaido Crushing Full Plant_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>砕石プラント</div>
                        <div class="catalogue-product-list-text-2"> Crushing Plant </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/Hokkaido Crushing Plant_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>電動自走式クラッシャ</div>
                        <div class="catalogue-product-list-text-2"> NE750J </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/NE750J_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>自走式ベルトコンベア</div>
                        <div class="catalogue-product-list-text-2"> NEBC10520 </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/NEBC10520 Belt Conveyor_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>給電コンテナハウス</div>
                        <div class="catalogue-product-list-text-2"> NePower </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/N-ePower_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>コンテナ型 <br> 小水力発電</div>
                        <div class="catalogue-product-list-text-2"> Micro Hydro System </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/Micro Hydro System_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>バケットモデル</div>
                        <div class="catalogue-product-list-text-2"> MSD700-Bucket </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/MSD700 Bucket_preview.png" />
                    </div>
                    <div class="catalogue-product-list-2" id="model_name_1">
                        <div>ブレードモデル</div>
                        <div class="catalogue-product-list-text-2"> MSD700-Blade </div>
                        <img class="catalogue-image-preview-2" src="./files/img_preview/MSD700 Blade_preview.png" />
                    </div>
                </div>
            </div>

            <div class="container-bottom-left-ml2x">
                <div class="sound-expand" style="display:none;">
                    <div class="sound-expand-component">
                        Voice Over
                        <div class="toggle-container">
                            Off
                            <div class="toggle-speech"></div>
                            On
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-bottom-right-mr2x" >
                <div class="menu-container-blue-lightning-expand" style="display: none">
                    <div class="menu-container-blue-lightning-expand-wrapper">
                        <div class="lightning-component">
                            <div class="lightning-title-2">
                                Lighting
                            </div>
                            <div class="lightning-component-container custom-lightning">
                                <div class="slider-group">
                                    Environment Brightness
                                    <div class="slider-container">
                                        <span class="bar">
                                            <span class="fill" id="fill-env"></span>
                                        </span>
                                        <input type="range" min="0" max="2" value="0.5" step="0.1" class="slider" id="slider-env"/>
                                    </div>
                                </div>
                                <div class="slider-group">
                                    Direct Lamp Brightness
                                    <div class="slider-container">
                                        <span class="bar">
                                            <span class="fill" id="fill-lamp"></span>
                                        </span>
                                        <input type="range" min="0" max="40" value="20" step="0.1" class="slider" id="slider-lamp"/>
                                    </div>
                                </div>
                                <div class="slider-group">
                                    Direct Lamp Position
                                    <div class="slider-container">
                                        <span class="bar">
                                            <span class="fill" id="fill-lamp-pos"></span>
                                        </span>
                                        <input type="range" min="0" max="400" value="210" step="1" class="slider" id="slider-lamp-pos"/>
                                    </div>   
                                </div>
                            </div>
                        </div>
                        <div class="lightning-component-center"> 
                            <div class="lightning-title">
                                Enlargement
                            </div>
                            <div class="lightning-component-container">
                                <div class="slider-group">
                                    Zoom
                                    <div class="slider-container">
                                        <span class="bar">
                                            <span class="fill" id="fill-zoom"></span>
                                        </span>
                                        <input type="range" min="0.2" max="20" value="1" step="0.1" class="slider" id="slider-zoom"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-bottom-left">

                <div class="menu-container-blue-information">
                    <img src="./assets/Information-Button.png">
                    Information
                </div>
                <!-- <div class="menu-container-blue-sound">
                    <img src="./assets/Sound-Off-Button.png" id="sound-off">
                    <img src="./assets/Sound-On-Button.png" id="sound-on" style="display: none;">
                </div> -->
                <div class="menu-container-blue-animation">
                    <img src="./assets/Animation-Off-Button.png" id="animation-off">
                    <img src="./assets/Animation-On-Button.png" id="animation-on" style="display: none;">
                    Animation
                </div>
            </div>

            <div class="container-bottom-right">
                <div class="menu-container-blue-album active">
                    Catalog
                    <img src="./assets/Album-Button.png">
                </div>
                <div class="menu-container-blue-lightning">
                    Lighting
                    <img src="./assets/Lightning-Button.png">
                </div>
                <div class="toggle"></div>
            </div>

            <div class="container-full-screen-pdf">
                <div class="pdf-pop-up-container" id="pdf-pop-up-container">
                    <embed id="pdf-file" type="application/pdf" width="100%" height="100%"/>
                </div>
            </div>
            <div class="container-full-screen-video">
                <div class="pdf-pop-up-container-video" id="pdf-pop-up-container-video">
                    <video id="video" width="100%" height="auto" type="video/mp4" controls></video>
                </div>
            </div>
            <div class="loadingScreenContainer" style="display: none">
                <label for="loadingBar" id='loadingBarLabel'> Loading... </label>
                <progress id='loadingBar' max='100' value='0'></progress>
            </div>

            <script type="module" src="script.js"> </script>
            <script type="module" src="./js/diorama.js"></script>
        </div>
    </body>
</html>
