<?php

$catalogs = [];
$errorMessage = '';
try {
    $stmt = $pdo->prepare("SELECT * FROM catalogs ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    $catalogs = $stmt->fetchAll();
} catch (PDOException $e) {
    $errorMessage = "Error loading catalogs: " . $e->getMessage();
}

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>スマート・プレゼンテーション</title>
    <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
    <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./style/catalog-page.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./style/admin-ui.css">
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

        <?php if ($isAdmin): ?>
        <div class="container-top-right-admin">
            <button class="admin-button-add" id="admin-add-catalog-btn">
                <i class="fas fa-plus"></i> Add New Catalog
            </button>
        </div>
        <?php endif; ?>

        <div class="pdf-content-container">
            <?php
                if (!empty($errorMessage)):
            ?>
                <p style="color: red;"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php
                elseif (empty($catalogs)):
            ?>
                <p style="color: white; text-align: center; grid-column: 1 / -1;">No catalogs available.</p>
            <?php
                else:
                    foreach ($catalogs as $catalog):
                        $pdfTitle = htmlspecialchars($catalog['title']);
                        $pdfPath = htmlspecialchars($catalog['pdf_file']);
                        $previewImage = htmlspecialchars($catalog['preview_image']);
                        $catalogId = $catalog['id'];
            ?>
                <div class="admin-card-container">
                    
                    <?php if ($isAdmin): ?>
                    <div class="admin-card-buttons">
                        <button class="admin-button-icon admin-button-edit" data-id="<?php echo $catalogId; ?>" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="admin-button-icon admin-button-delete" data-id="<?php echo $catalogId; ?>" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="pdf-background">
                        <div class="pdf-card" data-pdf="./files/pdf/<?php echo $pdfPath; ?>">
                            <img width="100%" src="./files/pdf_preview/<?php echo $previewImage; ?>" alt="pdf-icon"></img>
                        </div>
                        <div class="video-title"><?php echo $pdfTitle; ?></div>
                        <div class="overlay"></div>
                    </div>
                </div>
            <?php
                    endforeach;
                endif;
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

    <?php if ($isAdmin): ?>
    <div class="admin-modal-overlay" id="admin-catalog-modal">
        <div class="admin-modal-content">
            
            <div class="admin-modal-header">
                <h2 id="admin-modal-title">Add New Catalog</h2>
                <button class="admin-modal-close" id="admin-modal-close-btn">&times;</button>
            </div>

            <form class="admin-modal-body" id="admin-catalog-form" enctype="multipart/form-data">
                <input type="hidden" id="catalog-id" name="catalog_id" value="">

                <div class="admin-form-group">
                    <label for="catalog-title">Catalog Title</label>
                    <input type="text" id="catalog-title" name="title" placeholder="Example: MSD700" required>
                </div>

                <div class="admin-form-group">
                    <label for="catalog-pdf">PDF File</label>
                    <input type="file" id="catalog-pdf" name="pdf_file" accept=".pdf">
                    <small style="color: #9ca3af;">(Leave empty to keep existing file)</small>
                </div>

                <div class="admin-form-group">
                    <label for="catalog-image">Preview Image</label>
                    <input type="file" id="catalog-image" name="preview_image" accept="image/jpeg, image/png, image/jpg">
                     <small style="color: #9ca3af;">(Leave empty to keep existing image)</small>
                </div>

                <div class="admin-form-group">
                    <label for="catalog-order">Sort Order</label>
                    <input type="number" id="catalog-order" name="sort_order" value="10" required>
                </div>

                <div class="admin-modal-footer">
                    <button type="button" class="admin-button-cancel" id="admin-modal-cancel-btn">Cancel</button>
                    <button type="submit" class="admin-button-save">Save</button>
                </div>

            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="./js/admin-utils.js"></script>
    <script type="module" src="./js/catalog-page.js"></script>
</body>
</html>