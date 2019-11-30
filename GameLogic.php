<?php

require_once 'Card.php';
require_once 'Deck.php';
require_once 'Player.php';
require_once 'Game.php';

/**
 * Gets the current game state.
 */
function getGame($gameId)
{
    $game = new Game();

    $game->gameId = $gameId;

    return $game->readGameFromDB('json');
}

/**
 * Creates a new game.
 */
function createGame($name)
{
    // Create the new game object.
    $game = new Game();

    $game->addPlayer($name, 0);

    $game->writeGameToDB();

    return $game;
}

function findGame($gameId, $name){
    $gameObject = new Game();

    $gameObject->gameId = $gameId;

    $parsedGame = $gameObject->readGameFromDB();

    $playerCount = sizeof($parsedGame->players);
    $parsedGame->addPlayer($name, $playerCount);

    if (sizeof($parsedGame->players) > 1){
        $parsedGame->start();
    }

    $parsedGame->writeGameToDB();

    return $parsedGame;
}

function playCard($gameId, $playerId){
    $game = new Game();

    $game->gameId = $gameId;

    $game = $game->readGameFromDB();

    $game->playCard($playerId);

    $game->writeGameToDB();
}

function slapCard($gameId, $playerId){
    $game = new Game();

    $game->gameId = $gameId;

    $game = $game->readGameFromDB();

    $game->playerSlapEvent($playerId);
    
    $game->writeGameToDB();
}