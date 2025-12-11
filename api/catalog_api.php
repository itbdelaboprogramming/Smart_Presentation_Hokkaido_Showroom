<?php

header('Content-Type: application/json');
require_once '../config.php';

$action = $_GET['action'] ?? '';
if (!in_array($action, ['get_all', 'get_one'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
}

// Router API 
switch ($action) {
    case 'get_all':
        handle_get_catalogs($pdo);
        break;
    case 'get_one':
        handle_get_one_catalog($pdo);
        break;
    case 'add':
        handle_add_catalog($pdo);
        break;
    case 'update':
        handle_update_catalog($pdo);
        break;
    case 'delete':
        handle_delete_catalog($pdo);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}

// READ
function handle_get_catalogs($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM catalogs ORDER BY sort_order ASC, id ASC");
        $stmt->execute();
        $catalogs = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $catalogs]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function handle_get_one_catalog($pdo) {
    if (empty($_GET['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Katalog tidak valid.']);
        return;
    }
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM catalogs WHERE id = ?");
        $stmt->execute([$id]);
        $catalog = $stmt->fetch();

        if ($catalog) {
            echo json_encode(['status' => 'success', 'data' => $catalog]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Katalog tidak ditemukan.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// CREATE
function handle_add_catalog($pdo) {
    try {
        // Validate required fields
        Validator::validateRequiredFields(['title', 'sort_order']);
        
        $title = Validator::sanitizeString($_POST['title']);
        $sortOrder = Validator::validateInteger($_POST['sort_order'], 'Sort Order');

        // Validate file uploads
        if (!isset($_FILES['pdf_file']) || !isset($_FILES['preview_image'])) {
            throw new Exception('File PDF dan Gambar wajib diupload.');
        }

        // Upload PDF with validation
        $pdfUploader = new FileUploadHandler(
            '../files/pdf/',
            ['pdf'],
            FileUploadHandler::getMimeTypes('pdf'),
            10485760 // 10MB
        );
        $pdfResult = $pdfUploader->upload('pdf_file', false);
        if (!$pdfResult['success']) {
            throw new Exception($pdfResult['message']);
        }

        // Upload Preview Image with validation
        $imgUploader = new FileUploadHandler(
            '../files/pdf_preview/',
            ['jpg', 'jpeg', 'png'],
            FileUploadHandler::getMimeTypes('image'),
            5242880 // 5MB
        );
        $imgResult = $imgUploader->upload('preview_image', false);
        if (!$imgResult['success']) {
            FileUploadHandler::deleteFile('../' . $pdfResult['path']);
            throw new Exception($imgResult['message']);
        }

        $pdfName = basename($pdfResult['path']);
        $imgName = basename($imgResult['path']);

        $sql = "INSERT INTO catalogs (title, pdf_file, preview_image, sort_order) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$title, $pdfName, $imgName, $sortOrder]);

        Logger::logAdminAction('CATALOG_CREATED', [
            'catalog_id' => $pdo->lastInsertId(),
            'title' => $title
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Catalog added successfully!']);

    } catch (Exception $e) {
        Logger::error("Catalog creation failed", ['error' => $e->getMessage()]);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


// UPDATE
function handle_update_catalog($pdo) {
    try {
        // Validate required fields
        Validator::validateRequiredFields(['catalog_id', 'title', 'sort_order']);
        
        $id = Validator::validateInteger($_POST['catalog_id'], 'Catalog ID');
        $newTitle = Validator::sanitizeString($_POST['title']);
        $newSortOrder = Validator::validateInteger($_POST['sort_order'], 'Sort Order');

        // Get old catalog data
        $stmt = $pdo->prepare("SELECT pdf_file, preview_image FROM catalogs WHERE id = ?");
        $stmt->execute([$id]);
        $oldCatalog = $stmt->fetch();
        if (!$oldCatalog) {
            throw new Exception("Katalog yang akan diupdate tidak ditemukan.");
        }

        $pdfName = $oldCatalog['pdf_file'];
        $imgName = $oldCatalog['preview_image'];

        // Update PDF if new file uploaded
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $pdfUploader = new FileUploadHandler(
                '../files/pdf/',
                ['pdf'],
                FileUploadHandler::getMimeTypes('pdf'),
                10485760
            );
            $pdfResult = $pdfUploader->upload('pdf_file', false);
            if (!$pdfResult['success']) {
                throw new Exception($pdfResult['message']);
            }
            // Delete old file
            FileUploadHandler::deleteFile('../files/pdf/' . $oldCatalog['pdf_file']);
            $pdfName = basename($pdfResult['path']);
        }

        // Update preview image if new file uploaded
        if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
            $imgUploader = new FileUploadHandler(
                '../files/pdf_preview/',
                ['jpg', 'jpeg', 'png'],
                FileUploadHandler::getMimeTypes('image'),
                5242880
            );
            $imgResult = $imgUploader->upload('preview_image', false);
            if (!$imgResult['success']) {
                throw new Exception($imgResult['message']);
            }
            // Delete old file
            FileUploadHandler::deleteFile('../files/pdf_preview/' . $oldCatalog['preview_image']);
            $imgName = basename($imgResult['path']);
        }

        // UPDATE DATABASE
        $sql = "UPDATE catalogs SET title = ?, pdf_file = ?, preview_image = ?, sort_order = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$newTitle, $pdfName, $imgName, $newSortOrder, $id]);

        // Log admin action
        Logger::logAdminAction('CATALOG_UPDATED', [
            'catalog_id' => $id,
            'title' => $newTitle
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Catalog updated successfully!']);

    } catch (Exception $e) {
        Logger::error("Catalog update failed", ['error' => $e->getMessage()]);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}


// DELETE
function handle_delete_catalog($pdo) {
    try {
        if (empty($_POST['id'])) {
            throw new Exception('ID Katalog tidak ditemukan.');
        }

        $id = Validator::validateInteger($_POST['id'], 'Catalog ID');

        $stmt = $pdo->prepare("SELECT pdf_file, preview_image FROM catalogs WHERE id = ?");
        $stmt->execute([$id]);
        $catalog = $stmt->fetch();

        if (!$catalog) {
            throw new Exception("Katalog tidak ditemukan.");
        }

        // Delete files
        FileUploadHandler::deleteFile('../files/pdf/' . $catalog['pdf_file']);
        FileUploadHandler::deleteFile('../files/pdf_preview/' . $catalog['preview_image']);

        // Delete from database
        $stmtDelete = $pdo->prepare("DELETE FROM catalogs WHERE id = ?");
        $stmtDelete->execute([$id]);

        // Log admin action
        Logger::logAdminAction('CATALOG_DELETED', [
            'catalog_id' => $id,
            'title' => $catalog['pdf_file']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Catalog deleted successfully.']);

    } catch (Exception $e) {
        Logger::error("Catalog deletion failed", ['error' => $e->getMessage()]);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>