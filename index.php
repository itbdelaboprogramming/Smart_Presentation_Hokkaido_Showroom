<?php
    // include "main.php";
    $request = $_SERVER["REQUEST_URI"];
    $curPageName = explode('/', $request);
    $curPageName = end($curPageName);;


    switch ($curPageName){
        case "":
            include "./pages/home.php";
            break;

        case "home":
            include "./pages/home.php";
            break;

        case "crushing-plant":
            include "./pages/crushing-plant.php";
            break;

        case "recycling-plant":
            include "./pages/recycling-plant.php";
            break;

        case "company-video":
            include "./pages/company-video.php";
            break;
        
        default:
            http_response_code(404);
            include "./pages/404.php";
            break;

    }