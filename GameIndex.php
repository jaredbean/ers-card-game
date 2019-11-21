<?php
require_once 'Game.php';

echo "<h1>Egyptian Rat Screw</h1>";

$game = new Game();
$game->addPlayer("Joe Dirt", "123.123.123");
$game->addPlayer("Leroy Jenkins", "456.456.456");
// TODO: Debug starting the game. Dealing the cards.
$game->start();
var_dump($game);
