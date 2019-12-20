<?php
    require_once "../../config.php";
    require_once 'controller.php';

    $curl = new Curl($url);

    // Getting all contents of selected date range
    if (!empty($_POST) && isset($_GET['action']) && $_GET['action'] == 'histories') 
    {
        // how many items per page?
        $controller = new Controller();
        $data = $controller->getHistories($url, $_POST["startDate"], $_POST["endDate"], $_POST["page"], $_POST["itemsPerPage"]);
        echo json_encode($data);
    }

    // Getting graph data for drawing
    if (!empty($_POST) && isset($_GET['action']) && $_GET['action'] == 'drawGraph') 
    {
        $controller = new Controller();
        $logs = $controller->getGraphData($url, $_POST['historyId']);
        echo json_encode($logs);
    }


    
