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

    $game->writeGameToDB();

    return $game;
    // Insert new game into EgyptianRatScrew.
    // $query = "Insert Into EgyptianRatScrew (GameObject) Values ('" . json_encode($game) . "');";

    // if ($conn->query($query)) {
    //     // Assign game Id to the game object.
    //     $newId = $conn->insert_id;
    //     $game->gameId = $newId;

    //     // Re-save the game state.
    //     saveGameState($conn, $game, $newId);
    //     return $game;
    // } else {
    //     die($conn->error);
    // };
}

function findGame($conn, $gameId, $name){
    $gameObject = new Game();

    $gameObject->gameId = $gameId;

    $parsedGame = cast('Game', $gameObject->readGameFromDB());

    $playerCount = sizeof($parsedGame->players);
    $parsedGame->addPlayer($name, $playerCount);

    var_dump($parsedGame);
    if ($playerCount > 1){
        $parsedGame->start();
    }

    saveGameState($conn, $parsedGame, $gameId);

    return $gameObject;

}

function playCard($conn, $gameId, $playerId){
    $game = new Game();

    $game->gameId = $gameId;

    $game = cast('Game', $game->readGameFromDB());

    $game->playCard($playerId);
}

function slapCard($conn, $gameId, $playerId){
    $game = new Game();

    $game->gameId = $gameId;

    $game = cast('Game', $game->readGameFromDB());

    $game->slapCard($playerId);
}

/**
 * Update the current game state.
 */
function saveGameState($conn, $gameState, $gameId)
{
    $query = "Update EgyptianRatScrew Set GameObject = '" . json_encode($gameState) . "' Where GameID = " . $gameId . ";";
    
    return $conn->query($query) or die($conn->error);
}

/**
 * Uses reflection to cast to an object. See https://stackoverflow.com/a/9812059
 *
 * @param string|object $destination
 * @param object $sourceObject
 * @return object
 */
function cast($destination, $sourceObject)
{
    if (is_string($destination)) {
        $destination = new $destination();
    }
    $sourceReflection = new ReflectionObject($sourceObject);
    $destinationReflection = new ReflectionObject($destination);
    $sourceProperties = $sourceReflection->getProperties();
    foreach ($sourceProperties as $sourceProperty) {
        $sourceProperty->setAccessible(true);
        $name = $sourceProperty->getName();
        $value = $sourceProperty->getValue($sourceObject);
        if ($destinationReflection->hasProperty($name)) {
            $propDest = $destinationReflection->getProperty($name);
            $propDest->setAccessible(true);
            $propDest->setValue($destination,$value);
        } else {
            $destination->$name = $value;
        }
    }
    return $destination;
}