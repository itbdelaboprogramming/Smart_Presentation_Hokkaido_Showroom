-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 12:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hokkaido_showroom_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `annotations`
--

DROP TABLE IF EXISTS `annotations`;
CREATE TABLE IF NOT EXISTS `annotations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `pos_x` decimal(10,5) NOT NULL,
  `pos_y` decimal(10,5) NOT NULL,
  `pos_z` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `annotations`
--

INSERT INTO `annotations` (`id`, `product_id`, `text`, `pos_x`, `pos_y`, `pos_z`) VALUES
(1, 1, 'Overview Diorama I', 4.00000, 7.25000, 0.50000),
(2, 2, 'Crushing Plant', 4.00000, 7.25000, 0.00000),
(3, 3, 'NE750J', 0.00000, 6.25000, 0.50000),
(4, 4, 'NEBC10520', 0.00000, 3.25000, 0.50000),
(5, 5, 'NePower', 0.00000, 3.25000, 0.50000),
(6, 6, 'MSD700-Bucket', 0.00000, 0.25000, 0.25000),
(7, 7, 'MSD700-Blade', 0.00000, 0.25000, 0.25000),
(8, 8, 'Micro Hydro System', 0.00000, 2.25000, 1.00000),
(9, 9, 'Overview Diorama II', 4.00000, 7.25000, 0.00000),
(10, 10, 'Recycling Plant', 3.00000, 6.25000, 0.00000),
(11, 11, 'NE1200RD', 0.00000, 3.75000, 0.50000),
(12, 12, 'MSD700-Dump', 0.00000, 0.25000, 0.25000),
(13, 13, 'MSD700-Backhoe', 0.00000, 0.25000, 0.50000);

-- --------------------------------------------------------

--
-- Table structure for table `catalogs`
--

DROP TABLE IF EXISTS `catalogs`;
CREATE TABLE IF NOT EXISTS `catalogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `pdf_file` varchar(255) NOT NULL,
  `preview_image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 10,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `catalogs`
--

INSERT INTO `catalogs` (`id`, `title`, `pdf_file`, `preview_image`, `sort_order`) VALUES
(1, '会社案内', '会社案内.pdf', '会社案内.jpg', 10),
(2, '総合カタログ', '総合カタログ.pdf', '総合カタログ.jpg', 20),
(3, 'Dendomanシリーズ', 'Dendomanシリーズ.pdf', 'Dendomanシリーズ.jpg', 30),
(4, 'プラント-唐津砕石殿', 'Dendomanプラント-唐津砕石殿.pdf', 'プラント-唐津砕石殿.jpg', 40),
(5, 'プラント-熊礦石材殿', 'Dendomanプラント-熊礦石材殿.pdf', 'プラント-熊礦石材殿.jpg', 50),
(6, 'IoT高機能型プラント', 'IoT高機能型リサイクルプラント.pdf', 'IoT高機能型プラント.jpg', 60),
(7, 'N-Link', 'N-Link.pdf', 'N-Link.jpg', 70),
(8, 'MSD700', 'MSD700.pdf', 'MSD700.jpg', 80),
(9, 'NE750J', 'NE750J.pdf', 'NE750J.jpg', 90),
(10, 'NePower', 'NePower.pdf', 'NePower.jpg', 100),
(11, '小水力発電システム', '小水力発電システム.pdf', '小水力発電システム.jpg', 110),
(12, 'VSI', 'SR_en_ver.2.06_20220523.pdf', 'VSI.jpg', 120);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_key` varchar(100) NOT NULL,
  `category` enum('crushing','recycling') NOT NULL,
  `display_title_jp` varchar(255) DEFAULT NULL,
  `display_title_en` varchar(255) NOT NULL,
  `preview_img_path` varchar(255) NOT NULL,
  `info` text NOT NULL,
  `glb_file` varchar(255) NOT NULL,
  `audio_link` varchar(255) NOT NULL,
  `pdf_link` varchar(255) DEFAULT NULL,
  `video_link` varchar(255) DEFAULT NULL,
  `info_img` varchar(255) DEFAULT NULL,
  `camera_pos_x` decimal(10,5) NOT NULL,
  `camera_pos_y` decimal(10,5) NOT NULL,
  `camera_pos_z` decimal(10,5) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_key` (`product_key`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_key`, `category`, `display_title_jp`, `display_title_en`, `preview_img_path`, `info`, `glb_file`, `audio_link`, `pdf_link`, `video_link`, `info_img`, `camera_pos_x`, `camera_pos_y`, `camera_pos_z`) VALUES
(1, 'Hokkaido Crushing Full Plant', 'crushing', '破砕プラント (Overview)', 'Hokkaido Crushing Full Plant', 'files/img_preview/Hokkaido Crushing Full Plant_preview.png', '-', 'files/glb/Hokkaido Crushing Full Plant.glb', './audio/Crushing Plant.mp3', NULL, NULL, NULL, 30.00000, 30.00000, -30.00000),
(2, 'Crushing Plant', 'crushing', '砕石プラント', 'Crushing Plant', 'files/img_preview/Hokkaido Crushing Plant_preview.png', '中山鉄工所のDendomanプラントは、3ステージのコンパクト配置で機能性を高め、環境と安全に配慮したプラントです。複数の自走式破砕機をコンパクトに構成しているため、大掛かりな基礎工事が不要で、従来より短い工期で設置できます。採石場の切羽の変化に合わせて簡単に変更できるため、効率的なプラント運営が出来ます。各機械に搭載したセンサーやカメラで、生産量、稼働状況などを可視化し、操作室から少人数での安全な遠隔操作が可能です。短い工期と少人数でのプラント運営を可能にすることで、大幅なコスト削減を実現します。', 'files/glb/Hokkaido Crushing Plant.glb', './audio/Crushing Plant.mp3', './files/pdf/Crushing Plant.pdf', './files/video/Crushing Plant.mp4', './files/img/crushing-plant.png', 30.00000, 30.00000, -30.00000),
(3, 'NE750J', 'crushing', '電動自走式クラッシャ', 'NE750J', 'files/img_preview/NE750J_preview.png', 'NE750Jは採石場での一次破砕に使われる電動自走式クラッシャです。砕石場の切羽進展に合わせて場内の移動が可能です。処理能力は1時間あたり750トンを誇ります。', 'files/glb/NE750J.glb', './audio/NE750J.mp3', './files/pdf/NE750J.pdf', './files/video/NE750J_02.mp4', './files/img/NE750J.png', 14.00000, 11.00000, 5.00000),
(4, 'NEBC10520', 'crushing', '自走式ベルトコンベア', 'NEBC10520', 'files/img_preview/NEBC10520 Belt Conveyor_preview.png', 'NEBC10520', 'files/glb/NEBC10520 Belt Conveyor.glb', './audio/Crushing Plant.mp3', NULL, './files/video/中山鉄工所ジャイロパクタ SRシリーズ.mp4', NULL, 8.00000, 8.00000, 17.00000),
(5, 'NePower', 'crushing', '給電コンテナハウス', 'NePower', 'files/img_preview/N-ePower_preview.png', 'NePowerは電力事情が整っていない環境で、大型機械稼働や、オフィス作業を安定して行うことの出来る給電コンテナハウスです。状況・目的に応じて、電圧・出力・周波数を調整できます。大容量バッテリーによる安定した給電能力を持ち、ソーラー発電とディーゼル発電を搭載することで、急速充電にも対応しています。', 'files/glb/N-ePower.glb', './audio/NePower.mp3', './files/pdf/NePower.pdf', './files/video/NePower.mp4', './files/img/NEPOWER.png', 7.00000, 4.00000, 3.00000),
(6, 'MSD700-Bucket', 'crushing', 'バケットモデル', 'MSD700-Bucket', 'files/img_preview/MSD700 Bucket_preview.png', 'MSD700は、人が入るのが困難な現場で代わりに作業するマイクロ建機になります。クローラの付いた本体にアタッチメントを交換することで、用途に合わせて仕様変更が可能です。バケットモデルのバケットの高さは880mmで、50kgの重量物をラクラク持ち上げることが出来ます。', 'files/glb/MSD700 Bucket.glb', './audio/MSD700 Bucket.mp3', './files/pdf/MSD700.pdf', './files/video/MSD700_PV02_20220607.mp4', './files/img/Bucket.png', 2.50000, 1.00000, 2.00000),
(7, 'MSD700-Blade', 'crushing', 'ブレードモデル', 'MSD700-Blade', 'files/img_preview/MSD700 Blade_preview.png', 'MSD700は、人が入るのが困難な現場で代わりに作業するマイクロ建機になります。クローラの付いた本体にアタッチメントを交換することで、用途に合わせて仕様変更が可能です。ブレードモデルは狭いところでもパワフルに土砂を押し出すことが出来ます。', 'files/glb/MSD700 Blade.glb', './audio/MSD700 Blade.mp3', './files/pdf/MSD700.pdf', './files/video/MSD700_PV02_20220607.mp4', './files/img/Blade.png', 2.00000, 1.00000, 2.00000),
(8, 'Micro Hydro System', 'crushing', 'コンテナ型 小水力発電', 'Micro Hydro System', 'files/img_preview/Micro Hydro System_preview.png', '中山鉄工所のコンテナ型小水力発電設備は、水車を回すのに必要な落差と流量を確保できる場所であれば設置が可能です。一般河川や、農業用水路、既設ダムの放水路などに設置されています。発電能力は50kw以下を対象としており、12フィートコンテナの中にすべての機械が納まります。従来の個別品の構成をワンパッケージにすることで、建屋内の配管工事や配線工事が不要になり、現地の工事期間を短縮することができます。自動制御システムも備わっており、安全性と低コストの両方を実現しました。', 'files/glb/Micro Hydro System.glb', './audio/Micro Hydro System.mp3', './files/pdf/Micro_Hydro.pdf', './files/video/コンパクト小水力発電システム Mission to Zero-emission Micro Hydro System.mp4', './files/img/Microhydro.png', 4.00000, 1.00000, -4.00000),
(9, 'Recycling Full Plant', 'recycling', 'リサイクルプラント (Overview)', 'Recycling Full Plant', 'files/img_preview/Recycling Full Plant_preview.png', '-', 'files/glb/Recycling Full Plant.glb', './audio/Recycling Plant.mp3', NULL, NULL, NULL, -15.00000, 10.00000, -15.00000),
(10, 'Recycling Plant', 'recycling', 'リサイクルプラント', 'Recycling Plant', 'files/img_preview/Recycling Plant_preview.png', 'IoT高機能型リサイクルプラントは、生産量や機械状態の総合集中管理を行い、省人化を実現したリサイクルプラントです。全ての機械をN-Linkと連携し、安全で効率的な生産を可能にします。', 'files/glb/Recycling Plant.glb', './audio/Recycling Plant.mp3', './files/pdf/Recycling Plant.pdf', './files/video/中山鉄工所ブース プラント組立メイキング映像.mp4', './files/img/recycling-plant.png', -12.00000, 10.00000, -18.00000),
(11, 'NE1200RD', 'recycling', '電動自走式ロールクラッシャ', 'NE1200RD', 'files/img_preview/NE1200RD_preview.png', 'NE1200RDはビル解体工事向けに開発されたバッテリー搭載型の電動自走式ロールクラッシャです。大塊のコンクリート廃材を破砕し、鉄筋の除去率は95%を誇ります。作業区域内への立ち入りを検知すると、自動停止致します。このモデルはNE1500WRをベースに開発されました。', 'files/glb/NE1200RD.glb', './audio/NE1200RD.mp3', NULL, './files/video/NE1200RD_プレゼン_20230512.mp4', './files/img/NE1200RD.png', 7.00000, 6.00000, 6.00000),
(12, 'MSD700-Dump', 'recycling', 'ダンプモデル', 'MSD700-Dump', 'files/img_preview/MSD700 Dump_preview.png', 'MSD700は、人が入るのが困難な現場で代わりに作業するマイクロ建機になります。クローラの付いた本体にアタッチメントを交換することで、用途に合わせて仕様変更が可能です。ダンプモデルは100kgの重量物を運搬することができ、粉塵や土壌の運搬作業が可能です。', 'files/glb/MSD700 Dump.glb', './audio/MSD700 Dump.mp3', './files/pdf/MSD700.pdf', './files/video/ダンプ・バックホウ動画_修正版.mp4', './files/img/Dump.png', 2.50000, 1.00000, 2.00000),
(13, 'MSD700-Backhoe', 'recycling', 'バックホーモデル', 'MSD700-Backhoe', 'files/img_preview/MSD700 Backhoe_preview.png', '<p>MSD700は、人が入るのが困難な現場で代わりに作業するマイクロ建機になります。クローラの付いた本体にアタッチメントを交換することで、用途に合わせて仕様変更が可能ですバックホーモデルは地面の掘削や、クレーン作業を行うことが出来ます。</p>', 'files/glb/MSD700 Backhoe.glb', './audio/MSD700 Backhoe.mp3', './files/pdf/MSD700.pdf', './files/video/ダンプ・バックホウ動画_修正版.mp4', './files/img/Backhoe.png', 2.50000, 1.00000, 2.50000);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(17, 'admin', '$2y$10$gw6es43AoluUMyZXYWCy7uznU3qITs.lCYGPNoP9ejLyQYk0qWCUq', 'admin', '2025-11-28 04:26:11');

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

DROP TABLE IF EXISTS `videos`;
CREATE TABLE IF NOT EXISTS `videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `playlist_id` int(11) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `duration` time DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 10,
  PRIMARY KEY (`id`),
  KEY `playlist_id` (`playlist_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videos`
--

INSERT INTO `videos` (`id`, `playlist_id`, `file_name`, `title`, `duration`, `sort_order`) VALUES
(1, 1, '砕石フォーラム2024.mp4', '砕石フォーラム2024', '00:00:53', 10),
(2, 1, 'NHKワールド／中山鉄工所インターン生の活動.mp4', 'NHKワールド／中山鉄工所インターン生の活動', '00:08:19', 20),
(3, 1, '2022NEW環境展_紹介動画PV.mp4', '2022NEW環境展 紹介動画PV', '00:02:24', 30),
(4, 1, '2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像.mp4', '2018NEW環境展 中山鉄工所ブース プラント組立メイキング映像', '00:04:09', 40),
(5, 2, 'NE100HBJ_PV01_20220607.mp4', 'NE100HBJ　バッテリー搭載自走式クラッシャ', '00:01:42', 10),
(6, 2, 'NE200HBJ_PV01_20220607.mp4', 'NE200HBJ　バッテリー搭載自走式クラッシャ　CO2排出ゼロ', '00:02:06', 20),
(7, 2, 'NE200J_油圧緩衝機構PV_03.mp4', 'NE200J 油圧緩衝機構', '00:01:26', 30),
(8, 2, 'NE100JB_ja_50秒.mp4', 'NE100JB 完全電動化自走式クラッシャ', '00:00:51', 40),
(9, 2, 'NE100JB_en_30秒.mp4', 'The Battery Crusher NE100JB', '00:00:31', 50),
(10, 2, 'Dendoman NE100JP.mp4', 'Dendoman NE100JP', '00:04:41', 60),
(11, 2, 'Dendoman series NE100JT.mp4', 'Dendoman Series NE100JT', '00:02:15', 70),
(12, 2, 'Dendoman series NE250I.mp4', 'Dendoman Series NE250I', '00:01:59', 80),
(13, 2, 'Dendoman　電動自走式クラッシャ　NE200I（インパクトタイプ）／㈱中山鉄工所.mp4', 'Dendoman 電動自走式クラッシャ NE200I (インパクトタイプ)/㈱中山鉄工所', '00:02:05', 90),
(14, 2, 'Dendoman　電動自走式クラッシャ　NE200C（コーンタイプ）／㈱中山鉄工所.mp4', 'Dendoman　電動自走式クラッシャ　NE200C（コーンタイプ）／㈱中山鉄工所', '00:06:34', 100),
(15, 2, 'Dendoman　電動自走式クラッシャ　NE1500WR（ロールタイプ）／㈱中山鉄工所.mp4', 'Dendoman　電動自走式クラッシャ　NE1500WR（ロールタイプ）／㈱中山鉄工所', '00:07:55', 110),
(16, 2, 'Dendoman 電動自走式クラッシャNE280J（ジョータイプ）／㈱中山鉄工所.mp4', 'Dendoman 電動自走式クラッシャNE280J（ジョータイプ）／㈱中山鉄工所', '00:03:11', 120),
(17, 2, 'Dendoman 電動自走式クラッシャ NE250J（ジョータイプ）／㈱中山鉄工所.mp4', 'Dendoman 電動自走式クラッシャ NE250J（ジョータイプ）／㈱中山鉄工所', '00:04:28', 130),
(18, 2, 'Dendoman　電動自走式クラッシャNE100J（ジョータイプ）／㈱中山鉄工所.mp4', 'Dendoman　電動自走式クラッシャNE100J（ジョータイプ）／㈱中山鉄工所', '00:05:19', 140),
(19, 2, '電動自走式クラッシャＮＥ３００Ｊ _ ㈱中山鉄工所.mp4', '電動自走式クラッシャＮＥ３００Ｊ _ ㈱中山鉄工所', '00:03:56', 150),
(20, 3, 'N-Link_コスト削減編.mp4', 'N-Link コスト削減編', '00:00:10', 10),
(21, 3, 'N-Link_人材不足編.mp4', 'N-Link 人材不足編', '00:00:10', 20),
(22, 3, 'N-Link_環境改善編.mp4', 'N-Link 環境改善編', '00:00:10', 30),
(23, 4, '不燃物破砕専用機 FRシリーズ　フリッタ.mp4', '不燃物破砕専用機 FRシリーズ　フリッタ', '00:00:53', 10),
(24, 4, 'VSI_ja_20210610.mp4', 'リバーシブル竪型破砕・整粒機　ジャイロパクタ　NAKAYAMA VSI', '00:00:53', 20),
(25, 4, '2軸パワーロール破砕機 PRC1500P／2-shaft Power Roll Crusher ｜ (株)中山鉄工所.mp4', '2軸パワーロール破砕機 PRC1500P／2-shaft Power Roll Crusher ｜ (株)中山鉄工所', '00:00:10', 30),
(26, 4, '2軸解砕機 パワーロールクラッシャ　PRC1200P／㈱中山鉄工所.mp4', '2軸解砕機 パワーロールクラッシャ　PRC1200P／㈱中山鉄工所', '00:00:53', 40),
(27, 4, 'PRC1500P エンジン解砕.mp4', 'PRC1500P エンジン解砕', '00:00:10', 50),
(28, 4, '破砕能力2倍のネオコーン破砕機／Neo Cone Crusher｜ (株)中山鉄工所.mp4', '破砕能力2倍のネオコーン破砕機／Neo Cone Crusher｜ (株)中山鉄工所', '00:00:10', 60),
(29, 4, 'ジョー破砕機　AC Series／Jaw Crusher｜ (株)中山鉄工所.mp4', 'ジョー破砕機　AC Series／Jaw Crusher｜ (株)中山鉄工所', '00:00:10', 70),
(30, 5, 'リサイクル切替式選別機　NRE4122.mp4', 'リサイクル切替式選別機　NRE4122', '00:00:53', 10),
(31, 5, '小型選別機　PBS750,PBS900／Screen Kits｜ (株)中山鉄工所.mp4', '小型選別機　PBS750,PBS900／Screen Kits｜ (株)中山鉄工所', '00:00:53', 20),
(32, 6, '(株)中山鉄工所／可搬式破砕機　HC320GX.mp4', '(株)中山鉄工所／可搬式破砕機　HC320GX', '00:00:53', 10),
(33, 6, '(株)中山鉄工所／可搬式破砕機　HFA4M.mp4', '(株)中山鉄工所／可搬式破砕機　HFA4M', '00:00:53', 20),
(34, 7, '(株)中山鉄工所／ゴミ吸引選別ユニット　NAS1200.mp4', '(株)中山鉄工所／ゴミ吸引選別ユニット　NAS1200', '00:00:53', 10),
(35, 7, '(株)中山鉄工所／ゴミ吸引選別式ユニット　NAS900.mp4', '(株)中山鉄工所／ゴミ吸引選別式ユニット　NAS900', '00:00:53', 20),
(36, 7, '(株)中山鉄工所／吸選機　AS1500.mp4', '(株)中山鉄工所／吸選機　AS1500', '00:00:10', 30),
(37, 7, '(株)中山鉄工所／ゴミ吸引選別　ASシリーズ.mp4', '(株)中山鉄工所／ゴミ吸引選別　ASシリーズ', '00:00:53', 40),
(38, 7, '(株)中山鉄工所／NASシリーズ.mp4', '(株)中山鉄工所／NASシリーズ', '00:00:10', 50),
(39, 8, '(株)中山鉄工所／土壌土質改良機　MGM501E.mp4', '(株)中山鉄工所／土壌土質改良機　MGM501E', '00:00:53', 10),
(40, 8, '(株)中山鉄工所／土壌土質改良機　ZGMシリーズ.mp4', '(株)中山鉄工所／土壌土質改良機　ZGMシリーズ', '00:00:53', 20),
(41, 9, '(株)中山鉄工所／BH70.mp4', '(株)中山鉄工所／BH70', '00:00:53', 10),
(42, 10, 'ND50 2007.mp4', 'ND50 2007', '00:00:53', 10),
(43, 10, '老竹発電.mp4', '老竹発電', '00:00:53', 20),
(44, 10, '松隈小水力.mp4', '松隈小水力', '00:00:10', 30),
(45, 10, '(株)中山鉄工所／Ne-Power紹介動画.mp4', '(株)中山鉄工所／Ne-Power紹介動画', '00:00:53', 40),
(46, 11, '(株)中山鉄工所／コーンクラッシャ NSCシリーズ.mp4', '(株)中山鉄工所／コーンクラッシャ NSCシリーズ', '00:00:53', 10),
(47, 11, '(株)中山鉄工所／スクリーン NSRシリーズ.mp4', '(株)中山鉄工所／スクリーン NSRシリーズ', '00:00:53', 20),
(48, 11, '(株)中山鉄工所／ジャイロパクタ SRシリーズ.mp4', '(株)中山鉄工所／ジャイロパクタ SRシリーズ', '00:00:10', 30),
(49, 11, '(株)中山鉄工所／ジョークラッシャ AC、RCシリーズ.mp4', '(株)中山鉄工所／ジョークラッシャ AC、RCシリーズ', '00:00:53', 40),
(50, 11, '(株)中山鉄工所／インパクトクラッシャ ACDシリーズ.mp4', '(株)中山鉄工所／インパクトクラッシャ ACDシリーズ', '00:00:10', 50),
(51, 11, '(株)中山鉄工所／ロールクラッシャ PRCシリーズ.mp4', '(株)中山鉄工所／ロールクラッシャ PRCシリーズ', '00:00:10', 60),
(52, 11, '(株)中山鉄工所／油圧緩衝機構 説明.mp4', '(株)中山鉄工所／油圧緩衝機構 説明', '00:00:10', 70),
(53, 12, '(株)中山鉄工所／MSD700.mp4', '(株)中山鉄工所／MSD700', '00:00:53', 10);

-- --------------------------------------------------------

--
-- Table structure for table `video_playlists`
--

DROP TABLE IF EXISTS `video_playlists`;
CREATE TABLE IF NOT EXISTS `video_playlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `thumbnail_image` varchar(255) NOT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 10,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `video_playlists`
--

INSERT INTO `video_playlists` (`id`, `title`, `thumbnail_image`, `sort_order`) VALUES
(1, 'インフォメーション', '1.jpeg', 10),
(2, 'Dendoman', '2.png', 20),
(3, ' Plant Design N-LINK', '3.jpeg', 30),
(4, 'Crusher', '4.jpeg', 40),
(5, 'Screen Feeder', '5.jpeg', 50),
(6, 'Crusher Unit', '6.jpeg', 60),
(7, 'Suction Air Separator Unit', '7.jpeg', 70),
(8, 'Soil Remediation', '8.jpeg', 80),
(9, 'Attachment', '9.png', 90),
(10, 'Environment', '10.jpeg', 100),
(11, '3DCG', '11.jpeg', 110),
(12, 'Compact Construction Machine', '12.jpeg', 120);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `annotations`
--
ALTER TABLE `annotations`
  ADD CONSTRAINT `annotations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `videos`
--
ALTER TABLE `videos`
  ADD CONSTRAINT `videos_ibfk_1` FOREIGN KEY (`playlist_id`) REFERENCES `video_playlists` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
