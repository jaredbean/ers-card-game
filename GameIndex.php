<?php
require_once 'Game.php';

echo "<h1>Egyptian Rat Screw</h1>";

$game = new Game();
$game->addPlayer("Joe Dirt", "1");
$game->addPlayer("Leroy Jenkins", "2");
$game->start();

echo '<h2>Displaying top 5 cards for each deck.</h2>';
$game->displayDecks();
for ($i = 0; $i < 26; $i++) {
    $game->playCard(1);
    $game->playCard(2);
}
echo '<h2>Player 1 and player 2 each played 26 hands</h2>';
$game->displayDecks();
$game->writeGameToDB();

echo "<h2>Original PHP Game Object</h2>";
var_dump($game);
//echo "<h2>json_decode from DB</h2>";
//var_dump($game->readGameFromDB());
echo "<h2>unserialize() from DB</h2>";
$gameFromDB = $game->readGameFromDB();
var_dump($gameFromDB);
echo '<h2>Test DB Game object unserialized: isPlaying = ' . $gameFromDB->getIsPlaying() . '</h2>';
//$serializedGame = serialize($game);
//var_dump(unserialize($serializedGame));
echo '<h2>Get DB Game object as json</h2>';
echo $gameFromDB->readGameFromDB('json');

//echo $gameFromDB->Game->isPlaying;

// Debugging
//echo '<h2>Game object dump</h2>';
//var_dump($game);
//echo "<h2>Player 1 Deck:</h2>";
//$players = $game->getPlayers();
//var_dump($players[0]->getPlayerDeck());
//echo "<h2>Player 2 Deck:</h2>";
//var_dump($players[1]->getPlayerDeck());