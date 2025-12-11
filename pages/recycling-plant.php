<?php
$formattedData = [];
$productsList = [];
$errorMessage = '';

try {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE category = 'recycling' OR product_key = 'Recycling Full Plant' ORDER BY id ASC");
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $key = $p['product_key'];
        $productsList[] = $p;
        $stmtAnnot = $pdo->prepare("SELECT * FROM annotations WHERE product_id = ? LIMIT 1");
        $stmtAnnot->execute([$p['id']]);
        $annot = $stmtAnnot->fetch(PDO::FETCH_ASSOC);

        $formattedData[$key] = [
            "id" => $p['id'], 
            "info" => $p['info'],
            "glb_file" => $p['glb_file'],
            "audio_link" => $p['audio_link'],
            "pdf_link" => $p['pdf_link'],
            "video_link" => $p['video_link'],
            "info_img" => $p['info_img'],
            "position" => [
                "x" => (float)$p['camera_pos_x'],
                "y" => (float)$p['camera_pos_y'],
                "z" => (float)$p['camera_pos_z']
            ]
        ];

        if ($annot) {
            $formattedData[$key]["annotation_text"] = $annot['text'];
            $formattedData[$key]["annotation"] = [
                "x" => (float)$annot['pos_x'],
                "y" => (float)$annot['pos_y'],
                "z" => (float)$annot['pos_z']
            ];
        } else {
            $formattedData[$key]["annotation_text"] = $p['display_title_en'];
            $formattedData[$key]["annotation"] = ["x" => 0, "y" => 0, "z" => 0];
        }
    }

} catch (PDOException $e) {
    $errorMessage = "Gagal memuat data produk: " . $e->getMessage();
}

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>スマート・プレゼンテーション - リサイクルプラント</title>
        <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
        <meta charset="UTF-8">

        <script src="https://cdn.jsdelivr.net/npm/gsap@3.2.4/dist/gsap.js"></script>
        <link rel="stylesheet" href="./style/style.css" >
        <link rel="stylesheet" href="./style/admin-ui.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        
        <script async src="https://unpkg.com/es-module-shims@1.6.3/dist/es-module-shims.js"></script>

        <script type="importmap">
            {
                "imports": {
                "three": "https://unpkg.com/three@0.154.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.154.0/examples/jsm/"
                }
            }
        </script>

        <script>
            window.PRODUCT_DATA = <?php echo json_encode($formattedData, JSON_UNESCAPED_UNICODE); ?>;
            console.log("Data Loaded from DB (Recycling):", window.PRODUCT_DATA);
        </script>

        <script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
    </head>
    <body>
        <div class="home-page">
            <canvas id="myCanvas"></canvas>
            
            <div class="container-top-left">
                <button class="menu-container-back-button" onClick="location.href='home'">
                    <img src="./assets/Back-Button.svg"> Back
                </button>
                <div class="page-name-container">
                    <div class="page-name-text">リサイクルプラント</div>
                </div>
            </div>

            <div class="information-container" id="information-container" style="display:none;">
                <div class="information-description">
                    <img class="information-close" src="./assets/x_btn.svg" />
                    <p class="information-description-title">-</p>
                    <p class="information-description-description">-</p>
                    <img class="information-specification-img" />
                </div>
                <div class="information-footer">
                    <div  class="menu-pdf">
                        <img src="./assets/Pdf.svg"> カタログ​
                    </div>
                    <div class="menu-video">
                        <img src="./assets/Video.svg"> 動画​
                    </div>
                </div>
            </div>

            <div class="catalogue-container-2" id="catalogue-container-2" style="display:flex;">
                <img class="catalogue-close" src="./assets/x_btn.svg" />
                <div class="catalogue-description-2">
                    <?php if ($isAdmin): ?>
                    <div style="padding: 10px 0; text-align: center; border-bottom: 1px solid #74e7d4; margin-bottom: 15px;">
                        <button id="admin-add-product-btn" class="admin-button-add" style="font-size: 12px; padding: 8px; background-color: #189ab4; color: white; border:none; border-radius:5px; cursor:pointer; width: 100%;">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($errorMessage)): ?>
                        <p style="color:red; padding:10px;"><?= $errorMessage ?></p>
                    <?php endif; ?>

                    <?php 
                    foreach ($productsList as $index => $prod): 
                        $isActive = ($prod['product_key'] == 'Recycling Full Plant') ? 'active' : '';
                        $displayName = $prod['product_key']; 
                        if ($displayName == 'Recycling Full Plant') $displayName = 'Overview';
                    ?>
                    <div class="catalogue-product-list-2 <?= $isActive ?>" id="model_name_<?= $index ?>" style="position: relative; overflow: visible !important;">
                        <?php if ($isAdmin): ?>
                        <div class="admin-card-buttons" style="position: absolute !important; top: 10px !important; right: 10px !important; display: flex !important; gap: 5px; z-index: 999 !important;">
                            <button class="admin-button-icon admin-button-edit-product" 
                                    data-key="<?= htmlspecialchars($prod['product_key']) ?>"
                                    data-id="<?= $prod['id'] ?>"
                                    style="width: 30px !important; height: 30px !important; background-color: #f59e0b !important; color: white !important; border: none !important; border-radius: 5px; cursor: pointer; font-size: 14px;"
                                    title="Edit Product">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="admin-button-icon btn-manage-annotations" 
                                    data-id="<?= $prod['id'] ?>"
                                    data-name="<?= htmlspecialchars($displayName) ?>"
                                    style="width: 30px !important; height: 30px !important; background-color: #e67e22 !important; color: white !important; border: none !important; border-radius: 5px; cursor: pointer; font-size: 14px;"
                                    title="Manage Annotations">
                                <i class="fas fa-map-marker-alt"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($prod['display_title_jp'])): ?>
                            <div><?= htmlspecialchars($prod['display_title_jp']) ?></div>
                        <?php endif; ?>
                        
                        <div class="catalogue-product-list-text-2"> <?= htmlspecialchars($displayName) ?> </div>
                        <img class="catalogue-image-preview-2" src="<?= htmlspecialchars($prod['preview_img_path']) ?>" />
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="container-bottom-left-ml2x">
                <div class="sound-expand" style="display:none;">
                    <div class="sound-expand-component">
                        Volume Controller
                        <div class="slider-group">
                            <div class="slider-container">
                                <span class="bar"><span class="fill" id="fill-sound"></span></span>
                                <input type="range" min="0" max="1" value="0.5" step="0.05" class="slider" id="slider-sound"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container-bottom-right-mr2x" >
                <div class="menu-container-blue-lightning-expand" style="display: none">
                    <div class="menu-container-blue-lightning-expand-wrapper">
                        <div class="lightning-component">
                            <div class="lightning-title-2">Lighting</div>
                            <div class="lightning-component-container custom-lightning">
                                <div class="slider-group">
                                    Environment Brightness
                                    <div class="slider-container">
                                        <span class="bar"><span class="fill" id="fill-env"></span></span>
                                        <input type="range" min="0" max="2" value="0.5" step="0.1" class="slider" id="slider-env"/>
                                    </div>
                                </div>
                                <div class="slider-group">
                                    Direct Lamp Brightness
                                    <div class="slider-container">
                                        <span class="bar"><span class="fill" id="fill-lamp"></span></span>
                                        <input type="range" min="0" max="40" value="20" step="0.1" class="slider" id="slider-lamp"/>
                                    </div>
                                </div>
                                <div class="slider-group">
                                    Direct Lamp Position
                                    <div class="slider-container">
                                        <span class="bar"><span class="fill" id="fill-lamp-pos"></span></span>
                                        <input type="range" min="0" max="400" value="210" step="1" class="slider" id="slider-lamp-pos"/>
                                    </div>   
                                </div>
                            </div>
                        </div>
                        <div class="lightning-component-center"> 
                            <div class="lightning-title">Enlargement</div>
                            <div class="lightning-component-container">
                                <div class="slider-group">
                                    Zoom
                                    <div class="slider-container">
                                        <span class="bar"><span class="fill" id="fill-zoom"></span></span>
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
                    <img src="./assets/Information-Button.png"> Information
                </div>
                <div class="menu-container-blue-sound">
                    <img src="./assets/Sound-On-Button.png" id="sound-off"> Sound
                </div>
                <div class="menu-container-blue-animation">
                    <img src="./assets/Animation-Off-Button.png" id="animation-off">
                    <img src="./assets/Animation-On-Button.png" id="animation-on" style="display: none;"> Rotation
                </div>
            </div>

            <div class="container-bottom-right">
                <div class="menu-container-blue-album active">
                    Machine List <img src="./assets/Album-Button.png">
                </div>
                <div class="menu-container-blue-lightning">
                    Lighting <img src="./assets/Lightning-Button.png">
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
                    <div class="close-video">
                        <img src="./assets/video_close.svg" height="10px" width="10px"/>
                    </div>
                    <video id="video" width="100%" height="auto" type="video/mp4" controls preload="auto" disablePictureInPicture controlslist="nodownload noplaybackrate"></video>
                </div>
            </div>
            <div class="loadingScreenContainer" style="display: none">
                <label for="loadingBar" id='loadingBarLabel'> Loading... </label>
                <progress id='loadingBar' max='100' value='0'></progress>
            </div>

            <script type="module" src="script.js"></script>
            <script type="module" src="./js/diorama.js"></script>
            <script type="module" src="./js/audio.js"></script>
        </div>

    <?php if ($isAdmin): ?>
    <div class="admin-modal-overlay hidden" id="admin-product-modal">
        <div class="admin-modal-content" style="max-width: 800px; max-height: 90vh; overflow-y: auto;">
            
            <div class="admin-modal-header">
                <h2 id="product-modal-title">Edit Product</h2>
                <button class="admin-modal-close" id="admin-product-close-btn">&times;</button>
            </div>

            <form class="admin-modal-body" id="admin-product-form" enctype="multipart/form-data">
                <input type="hidden" id="prod-id" name="id">
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="admin-form-group">
                        <label>Product Key (System Name)</label>
                        <input type="text" id="prod-key" name="product_key" readonly style="background-color: #2d3748; cursor: not-allowed;">
                        <small style="color: #ccc;">Can not be changed (used by system).</small>
                    </div>
                    <div class="admin-form-group">
                        <label>Category</label>
                        <select id="prod-category" name="category" style="width: 100%; padding: 10px; border-radius: 5px; background: #1d2538; color: white; border: 1px solid #475467;">
                            <option value="crushing">Crushing Plant</option>
                            <option value="recycling">Recycling Plant</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="admin-form-group">
                        <label>Display Title (English)</label>
                        <input type="text" id="prod-title-en" name="display_title_en" required>
                    </div>
                    <div class="admin-form-group">
                        <label>Display Title (Japanese)</label>
                        <input type="text" id="prod-title-jp" name="display_title_jp">
                    </div>
                </div>

                <div class="admin-form-group">
                    <label>Description / Info</label>
                    <textarea id="prod-info" name="info" rows="4" style="width: 100%; padding: 10px; border-radius: 5px; background: #1d2538; color: white; border: 1px solid #475467;"></textarea>
                </div>

                <div class="admin-form-group">
                    <label>Camera Position (X, Y, Z)</label>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 10px;">
                        <input type="number" step="0.001" id="prod-cam-x" name="camera_pos_x" placeholder="X" required>
                        <input type="number" step="0.001" id="prod-cam-y" name="camera_pos_y" placeholder="Y" required>
                        <input type="number" step="0.001" id="prod-cam-z" name="camera_pos_z" placeholder="Z" required>
                    </div>
                </div>

                <hr style="border: 0.5px solid #475467; margin: 20px 0;">
                <h3 style="color: #74e7d4; margin-bottom: 15px;">File Management</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    
                    <div class="admin-form-group">
                        <label>3D Model (.glb)</label>
                        <input type="file" id="prod-file-glb" name="glb_file" accept=".glb">
                        <small class="file-status" id="status-glb" style="color: #74e7d4;"></small>
                    </div>

                    <div class="admin-form-group">
                        <label>Sidebar Preview Image (.png/.jpg)</label>
                        <input type="file" id="prod-file-preview" name="preview_img_path" accept="image/*">
                        <small class="file-status" id="status-preview" style="color: #74e7d4;"></small>
                    </div>

                    <div class="admin-form-group">
                        <label>Audio Explanation (.mp3)</label>
                        <input type="file" id="prod-file-audio" name="audio_link" accept=".mp3,.wav">
                        <small class="file-status" id="status-audio" style="color: #74e7d4;"></small>
                    </div>

                    <div class="admin-form-group">
                        <label>Spec Image (.jpg/.png) - <i>Optional</i></label>
                        <input type="file" id="prod-file-info-img" name="info_img" accept="image/*">
                        <small class="file-status" id="status-info-img" style="color: #74e7d4;"></small>
                    </div>

                    <div class="admin-form-group">
                        <label>PDF Catalog - <i>Optional</i></label>
                        <input type="file" id="prod-file-pdf" name="pdf_link" accept=".pdf">
                        <small class="file-status" id="status-pdf" style="color: #74e7d4;"></small>
                    </div>

                    <div class="admin-form-group">
                        <label>Video (.mp4) - <i>Optional</i></label>
                        <input type="file" id="prod-file-video" name="video_link" accept=".mp4">
                        <small class="file-status" id="status-video" style="color: #74e7d4;"></small>
                    </div>
                </div>

                <div class="admin-modal-footer">
                    <button type="button" class="admin-button-cancel" id="admin-product-cancel-btn">Cancel</button>
                    <button type="submit" class="admin-button-save">Update Product</button>
                </div>

            </form>
        </div>
    </div>
    
    <script src="./js/admin-utils.js"></script>
    <script src="./js/product-admin.js"></script>
    <?php endif; ?>
    </body>
</html>