<?php
require_once 'Game.php';

echo "<h1>Egyptian Rat Screw</h1>";

$game = new Game();
$game->addPlayer("Joe Dirt", "1");
$game->addPlayer("Leroy Jenkins", "2");
$game->start();
$game->displayDecks();
for ($i = 0; $i < 26; $i++) {
    $game->playCard(1);
    $game->playCard(2);
}

$game->displayDecks();
//$game->writeGameToDB();
//echo "<h2>Json from DB</h2>" . $game->readGameFromDB();

// Debugging
//echo '<h2>Game object dump</h2>';
//var_dump($game);
//echo "<h2>Player 1 Deck:</h2>";
//$players = $game->getPlayers();
//var_dump($players[0]->getPlayerDeck());
//echo "<h2>Player 2 Deck:</h2>";
//var_dump($players[1]->getPlayerDeck());