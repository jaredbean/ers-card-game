<?php
    ini_set('display_errors', 1);
    ini_set('html_errors', 1);
    error_reporting(E_ALL);

    require_once 'Db_connection.php';
    require_once 'GameLogic.php';

    if (isset($_GET['action'])){
        $action = $_GET['action'];
        switch ($action){
            case 'getGame':
                if(isset($_GET['gameId'])){
                    $game = getGame($conn, $_GET['gameId']);
                }
                else {
                    break;
                    // echo 'Server.getGame: No game id';
                }

                if(!isset($game->gameId)){
                    $game->gameId = $_GET['gameId'];
                }
                
                echo json_encode($game);
                break;
        }
    }

    if (isset($_POST['action'])){
        $action = $_POST['action'];
        switch ($action){
            case 'newGame':
                if (isset($_POST['name'])){
                    $newGame = createGame($conn, $_POST['name']);
                    echo json_encode($newGame);
                }
                else {
                    die('newGame: Name is required for this function.');
                }
                break;
            case 'findGame':
                if (isset($_POST['name']) && isset($_POST['gameId'])){
                    $game = findGame($conn, $_POST['gameId'], $_POST['name']);

                    echo json_encode($game);
                }
                else {
                    die('findGame: Name and Game ID are required for this function.');
                }
                break;
            // case 'playCard':
            //     if (isset($_POST['gameId']) && isset($_POST['playerId'])){
            //         playCard($conn, $_POST)
            //     }
            
            // case 'saveGameState':
            // $jsonString = $_POST['gameState'];
            // $query = "Insert Into ErsJson (JsonObject) Values ('" . $_POST['gameState'] . "';";
        
            // $results = mysqli_query($conn, $query) or die(mysqli_error($conn));

            // // echo $row['jsonObject'];
            // break;
        }
    }
?>