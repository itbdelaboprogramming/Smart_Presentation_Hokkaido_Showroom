<?php

header('Content-Type: application/json');
require_once '../config.php';

// ADMIN CHECK (except for get_all and get_one)
$action = $_GET['action'] ?? '';
if (!in_array($action, ['get_all', 'get_one'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
}

// ROUTER
switch ($action) {
    case 'get_all':
        handle_get_products($pdo);
        break;
    case 'get_one':
        handle_get_one_product($pdo);
        break;
    case 'add':
        handle_add_product($pdo);
        break;
    case 'update':
        handle_update_product($pdo);
        break;
    case 'delete':
        handle_delete_product($pdo);
        break;
    case 'get_annotations':
        handle_get_annotations($pdo);
        break;
    case 'add_annotation':
        handle_add_annotation($pdo);
        break;
    case 'update_annotation':
        handle_update_annotation($pdo);
        break;
    case 'delete_annotation':
        handle_delete_annotation($pdo);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}

// UPLOAD HELPER FUNCTION
function upload_file($fileInputName, $targetDir, $allowedExts, $isOptional = false) {
    if (!isset($_FILES[$fileInputName]) || $_FILES[$fileInputName]['error'] !== UPLOAD_ERR_OK) {
        if ($isOptional) return ['path' => null, 'success' => true]; // Jika optional, ini bukan error
        return ['success' => false, 'message' => "File '$fileInputName' wajib diupload."];
    }

    $file = $_FILES[$fileInputName];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowedExts)) {
        return ['success' => false, 'message' => "Format file '$fileInputName' tidak valid. Diizinkan: " . implode(', ', $allowedExts)];
    }

    if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

    // Generate unique file name
    $newFileName = time() . '_' . uniqid() . '.' . $ext;
    $targetPath = $targetDir . $newFileName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // return path relative ke root web (use ../ to remove ../ from path)
        $dbPath = str_replace('../', '', $targetPath);
        if (strpos($dbPath, 'files/') !== 0 && strpos($dbPath, 'audio/') !== 0) {
             $dbPath = './' . $dbPath; 
        }
        
        return ['success' => true, 'path' => $dbPath];
    } else {
        return ['success' => false, 'message' => "Gagal mengupload file '$fileInputName'."];
    }
}

// READ
function handle_get_products($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products ORDER BY id ASC");
        $stmt->execute();
        $products = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $products]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function handle_get_one_product($pdo) {
    if (empty($_GET['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Produk tidak valid.']);
        return;
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $product = $stmt->fetch();
        if ($product) {
            echo json_encode(['status' => 'success', 'data' => $product]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// CREATE
function handle_add_product($pdo) {
    $requiredFields = ['product_key', 'category', 'display_title_en', 'info', 'camera_pos_x', 'camera_pos_y', 'camera_pos_z'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            echo json_encode(['status' => 'error', 'message' => "Field '$field' wajib diisi."]);
            return;
        }
    }

    $uploads = [
        'glb_file'          => upload_file('glb_file', '../files/glb/', ['glb'], false), 
        'audio_link'        => upload_file('audio_link', '../audio/', ['mp3', 'wav'], false), 
        'preview_img_path'  => upload_file('preview_img_path', '../files/img_preview/', ['jpg', 'jpeg', 'png'], false), 
        'pdf_link'          => upload_file('pdf_link', '../files/pdf/', ['pdf'], true), 
        'video_link'        => upload_file('video_link', '../files/video/', ['mp4', 'mov'], true), 
        'info_img'          => upload_file('info_img', '../files/img/', ['jpg', 'jpeg', 'png'], true) 
    ];

    foreach ($uploads as $key => $result) {
        if (!$result['success']) {
            foreach ($uploads as $r) { if ($r['success'] && $r['path']) @unlink('../' . $r['path']); }
            echo json_encode(['status' => 'error', 'message' => $result['message']]);
            return;
        }
    }

    try {
        $sql = "INSERT INTO products (
            product_key, category, display_title_jp, display_title_en, info, 
            camera_pos_x, camera_pos_y, camera_pos_z,
            glb_file, audio_link, preview_img_path, pdf_link, video_link, info_img
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['product_key'],
            $_POST['category'],
            $_POST['display_title_jp'] ?? null,
            $_POST['display_title_en'],
            $_POST['info'],
            $_POST['camera_pos_x'],
            $_POST['camera_pos_y'],
            $_POST['camera_pos_z'],
            // File paths
            $uploads['glb_file']['path'],
            $uploads['audio_link']['path'],
            $uploads['preview_img_path']['path'],
            $uploads['pdf_link']['path'],
            $uploads['video_link']['path'],
            $uploads['info_img']['path']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Product added successfully!']);

    } catch (PDOException $e) {
        foreach ($uploads as $r) { if ($r['success'] && $r['path']) @unlink('../' . $r['path']); }
        echo json_encode(['status' => 'error', 'message' => "Database Error: " . $e->getMessage()]);
    }
}

// UPDATE
function handle_update_product($pdo) {
    if (empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Produk tidak valid.']);
        return;
    }
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $oldData = $stmt->fetch();
        if (!$oldData) { echo json_encode(['status' => 'error', 'message' => 'Produk tidak ditemukan.']); return; }

        $updateFields = [
            'product_key' => $_POST['product_key'],
            'category' => $_POST['category'],
            'display_title_jp' => $_POST['display_title_jp'] ?? null,
            'display_title_en' => $_POST['display_title_en'],
            'info' => $_POST['info'],
            'camera_pos_x' => $_POST['camera_pos_x'],
            'camera_pos_y' => $_POST['camera_pos_y'],
            'camera_pos_z' => $_POST['camera_pos_z']
        ];

        $fileInputs = [
            'glb_file' => ['dir' => '../files/glb/', 'ext' => ['glb']],
            'audio_link' => ['dir' => '../audio/', 'ext' => ['mp3', 'wav']],
            'preview_img_path' => ['dir' => '../files/img_preview/', 'ext' => ['jpg', 'jpeg', 'png']],
            'pdf_link' => ['dir' => '../files/pdf/', 'ext' => ['pdf']],
            'video_link' => ['dir' => '../files/video/', 'ext' => ['mp4', 'mov']],
            'info_img' => ['dir' => '../files/img/', 'ext' => ['jpg', 'jpeg', 'png']]
        ];

        foreach ($fileInputs as $dbColumn => $config) {
            if (isset($_FILES[$dbColumn]) && $_FILES[$dbColumn]['error'] === UPLOAD_ERR_OK) {
                $uploadResult = upload_file($dbColumn, $config['dir'], $config['ext'], false);
                if (!$uploadResult['success']) {
                    echo json_encode(['status' => 'error', 'message' => $uploadResult['message']]);
                    return;
                }
                $oldFile = $oldData[$dbColumn];
                $oldFilePath = '../' . ltrim($oldFile, './'); 
                if ($oldFile && file_exists($oldFilePath)) {
                    @unlink($oldFilePath);
                }
                
                $updateFields[$dbColumn] = $uploadResult['path'];
            }
        }

        $sqlSet = [];
        $params = [];
        foreach ($updateFields as $col => $val) {
            $sqlSet[] = "$col = ?";
            $params[] = $val;
        }
        $params[] = $id; 

        $sql = "UPDATE products SET " . implode(', ', $sqlSet) . " WHERE id = ?";
        $stmtUpdate = $pdo->prepare($sql);
        $stmtUpdate->execute($params);

        echo json_encode(['status' => 'success', 'message' => 'Product updated successfully!']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// DELETE
function handle_delete_product($pdo) {
    if (empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Produk tidak valid.']);
        return;
    }
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("SELECT glb_file, audio_link, preview_img_path, pdf_link, video_link, info_img FROM products WHERE id = ?");
        $stmt->execute([$id]);
        $product = $stmt->fetch();

        if ($product) {
            foreach ($product as $filePath) {
                if ($filePath) {
                    $realPath = '../' . ltrim($filePath, './');
                    if (file_exists($realPath)) @unlink($realPath);
                }
            }
        }

        $stmtDel = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmtDel->execute([$id]);

        echo json_encode(['status' => 'success', 'message' => 'Product and related files deleted successfully.']);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// ANNOTATIONS HANDLERS

// READ
function handle_get_annotations($pdo) {
    if (empty($_GET['product_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Product ID is required']);
        return;
    }
    
    $productId = $_GET['product_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM annotations WHERE product_id = ?");
        $stmt->execute([$productId]);
        $annotations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode(['status' => 'success', 'data' => $annotations]);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// CREATE
function handle_add_annotation($pdo) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['product_id']) || empty($data['text']) || !isset($data['pos_x'])) {
        echo json_encode(['status' => 'error', 'message' => 'Data anotasi tidak lengkap.']);
        return;
    }

    try {
        $sql = "INSERT INTO annotations (product_id, text, pos_x, pos_y, pos_z) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['product_id'],
            $data['text'],
            $data['pos_x'],
            $data['pos_y'],
            $data['pos_z']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Annotation added', 'id' => $pdo->lastInsertId()]);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// UPDATE
function handle_update_annotation($pdo) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Annotation ID is required']);
        return;
    }

    try {
        $fields = [];
        $params = [];

        if (isset($data['text'])) {
            $fields[] = "text = ?";
            $params[] = $data['text'];
        }
        if (isset($data['pos_x'])) { 
            $fields[] = "pos_x = ?"; $params[] = $data['pos_x'];
            $fields[] = "pos_y = ?"; $params[] = $data['pos_y'];
            $fields[] = "pos_z = ?"; $params[] = $data['pos_z'];
        }

        if (empty($fields)) {
            echo json_encode(['status' => 'error', 'message' => 'Tidak ada data yang diupdate.']);
            return;
        }

        $params[] = $data['id']; 
        $sql = "UPDATE annotations SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        echo json_encode(['status' => 'success', 'message' => 'Annotation updated']);

    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// DELETE
function handle_delete_annotation($pdo) {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (empty($data['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Annotation ID is required']);
        return;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM annotations WHERE id = ?");
        $stmt->execute([$data['id']]);
        echo json_encode(['status' => 'success', 'message' => 'Annotation deleted']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>