<?php
    ini_set('display_errors', 1);
    ini_set('html_errors', 1);
    error_reporting(E_ALL);

    class chat { public $messages; }
    class game { public $name; public $gameId; }

    require_once 'db_connection.php';

    function checkMsgs($conn){
        $results = $conn->query("Select JsonObject From ErsJson Where Id = 22") or die($conn->error);

        $row = $results->fetch_assoc();
        $chatObject = json_decode($row['JsonObject']);
        if (isset($chatObject->messages)){
            return $chatObject->messages;
        }
        else {
            return array();
        }
    }

    function sendMsg($conn, $msg){
        $chatObject = new chat();
        $messages = checkMsgs($conn);

        array_push($messages, $msg);

        $chatObject->messages = $messages;
        $messagesJson = json_encode($chatObject);

        $conn->query("Update ErsJson Set JsonObject = '" . $messagesJson . "' Where Id = 22") or die($conn->error);
    }

    $query = "Select JsonObject From ErsJson;";

    $results = $conn->query($query) or die($conn->error);


    function getGame($gameId, $conn){
        $query = "Select JsonObject From ErsJson Where id = ". $gameId .";";
    
        $results = mysqli_query($conn, $query) or die(mysqli_error($conn));

        $row = mysqli_fetch_array($results, MYSQLI_ASSOC);
        return json_decode($row['JsonObject']);
    }

    function createGame($conn, $name){
        $game = new game();
        $game->name = $name;
        $query = "Insert Into ErsJson (JsonObject) Values ('" . json_encode($game) ."');";
    
        if ($conn->query($query)){
            $newId = $conn->insert_id;
            $game->gameId = $newId;
            return $game;
        }  else {
            die($conn->error);
        };
        // $row = mysqli_fetch_array($results, MYSQLI_ASSOC);
        // return $row['jsonObject'];
    }
    function saveGameState($gameState, $gameId, $conn){
        $query = "Update ErsJson Set JsonObject = '" . json_encode($gameState) . "' Where id = " . $gameId . ";";

        return mysqli_query($conn, $query) or die(mysqli_error($conn));
    }

    if (isset($_GET['action'])){
        $action = $_GET['action'];
        switch ($action){
            case 'checkMsgs':
                $messages = checkMsgs($conn);

                echo json_encode($messages);
                break;
            case 'getGame':
                if(isset($_GET['gameId'])){
                    $game = getGame($_GET['gameId'], $conn);
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
            case 'sendMsg':
                if (isset($_POST['msg'])){
                    echo $_POST['msg'];
                    sendMsg($conn, $_POST['msg']);
                }
            case 'newGame':
                if (isset($_POST['name'])){
                    $newGame = createGame($conn, $_POST['name']);
                    echo json_encode($newGame);   
                }
                break;
            
            // case 'saveGameState':
            // $jsonString = $_POST['gameState'];
            // $query = "Insert Into ErsJson (JsonObject) Values ('" . $_POST['gameState'] . "';";
        
            // $results = mysqli_query($conn, $query) or die(mysqli_error($conn));

            // // echo $row['jsonObject'];
            // break;
        }
    }
?>