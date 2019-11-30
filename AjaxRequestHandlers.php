<?php
    ini_set('display_errors', 1);
    ini_set('html_errors', 1);
    error_reporting(E_ALL);

    require_once 'GameLogic.php';

    if (isset($_GET['action'])){
        $action = $_GET['action'];
        switch ($action){
            case 'getGame':
                if(isset($_GET['gameId'])){
                    $game = getGame($_GET['gameId']);
                    echo $game;
                }
                else {
                    die('getGame: gameId is required for this function');
                }
                break;
        }
    }

    if (isset($_POST['action'])){
        $action = $_POST['action'];
        switch ($action){
            case 'newGame':
                if (isset($_POST['name'])){
                    $newGame = createGame($_POST['name']);
                    echo json_encode($newGame);
                }
                else {
                    die('newGame: Name is required for this function.');
                }
                break;
            case 'findGame':
                if (isset($_POST['name']) && isset($_POST['gameId'])){
                    $game = findGame($_POST['gameId'], $_POST['name']);

                    echo json_encode($game);
                }
                else {
                    die('findGame: Name and Game ID are required for this function.');
                }
                break;
            case 'playCard':
                if (isset($_POST['gameId']) && isset($_POST['playerId'])){
                    playCard($_POST['gameId'], $_POST['playerId']);
                }
                else {
                    die('playCard: gameId and playerId are required for this function.');
                }
                break;
            case 'slapCard':
                if (isset($_POST['gameId']) && isset($_POST['playerId'])){
                    slapCard($_POST['gameId'], $_POST['playerId']);
                }
                else {
                    die('slapCard: gameId and playerId are required for this function.');
                }
                break;
        }
    }
?>