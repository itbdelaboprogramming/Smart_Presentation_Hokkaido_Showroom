<?php
set_time_limit(300);
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting data migration...<br>";

// Database connection
$db_host = 'localhost';
$db_user = 'root'; 
$db_pass = ''; 
$db_name = 'hokkaido_showroom_db';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
echo "Connected to database successfully.<br>";

// 1. Users Migration
try {
    $pdo->exec("DELETE FROM users WHERE username = 'admin'");

    $admin_user = 'admin';
    $admin_pass = password_hash('admin123', PASSWORD_DEFAULT); // default password
    $role = 'admin';

    $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
    $stmt->execute([$admin_user, $admin_pass, $role]);
    echo "[OK] 1. Admin user created successfully.<br>";
} catch (PDOException $e) {
    echo "[Failed] 1. Error migrating users: " . $e->getMessage() . "<br>";
}

// 2. Products & annotations migration (from data.json)
echo "Start migration for products & annotations<br>";

$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE annotations;");
$pdo->exec("TRUNCATE TABLE products;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

$jsonData = file_get_contents('data/data.json');
$products = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error decoding JSON: " . json_last_error_msg());
}

$stmt_product = $pdo->prepare("
    INSERT INTO products 
    (product_key, category, display_title_jp, display_title_en, preview_img_path, info, glb_file, audio_link, pdf_link, video_link, info_img, camera_pos_x, camera_pos_y, camera_pos_z) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt_annot = $pdo->prepare("
    INSERT INTO annotations (product_id, text, pos_x, pos_y, pos_z)
    VALUES (?, ?, ?, ?, ?)
");

$recycling_keys = [
    'Recycling Full Plant', 'Recycling Plant', 'NE1200RD', 'MSD700-Dump', 'MSD700-Backhoe'
];

$titles_jp = [
    "Hokkaido Crushing Full Plant" => "破砕プラント (Overview)",
    "Crushing Plant" => "砕石プラント",
    "NE750J" => "電動自走式クラッシャ",
    "NEBC10520" => "自走式ベルトコンベア",
    "NePower" => "給電コンテナハウス",
    "MSD700-Bucket" => "バケットモデル",
    "MSD700-Blade" => "ブレードモデル",
    "Micro Hydro System" => "コンテナ型 小水力発電",
    "Recycling Full Plant" => "リサイクルプラント (Overview)",
    "Recycling Plant" => "リサイクルプラント",
    "NE1200RD" => "電動自走式ロールクラッシャ",
    "MSD700-Dump" => "ダンプモデル",
    "MSD700-Backhoe" => "バックホーモデル"
];

$preview_mapping = [
    "Hokkaido Crushing Full Plant" => "files/img_preview/Hokkaido Crushing Full Plant_preview.png",
    "Crushing Plant" => "files/img_preview/Hokkaido Crushing Plant_preview.png",
    "NE750J" => "files/img_preview/NE750J_preview.png",
    "NEBC10520" => "files/img_preview/NEBC10520 Belt Conveyor_preview.png",
    "NePower" => "files/img_preview/N-ePower_preview.png",
    "Micro Hydro System" => "files/img_preview/Micro Hydro System_preview.png",
    "MSD700-Bucket" => "files/img_preview/MSD700 Bucket_preview.png",
    "MSD700-Blade" => "files/img_preview/MSD700 Blade_preview.png",
    "Recycling Full Plant" => "files/img_preview/Recycling Full Plant_preview.png",
    "Recycling Plant" => "files/img_preview/Recycling Plant_preview.png",
    "NE1200RD" => "files/img_preview/NE1200RD_preview.png",
    "MSD700-Dump" => "files/img_preview/MSD700 Dump_preview.png",
    "MSD700-Backhoe" => "files/img_preview/MSD700 Backhoe_preview.png"
];

$product_count = 0;
$annot_count = 0;

foreach ($products as $key => $p) {
    try {
        $category = in_array($key, $recycling_keys) ? 'recycling' : 'crushing';

        $title_jp = $titles_jp[$key] ?? $key;
        $title_en = $key;

        $preview_img_path = $preview_mapping[$key] ?? null;

        $stmt_product->execute([
            $key,
            $category,
            $title_jp,
            $title_en,
            $preview_img_path,
            $p['info'] ?? null,
            $p['glb_file'] ?? null,
            $p['audio_link'] ?? null,
            $p['pdf_link'] ?? null,
            $p['video_link'] ?? null,
            $p['info_img'] ?? null,
            $p['position']['x'] ?? 0,
            $p['position']['y'] ?? 0,
            $p['position']['z'] ?? 0
        ]);

        $product_id = $pdo->lastInsertId();
        $product_count++;

        if (isset($p['annotation']) && isset($p['annotation_text'])) {
            $stmt_annot->execute([
                $product_id,
                $p['annotation_text'] ?? null,
                $p['annotation']['x'] ?? 0,
                $p['annotation']['y'] ?? 0,
                $p['annotation']['z'] ?? 0
            ]);
            $annot_count++;
        }
    } catch (PDOException $e) {
        echo "[Error] Failed inserting '$key': " . $e->getMessage() . "<br>";
    }
}

echo "[OK] 2. Migrated $product_count products and $annot_count annotations<br>";


// 3. Catalog migration (from catalog-page.php)
$pdfList = [ 
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
];

echo "Start migration for catalog PDFs<br>";
$pdo->exec("TRUNCATE TABLE catalogs;");
$stmt_catalog = $pdo->prepare("INSERT INTO catalogs (title, pdf_file, preview_image, sort_order) VALUES (?, ?, ?, ?)");
$catalog_count = 0;
$sort_order = 10;
foreach ($pdfList as $title => $pdf_file) {
    try {
        $preview_image =  $title . '.jpg';
        $stmt_catalog->execute([
            $title,
            $pdf_file,
            $preview_image,
            $sort_order
        ]);
        $catalog_count++;
        $sort_order += 10;
    } catch (PDOException $e) {
        echo "[Failed] Error migrating catalog '$title': " . $e->getMessage() . "<br>";
    }
}
echo "[OK] 3. Migrated $catalog_count catalog PDFs successfully.<br>";

// 4. Video playlist & videos (from company-playlist.php)
$playlists = [
    "1.jpeg" => [
        "title" => "インフォメーション",
        "videos" => [
            ["file" => "砕石フォーラム2024.mp4", "title" => "砕石フォーラム2024", "duration" => "00:00:53"],
            ["file" => "NHKワールド／中山鉄工所インターン生の活動.mp4", "title" => "NHKワールド／中山鉄工所インターン生の活動", "duration" => "00:08:19"],
            ["file" => "2022NEW環境展_紹介動画PV.mp4", "title" => "2022NEW環境展 紹介動画PV", "duration" => "00:02:24"],
            ["file" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4", "title" => "2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像", "duration" => "00:04:09"]
        ]
    ],
    "2.jpeg" => [
        "title" => "Dendoman",
        "videos" => [
            ["file" => "NE100HBJ_PV01_20220607.mp4", "title" => "NE100HBJ　バッテリー搭載自走式クラッシャ", "duration" => "00:01:42"],
            ["file" => "NE200HBJ_PV01_20220607.mp4", "title" => "NE200HBJ　バッテリー搭載自走式クラッシャ　CO2排出ゼロ", "duration" => "00:02:06"],
            ["file" => "NE200J_油圧緩衝機構PV_03.mp4", "title" => "NE200J 油圧緩衝機構", "duration" => "00:01:26"],
            ["file" => "NE100JB_ja_50秒.mp4", "title" => "NE100JB 完全電動化自走式クラッシャ", "duration" => "00:00:51"],
            ["file" => "NE100JB_en_30秒.mp4", "title" => "The Battery Crusher NE100JB", "duration" => "00:00:31"],
            ["file" => "Dendoman NE100JP.mp4", "title" => "Dendoman NE100JP", "duration" => "00:04:41"],
            ["file" => "Dendoman series NE100JT.mp4", "title" => "Dendoman Series NE100JT", "duration" => "00:02:15"],
            ["file" => "Dendoman series NE250I.mp4", "title" => "Dendoman Series NE250I", "duration" => "00:01:59"],
            ["file" => "Dendoman　電動自走式クラッシャ　NE200I（インパクトタイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman 電動自走式クラッシャ NE200I (インパクトタイプ)/㈱中山鉄工所", "duration" => "00:02:05"],
            ["file" => "Dendoman　電動自走式クラッシャ　NE200C（コーンタイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman　電動自走式クラッシャ　NE200C（コーンタイプ）／㈱中山鉄工所", "duration" => "00:06:34"],
            ["file" => "Dendoman　電動自走式クラッシャ　NE1500WR（ロールタイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman　電動自走式クラッシャ　NE1500WR（ロールタイプ）／㈱中山鉄工所", "duration" => "00:07:55"],
            ["file" => "Dendoman 電動自走式クラッシャNE280J（ジョータイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman 電動自走式クラッシャNE280J（ジョータイプ）／㈱中山鉄工所", "duration" => "00:03:11"],
            ["file" => "Dendoman 電動自走式クラッシャ NE250J（ジョータイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman 電動自走式クラッシャ NE250J（ジョータイプ）／㈱中山鉄工所", "duration" => "00:04:28"],
            ["file" => "Dendoman　電動自走式クラッシャNE100J（ジョータイプ）／㈱中山鉄工所.mp4", "title" => "Dendoman　電動自走式クラッシャNE100J（ジョータイプ）／㈱中山鉄工所", "duration" => "00:05:19"],
            ["file" => "電動自走式クラッシャＮＥ３００Ｊ _ ㈱中山鉄工所.mp4", "title" => "電動自走式クラッシャＮＥ３００Ｊ _ ㈱中山鉄工所", "duration" => "00:03:56"]
        ]
    ],
    "3.jpeg" => [
        "title" => " Plant Design N-LINK",
        "videos" => [
            ["file" => "N-Link_コスト削減編.mp4", "title" => "N-Link コスト削減編", "duration" => "00:00:10"],
            ["file" => "N-Link_人材不足編.mp4", "title" => "N-Link 人材不足編", "duration" => "00:00:10"],
            ["file" => "N-Link_環境改善編.mp4", "title" => "N-Link 環境改善編", "duration" => "00:00:10"]
        ]
    ],
    "4.jpeg" => [
        "title" => "Crusher",
        "videos" => [
            ["file" => "不燃物破砕専用機 FRシリーズ　フリッタ.mp4", "title" => "不燃物破砕専用機 FRシリーズ　フリッタ", "duration" => "00:00:53"],
            ["file" => "VSI_ja_20210610.mp4", "title" => "リバーシブル竪型破砕・整粒機　ジャイロパクタ　NAKAYAMA VSI", "duration" => "00:00:53"],
            ["file" => "2軸パワーロール破砕機 PRC1500P／2-shaft Power Roll Crusher ｜ (株)中山鉄工所.mp4", "title" => "2軸パワーロール破砕機 PRC1500P／2-shaft Power Roll Crusher ｜ (株)中山鉄工所", "duration" => "00:00:10"],
            ["file" => "2軸解砕機 パワーロールクラッシャ　PRC1200P／㈱中山鉄工所.mp4", "title" => "2軸解砕機 パワーロールクラッシャ　PRC1200P／㈱中山鉄工所", "duration" => "00:00:53"],
            ["file" => "PRC1500P エンジン解砕.mp4", "title" => "PRC1500P エンジン解砕", "duration" => "00:00:10"],
            ["file" => "破砕能力2倍のネオコーン破砕機／Neo Cone Crusher｜ (株)中山鉄工所.mp4", "title" => "破砕能力2倍のネオコーン破砕機／Neo Cone Crusher｜ (株)中山鉄工所", "duration" => "00:00:10"],
            ["file" => "ジョー破砕機　AC Series／Jaw Crusher｜ (株)中山鉄工所.mp4", "title" => "ジョー破砕機　AC Series／Jaw Crusher｜ (株)中山鉄工所", "duration" => "00:00:10"]
        ]
    ],
    "5.jpeg" => [
        "title" => "Screen Feeder",
        "videos" => [
            ["file" => "リサイクル切替式選別機　NRE4122.mp4", "title" => "リサイクル切替式選別機　NRE4122", "duration" => "00:00:53"],
            ["file" => "小型選別機　PBS750,PBS900／Screen Kits｜ (株)中山鉄工所.mp4", "title" => "小型選別機　PBS750,PBS900／Screen Kits｜ (株)中山鉄工所", "duration" => "00:00:53"]
        ]
    ],
    "6.jpeg" => [
        "title" => "Crusher Unit",
        "videos" => [
            ["file" => "(株)中山鉄工所／可搬式破砕機　HC320GX.mp4", "title" => "(株)中山鉄工所／可搬式破砕機　HC320GX", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／可搬式破砕機　HFA4M.mp4", "title" => "(株)中山鉄工所／可搬式破砕機　HFA4M", "duration" => "00:00:53"]
        ]
    ],
    "7.jpeg" => [
        "title" => "Suction Air Separator Unit",
        "videos" => [
            ["file" => "(株)中山鉄工所／ゴミ吸引選別ユニット　NAS1200.mp4", "title" => "(株)中山鉄工所／ゴミ吸引選別ユニット　NAS1200", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／ゴミ吸引選別式ユニット　NAS900.mp4", "title" => "(株)中山鉄工所／ゴミ吸引選別式ユニット　NAS900", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／吸選機　AS1500.mp4", "title" => "(株)中山鉄工所／吸選機　AS1500", "duration" => "00:00:10"],
            ["file" => "(株)中山鉄工所／ゴミ吸引選別　ASシリーズ.mp4", "title" => "(株)中山鉄工所／ゴミ吸引選別　ASシリーズ", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／NASシリーズ.mp4", "title" => "(株)中山鉄工所／NASシリーズ", "duration" => "00:00:10"]
        ]
    ],
    "8.jpeg" => [
        "title" => "Soil Remediation",
        "videos" => [
            ["file" => "(株)中山鉄工所／土壌土質改良機　MGM501E.mp4", "title" => "(株)中山鉄工所／土壌土質改良機　MGM501E", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／土壌土質改良機　ZGMシリーズ.mp4", "title" => "(株)中山鉄工所／土壌土質改良機　ZGMシリーズ", "duration" => "00:00:53"]
        ]
    ],
    "9.jpeg" => [
        "title" => "Attachment",
        "videos" => [
            ["file" => "(株)中山鉄工所／BH70.mp4", "title" => "(株)中山鉄工所／BH70", "duration" => "00:00:53"]
        ]
    ],
    "10.jpeg" => [
        "title" => "Environment",
        "videos" => [
            ["file" => "ND50 2007.mp4", "title" => "ND50 2007", "duration" => "00:00:53"],
            ["file" => "老竹発電.mp4", "title" => "老竹発電", "duration" => "00:00:53"],
            ["file" => "松隈小水力.mp4", "title" => "松隈小水力", "duration" => "00:00:10"],
            ["file" => "(株)中山鉄工所／Ne-Power紹介動画.mp4", "title" => "(株)中山鉄工所／Ne-Power紹介動画", "duration" => "00:00:53"]
        ]
    ],
    "11.jpeg" => [
        "title" => "3DCG",
        "videos" => [
            ["file" => "(株)中山鉄工所／コーンクラッシャ NSCシリーズ.mp4", "title" => "(株)中山鉄工所／コーンクラッシャ NSCシリーズ", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／スクリーン NSRシリーズ.mp4", "title" => "(株)中山鉄工所／スクリーン NSRシリーズ", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／ジャイロパクタ SRシリーズ.mp4", "title" => "(株)中山鉄工所／ジャイロパクタ SRシリーズ", "duration" => "00:00:10"],
            ["file" => "(株)中山鉄工所／ジョークラッシャ AC、RCシリーズ.mp4", "title" => "(株)中山鉄工所／ジョークラッシャ AC、RCシリーズ", "duration" => "00:00:53"],
            ["file" => "(株)中山鉄工所／インパクトクラッシャ ACDシリーズ.mp4", "title" => "(株)中山鉄工所／インパクトクラッシャ ACDシリーズ", "duration" => "00:00:10"],
            ["file" => "(株)中山鉄工所／ロールクラッシャ PRCシリーズ.mp4", "title" => "(株)中山鉄工所／ロールクラッシャ PRCシリーズ", "duration" => "00:00:10"],
            ["file" => "(株)中山鉄工所／油圧緩衝機構 説明.mp4", "title" => "(株)中山鉄工所／油圧緩衝機構 説明", "duration" => "00:00:10"]
        ]
    ],
    "12.jpeg" => [
        "title" => "Compact Construction Machine",
        "videos" => [
            ["file" => "(株)中山鉄工所／MSD700.mp4", "title" => "(株)中山鉄工所／MSD700", "duration" => "00:00:53"]
        ]
    ]
];

echo "Start migration for video playlists & videos<br>";
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->exec("TRUNCATE TABLE videos;");
$pdo->exec("TRUNCATE TABLE video_playlists;");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");

$stmt_playlist = $pdo->prepare("INSERT INTO video_playlists (title, thumbnail_image, sort_order) VALUES (?, ?, ?)");
$stmt_video = $pdo->prepare("INSERT INTO videos (playlist_id, file_name, title, duration, sort_order) VALUES (?, ?, ?, ?, ?)");
$playlist_count = 0;
$video_count = 0;
$playlist_sort_order = 10;
foreach ($playlists as $thumbnail => $p) {
    try {
        $stmt_playlist->execute([
            $p['title'],
            $thumbnail,
            $playlist_sort_order
        ]);
        $playlist_id = $pdo->lastInsertId();
        $playlist_count++;
        $playlist_sort_order += 10;

        $video_sort_order = 10;
        foreach ($p['videos'] as $v) {
            $stmt_video->execute([
                $playlist_id,
                $v['file'],
                $v['title'],
                $v['duration'],
                $video_sort_order
            ]);
            $video_count++;
            $video_sort_order += 10;
        }
    } catch (PDOException $e) {
        echo "[Failed] Error migrating playlist '" . $p['title'] . "': " . $e->getMessage() . "<br>";
    }
}
echo "[OK] 4. Migrated $playlist_count video playlists and $video_count videos successfully.<br>";

echo "Data migration completed.<br>";

?>