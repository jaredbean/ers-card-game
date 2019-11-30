<?php
require_once 'Game.php';

echo "<h1>Egyptian Rat Screw</h1>";

// Create a fresh game and deal the cards.
//============================================================================================
$game = new Game();
$game->addPlayer("Joe Dirt", "1");
$game->addPlayer("Leroy Jenkins", "2");
$game->start();
//============================================================================================

// TEST: Each player plays 1 card to the main deck. Display decks before/after each play.
//============================================================================================
//echo '<h2>Displaying top 5 cards for each deck.</h2>';
//$game->displayDecks();
//$game->playCard(1);
//echo '<h2>Displaying top 5 cards for each deck.</h2>';
//$game->displayDecks();
//$game->playCard(2);
//echo '<h2>Displaying top 5 cards for each deck.</h2>';
//$game->displayDecks();
//============================================================================================

//var_dump($game->getPlayers()[0]->getPlayerDeck()->getCards());
//echo '<br><br>';
//var_dump($game->getPlayers()[0]->getPlayerDeck()->removeCardsFromBottom(26));


// TEST: Moving cards from game deck to bottom of player's deck after winning a round.
//============================================================================================
//$game->playCard(1);
//$game->playCard(1);
//$game->playCard(1);
//echo '<h2>Game Deck</h2>';
//var_dump($game->getGameDeck()->getCards());
////echo '<h2>Player 1 Deck</h2>';
////var_dump($game->getPlayers()[0]->getPlayerDeck()->getCards());
//echo '<h2>Player 2 Deck</h2>';
//var_dump($game->getPlayers()[1]->getPlayerDeck()->getCards());
//
//$game->moveWonCards(1);
//
//echo '<br><br><h2>After the move to player 2</h2>';
//echo '<h2>Game Deck</h2>';
//var_dump($game->getGameDeck()->getCards());
////echo '<h2>Player 1 Deck</h2>';
////var_dump($game->getPlayers()[0]->getPlayerDeck()->getCards());
//echo '<h2>Player 2 Deck</h2>';
//var_dump($game->getPlayers()[1]->getPlayerDeck()->getCards());
//============================================================================================


// TEST: Playing 70 rounds to test face card, player turn, player win logic.
//============================================================================================
for ($i = 0; $i < 70; $i++)
{
    echo "<h2>Round $i: Displaying top 5 cards for each deck.</h2>";
    $game->displayDecks();

    $playerIndex = $game->getPlayerIndex();
    if ($playerIndex == 0)
    {
        $game->playCard(1);
//        if (!($game->gameDeck->getSize() < 1))
//        {
//            $topCardArr = json_decode($game->showTopCards($game->gameDeck, 1));
////            var_dump($game->players[1]->getPlayerDeck()->getCards());
////            var_dump($topCardArr);
//            $topCard = $topCardArr[0]->value;
//            echo "<h3>Player 1 played a $topCard</h3>";
//        }

    }
    else
    {
        $game->playCard(2);
//        if (!($game->gameDeck->getSize() < 1))
//        {
//            $topCardArr = json_decode($game->showTopCards($game->gameDeck, 1));
////            var_dump($game->players[1]->getPlayerDeck()->getCards());
////            var_dump($topCardArr);
//            $topCard = $topCardArr[0]->value;
//            echo "<h3>Player 2 played a $topCard</h3>";
//        }
    }
}
//============================================================================================


// TEST: Writing and reading to the database using both serialize() and json.
//============================================================================================
//$game->writeGameToDB();
//
//echo "<h2>Original PHP Game Object</h2>";
//var_dump($game);
////echo "<h2>json_decode from DB</h2>";
////var_dump($game->readGameFromDB());
//echo "<h2>unserialize() from DB</h2>";
//$gameFromDB = $game->readGameFromDB();
//var_dump($gameFromDB);
//echo '<h2>Test DB Game object unserialized: isPlaying = ' . $gameFromDB->getIsPlaying() . '</h2>';
////$serializedGame = serialize($game);
////var_dump(unserialize($serializedGame));
//echo '<h2>Get DB Game object as json</h2>';
//echo $gameFromDB->readGameFromDB('json');
//============================================================================================



//echo $gameFromDB->Game->isPlaying;

// Debugging
//echo '<h2>Game object dump</h2>';
//var_dump($game);
//echo "<h2>Player 1 Deck:</h2>";
//$players = $game->getPlayers();
//var_dump($players[0]->getPlayerDeck());
//echo "<h2>Player 2 Deck:</h2>";
//var_dump($players[1]->getPlayerDeck());