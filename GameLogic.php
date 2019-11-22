<?php

require_once 'Card.php';
require_once 'Deck.php';
require_once 'Player.php';
require_once 'Game.php';

/**
 * Gets the current game state.
 */
function getGame($conn, $gameId)
{
    $query = "Select GameObject From EgyptianRatScrew Where GameID = " . $gameId . ";";

    $results = $conn->query($query) or die($conn->error);

    $row = $results->fetch_assoc();

    // Be sure to decode the database json object.
    return json_decode($row['GameObject']);
}

/**
 * Creates a new game.
 */
function createGame($conn, $name)
{
    // Create the new game object.
    $game = new Game();

    $game->addPlayer($name, 0);

    // Insert new game into EgyptianRatScrew.
    $query = "Insert Into EgyptianRatScrew (GameObject) Values ('" . json_encode($game) . "');";

    if ($conn->query($query)) {
        // Assign game Id to the game object.
        $newId = $conn->insert_id;
        $game->gameId = $newId;

        // Re-save the game state.
        saveGameState($conn, $game, $newId);
        return $game;
    } else {
        die($conn->error);
    };
}

function findGame($conn, $gameId, $name){
    $gameObject = getGame($conn, $gameId);

    $playerCount = count($gameObject->players);
    $gameObject->addPlayer($name, $playerCount - 1);

    saveGameState($conn, $gameObject, $gameId);

    return $gameObject;

}

/**
 * Update the current game state.
 */
function saveGameState($conn, $gameState, $gameId)
{
    $query = "Update EgyptianRatScrew Set GameObject = '" . json_encode($gameState) . "' Where GameID = " . $gameId . ";";
    
    return $conn->query($query) or die($conn->error);
}
