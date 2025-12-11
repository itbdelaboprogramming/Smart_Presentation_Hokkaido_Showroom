<?php

header('Content-Type: application/json');
require_once '../config.php';

$action = $_GET['action'] ?? '';
if (!in_array($action, ['get_all', 'get_one', 'get_videos'])) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }
}

// Router 
switch ($action) {
    case 'get_all':
        handle_get_all_playlists($pdo);
        break;
    case 'get_one':
        handle_get_one_playlist($pdo);
        break;
    case 'add':
        handle_add_playlist($pdo);
        break;
    case 'update':
        handle_update_playlist($pdo);
        break;
    case 'delete':
        handle_delete_playlist($pdo);
        break;
    case 'get_videos':
        handle_get_videos_by_playlist($pdo);
        break;
    case 'add_video':
        handle_add_video($pdo);
        break;
    case 'update_video':
        handle_update_video($pdo);
        break;
    case 'delete_video':
        handle_delete_video($pdo);
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Aksi tidak valid']);
        break;
}

// PLAYLISTS

// READ
function handle_get_all_playlists($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM video_playlists ORDER BY sort_order ASC, id ASC");
        $stmt->execute();
        $playlists = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $playlists]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function handle_get_one_playlist($pdo) {
    if (empty($_GET['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Playlist tidak valid.']);
        return;
    }
    $id = $_GET['id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM video_playlists WHERE id = ?");
        $stmt->execute([$id]);
        $playlist = $stmt->fetch();

        if ($playlist) {
            echo json_encode(['status' => 'success', 'data' => $playlist]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Playlist tidak ditemukan.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// CREATE
function handle_add_playlist($pdo) {
    if (empty($_POST['title']) || empty($_POST['sort_order'])) {
        echo json_encode(['status' => 'error', 'message' => 'Judul dan Urutan harus diisi.']);
        return;
    }

    if (!isset($_FILES['thumbnail_image']) || $_FILES['thumbnail_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'Gambar Thumbnail wajib diupload.']);
        return;
    }

    $uploadDirImg = '../files/img/playlists/';
    if (!is_dir($uploadDirImg)) mkdir($uploadDirImg, 0755, true); 

    try {
        // IMAGE UPLOAD PROCESS
        $imgFile = $_FILES['thumbnail_image'];
        $imgName = time() . '_' . basename($imgFile['name']);
        $imgTarget = $uploadDirImg . $imgName;
        $imgFileType = strtolower(pathinfo($imgTarget, PATHINFO_EXTENSION));
        $allowedImgTypes = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($imgFileType, $allowedImgTypes)) {
            throw new Exception("File Thumbnail harus berupa Gambar (JPG, PNG).");
        }
        if (!move_uploaded_file($imgFile['tmp_name'], $imgTarget)) {
            throw new Exception("Gagal mengupload file Gambar.");
        }

        $sql = "INSERT INTO video_playlists (title, thumbnail_image, sort_order) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['title'],
            $imgName,
            $_POST['sort_order']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Playlist added successfully!']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// UPDATE
function handle_update_playlist($pdo) {
    if (empty($_POST['playlist_id']) || empty($_POST['title']) || empty($_POST['sort_order'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID, Judul, dan Urutan harus diisi.']);
        return;
    }

    $id = $_POST['playlist_id'];
    $newTitle = $_POST['title'];
    $newSortOrder = $_POST['sort_order'];

    $uploadDirImg = '../files/img/playlists/';
    $imgName = null;

    try {
        $stmt = $pdo->prepare("SELECT thumbnail_image FROM video_playlists WHERE id = ?");
        $stmt->execute([$id]);
        $oldPlaylist = $stmt->fetch();
        if (!$oldPlaylist) {
            throw new Exception("Playlist yang akan diupdate tidak ditemukan.");
        }

        if (isset($_FILES['thumbnail_image']) && $_FILES['thumbnail_image']['error'] === UPLOAD_ERR_OK) {
            $imgFile = $_FILES['thumbnail_image'];
            $imgFileType = strtolower(pathinfo(basename($imgFile['name']), PATHINFO_EXTENSION));
            $allowedImgTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($imgFileType, $allowedImgTypes)) {
                throw new Exception("File Thumbnail harus berupa Gambar (JPG, PNG).");
            }
            
            if (file_exists($uploadDirImg . $oldPlaylist['thumbnail_image'])) {
                unlink($uploadDirImg . $oldPlaylist['thumbnail_image']);
            }
            
            $imgName = time() . '_' . basename($imgFile['name']);
            if (!move_uploaded_file($imgFile['tmp_name'], $uploadDirImg . $imgName)) {
                throw new Exception("Gagal mengupload file Gambar baru.");
            }
        } else {
            $imgName = $oldPlaylist['thumbnail_image'];
        }

        $sql = "UPDATE video_playlists SET title = ?, thumbnail_image = ?, sort_order = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $newTitle,
            $imgName,
            $newSortOrder,
            $id
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Playlist updated successfully!']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// DELETE
function handle_delete_playlist($pdo) {
    if (empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Playlist tidak ditemukan.']);
        return;
    }

    $id = $_POST['id'];
    $uploadDirImg = '../files/img/playlists/';

    try {
        $stmt = $pdo->prepare("SELECT thumbnail_image FROM video_playlists WHERE id = ?");
        $stmt->execute([$id]);
        $playlist = $stmt->fetch();

        if (!$playlist) {
            throw new Exception("Playlist tidak ditemukan.");
        }

        $imgPath = $uploadDirImg . $playlist['thumbnail_image'];
        if (file_exists($imgPath)) unlink($imgPath); 
        
        // Cascade delete logic
        $stmtDelete = $pdo->prepare("DELETE FROM video_playlists WHERE id = ?");
        $stmtDelete->execute([$id]);

        echo json_encode(['status' => 'success', 'message' => 'Playlist deleted successfully. All videos within it have also been deleted.']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// VIDEOS ON PLAYLIST

// READ
function handle_get_videos_by_playlist($pdo) {
    if (empty($_GET['playlist_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Playlist tidak ditemukan.']);
        return;
    }
    $playlistId = $_GET['playlist_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM videos WHERE playlist_id = ? ORDER BY sort_order ASC, id ASC");
        $stmt->execute([$playlistId]);
        $videos = $stmt->fetchAll();
        echo json_encode(['status' => 'success', 'data' => $videos]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function handle_add_video($pdo) {
    if (empty($_POST['playlist_id']) || empty($_POST['title']) || empty($_POST['sort_order'])) {
        echo json_encode(['status' => 'error', 'message' => 'Semua field wajib diisi.']);
        return;
    }
    if (!isset($_FILES['video_file']) || $_FILES['video_file']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(['status' => 'error', 'message' => 'File Video wajib diupload.']);
        return;
    }

    $uploadDir = '../files/video/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true); 

    try {
        $videoFile = $_FILES['video_file'];
        $videoName = time() . '_' . basename($videoFile['name']);
        $videoTarget = $uploadDir . $videoName;
        $videoFileType = strtolower(pathinfo($videoTarget, PATHINFO_EXTENSION));

        if ($videoFileType != "mp4" && $videoFileType != "mov") {
            throw new Exception("File Video harus berformat MP4 atau MOV.");
        }
        if (!move_uploaded_file($videoFile['tmp_name'], $videoTarget)) {
            throw new Exception("Gagal mengupload file Video.");
        }

        $sql = "INSERT INTO videos (playlist_id, file_name, title, duration, sort_order) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['playlist_id'],
            $videoName,
            $_POST['title'],
            $_POST['duration'],
            $_POST['sort_order']
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Video added successfully!']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// UPDATE
function handle_update_video($pdo) {
    if (empty($_POST['video_id']) || empty($_POST['title']) || empty($_POST['duration']) || empty($_POST['sort_order'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID, Judul, Durasi, dan Urutan harus diisi.']);
        return;
    }

    $id = $_POST['video_id'];
    $uploadDir = '../files/video/';
    $videoName = null;

    try {
        $stmt = $pdo->prepare("SELECT file_name FROM videos WHERE id = ?");
        $stmt->execute([$id]);
        $oldVideo = $stmt->fetch();
        if (!$oldVideo) throw new Exception("Video tidak ditemukan.");

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
            $videoFile = $_FILES['video_file'];
            $videoFileType = strtolower(pathinfo(basename($videoFile['name']), PATHINFO_EXTENSION));

            if ($videoFileType != "mp4" && $videoFileType != "mov") {
                throw new Exception("File Video harus berformat MP4 atau MOV.");
            }
            
            if (file_exists($uploadDir . $oldVideo['file_name'])) {
                unlink($uploadDir . $oldVideo['file_name']);
            }
            
            $videoName = time() . '_' . basename($videoFile['name']);
            if (!move_uploaded_file($videoFile['tmp_name'], $uploadDir . $videoName)) {
                throw new Exception("Gagal mengupload file Video baru.");
            }
        } else {
            $videoName = $oldVideo['file_name'];
        }

        $sql = "UPDATE videos SET file_name = ?, title = ?, duration = ?, sort_order = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $videoName,
            $_POST['title'],
            $_POST['duration'],
            $_POST['sort_order'],
            $id
        ]);

        echo json_encode(['status' => 'success', 'message' => 'Video updated successfully!']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

// DELETE
function handle_delete_video($pdo) {
    if (empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'message' => 'ID Video tidak ditemukan.']);
        return;
    }

    $id = $_POST['id'];
    $uploadDir = '../files/video/';

    try {
        $stmt = $pdo->prepare("SELECT file_name FROM videos WHERE id = ?");
        $stmt->execute([$id]);
        $video = $stmt->fetch();

        if (!$video) throw new Exception("Video tidak ditemukan.");

        $filePath = $uploadDir . $video['file_name'];
        if (file_exists($filePath)) unlink($filePath); 
        
        $stmtDelete = $pdo->prepare("DELETE FROM videos WHERE id = ?");
        $stmtDelete->execute([$id]);

        echo json_encode(['status' => 'success', 'message' => 'Video deleted successfully.']);

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>