<?php

$playlists = [];
$errorMessage = '';
try {
    $stmt = $pdo->prepare("SELECT * FROM video_playlists ORDER BY sort_order ASC, id ASC");
    $stmt->execute();
    $playlists = $stmt->fetchAll();
} catch (PDOException $e) {
    $errorMessage = "Error loading playlists: " . $e->getMessage();
}

$isAdmin = (isset($_SESSION['role']) && $_SESSION['role'] == 'admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Presentation - Playlist</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="./style/company-playlist.css">
    <link rel="stylesheet" href="./style/admin-ui.css">
    <link rel="icon" type="image/x-icon" href="assets/SR_logo_03_red.png">
    <link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />
    <script src="https://cdn.plyr.io/3.7.8/plyr.js"></script>
</head>
<body>
    <div class="company-page">
        <div class="container-top-left">
            <button class="menu-container-back-button" onclick="location.href='company-video'">
                <img src="./assets/Back-Button.svg"> Back
            </button>
            <div class="page-name-container">
                <div class="page-name-text">Video Playlist</div>
            </div>
        </div>

        <?php if ($isAdmin): ?>
        <div class="container-top-right-admin">
            <button class="admin-button-add" id="admin-add-playlist-btn">
                <i class="fas fa-plus"></i> Add New Playlist
            </button>
        </div>
        <?php endif; ?>

        <div class="video-player-overlay hidden" id="video-player-overlay">
            <div class="video-player-container">
                <section class="main-video">
                    <video id="main-video" class="plyr static-size" autoplay>
                         <source src="" id="main-video-source" type="video/mp4">
                    </video>
                    <h3 id="main-video-title"></h3>
                </section>
                <section class="video-playlist">
                    <h3 id="video-playlist-title">Playlist of</h3>
                    <div id="video-playlist"></div>
                </section>
            </div>
        </div>

        <div class="playlist-content-container">
            <?php
                if (!empty($errorMessage)):
            ?>
                <p style="color: red; text-align: center; width: 100%;"><?php echo htmlspecialchars($errorMessage); ?></p>
            <?php
                elseif (empty($playlists)):
            ?>
                <p style="color: white; text-align: center; width: 100%;">No playlists available.</p>
            <?php
                else:
                    foreach ($playlists as $playlist):
                        $playlistTitle = htmlspecialchars($playlist['title']);
                        $thumbnailImage = htmlspecialchars($playlist['thumbnail_image']);
                        $playlistId = $playlist['id'];
            ?>
                <div class="admin-card-container">
                    
                    <?php if ($isAdmin): ?>
                    <div class="admin-card-buttons">
                        <button class="admin-button-icon admin-button-manage" data-id="<?php echo $playlistId; ?>" title="Manage Videos">
                            <i class="fas fa-film"></i>
                        </button>
                        <button class="admin-button-icon admin-button-edit" data-id="<?php echo $playlistId; ?>" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="admin-button-icon admin-button-delete" data-id="<?php echo $playlistId; ?>" title="Delete">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                    <?php endif; ?>

                    <div class="playlist-card" data-playlist-id="<?php echo $playlistId; ?>">
                        <div class="card-content">
                            <img src="./files/img/playlists/<?php echo $thumbnailImage; ?>" alt="<?php echo $playlistTitle; ?>">
                            <div class="playlist-title"><?php echo $playlistTitle; ?></div>
                        </div>
                    </div>
                </div>
            <?php
                    endforeach;
                endif;
            ?>
        </div>

    </div>

    <?php if ($isAdmin): ?>
    <div class="admin-modal-overlay" id="admin-playlist-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 id="admin-playlist-modal-title">Add New Playlist</h2>
                <button class="admin-modal-close" id="admin-playlist-modal-close-btn">&times;</button>
            </div>
            <form class="admin-modal-body" id="admin-playlist-form" enctype="multipart/form-data">
                <input type="hidden" id="playlist-id" name="playlist_id" value="">
                <div class="admin-form-group">
                    <label for="playlist-title">Playlist Title</label>
                    <input type="text" id="playlist-title" name="title" placeholder="Example: Dendoman Series" required>
                </div>
                <div class="admin-form-group">
                    <label for="playlist-image">Thumbnail Image</label>
                    <input type="file" id="playlist-image" name="thumbnail_image" accept="image/jpeg, image/png, image/jpg">
                     <small style="color: #9ca3af;">(Leave empty to keep existing image)</small>
                </div>
                <div class="admin-form-group">
                    <label for="playlist-order">Sort Order</label>
                    <input type="number" id="playlist-order" name="sort_order" value="10" required>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="admin-button-cancel" id="admin-playlist-modal-cancel-btn">Cancel</button>
                    <button type="submit" class="admin-button-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-modal-overlay hidden" id="admin-video-manager-modal">
        <div class="admin-modal-content" style="max-width: 800px;">
            <div class="admin-modal-header">
                <h2 id="video-manager-title">Manage Videos</h2>
                <button class="admin-modal-close" id="admin-video-manager-close-btn">&times;</button>
            </div>
            <div class="admin-modal-body">
                <button class="admin-button-add" id="admin-add-video-btn" style="margin-bottom: 15px;">
                    <i class="fas fa-plus"></i> Add New Video
                </button>
                <div id="video-list-container" class="admin-table-container" style="overflow-y: auto; max-height: 400px;"></div>
            </div>
            <div class="admin-modal-footer">
                <button type="button" class="admin-button-cancel" id="admin-video-manager-cancel-btn">Close</button>
            </div>
        </div>
    </div>

    <div class="admin-modal-overlay hidden" id="admin-video-form-modal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h2 id="video-form-title">Add New Video</h2>
                <button class="admin-modal-close" id="admin-video-form-close-btn">&times;</button>
            </div>
            <form class="admin-modal-body" id="admin-video-form" enctype="multipart/form-data">
                <input type="hidden" id="video-manager-playlist-id" name="playlist_id" value="">
                <input type="hidden" id="video-form-id" name="video_id" value="">
                <div class="admin-form-group">
                    <label for="video-title">Video Title</label>
                    <input type="text" id="video-title" name="title" required>
                </div>
                <div class="admin-form-group">
                    <label for="video-duration">Duration (HH:MM:SS)</label>
                    <input type="text" id="video-duration" name="duration" placeholder="Auto-filled on file selection" required readonly>
                </div>
                <div class="admin-form-group">
                    <label for="video-file">Video File (.mp4/.mov)</label>
                    <input type="file" id="video-file" name="video_file" accept=".mp4,.mov">
                     <small style="color: #9ca3af;">(Leave empty to keep existing file)</small>
                </div>
                <div class="admin-form-group">
                    <label for="video-order">Sort Order</label>
                    <input type="number" id="video-order" name="sort_order" value="10" required>
                </div>
                <div class="admin-modal-footer">
                    <button type="button" class="admin-button-cancel" id="admin-video-form-cancel-btn">Cancel</button>
                    <button type="submit" class="admin-button-save">Save Video</button>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <script src="./js/admin-utils.js"></script>
    <script src="./js/company-playlist.js"></script>
</body>
</html>