<?php
    require_once 'config.php';

    $request = $_SERVER["REQUEST_URI"];
    $requestUriOnly = strtok($request, '?');
    $curPageName = explode('/', $requestUriOnly);
    $curPageName = end($curPageName);

    if (empty($curPageName) && str_contains(strtolower(getcwd()), "htdocs")) {
         $curPageName = "home";
    }

    switch ($curPageName){
        case "":
        case "home":
            require_once 'middleware/auth_check.php'; 
            include "./pages/home.php";
            break;

        case "login":
            include "./pages/login.php";
            break;

        case "crushing-plant":
            require_once 'middleware/auth_check.php'; 
            include "./pages/crushing-plant.php";
            break;

        case "recycling-plant":
            require_once 'middleware/auth_check.php'; 
            include "./pages/recycling-plant.php";
            break;

        case "company-video":
            require_once 'middleware/auth_check.php'; 
            include "./pages/company-video.php";
            break;
        
        case "company-playlist":
            require_once 'middleware/auth_check.php'; 
            include "./pages/company-playlist.php";
            break;

        case "catalog-page":
            require_once 'middleware/auth_check.php'; 
            include "./pages/catalog-page.php";
            break;
        
        default:
            http_response_code(404);
            include "./pages/404.php";
            break;

    }
?>