<?php
require_once 'Player.php';
require_once 'Deck.php';
require_once 'DatabaseConnection.php';

/**
 * The Game class represents the game of Egyptian Rat Screw.
 */
class Game implements JsonSerializable
{
    /**
     * The game ID used for other players to connect to the game.
     */
    public $gameId = -1;
    /**
     * An array of Player objects that represent each player of the game.
     */
    public $players = null;
    /**
     * A deck object that represents the 52 card deck before the cards are dealt to players.
     */
    public $gameDeck = null;
    /**
     * A bool that determines if the game is in the plays state.
     */
    public $isPlaying = false;
    /**
     * An index to the $players array indicating the turn of the current player.
     */
    private $playerIndex = -1;
    /***
     * A variable storing if a face card has been played.
     */
    private $isFaceCardPlayed = false;
    /***
     * Holds the number of required consecutive plays a player must perform to account for face cards.
     */
    private $requiredPlays = -1;
    /***
     * A boolean indicating if the previous player is out of cards.
     */
    private $isPlayerOutOfCards = false;
    /***
     * The playerId of the player with no cards.
     */
    private $playerIdOfNoCards = -1;
    /***
     * Boolean indicating if the round is won.
     */
    private $isRoundWon = false;
    /***
     * Boolean indicating if the player's name of a winning round should be displayed.
     */
    private $isDisplayRoundWinner;
    /***
     * Username of the round winner.
     */
    private $nameOfRoundWinner = null;
    /***
     * Boolean that indicates if the game is over.
     */
    private $isGameOver = false;
    /***
     * The name of the player that won the game.
     */
    private $nameOfGameWinner = null;


    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->gameDeck = new Deck();
        $this->players = array();
        $this->gameDeck->generateGameDeck();
    }

    /**
     * A function to create a new player.
     * @param string $name: The name of the player.
     * @param int $playerId: Id of the player.
     */
    public function addPlayer(string $name, int $playerId): void
    {
        $this->players[] = new Player($name, $playerId);
    }

    /**
     * A function that starts the game after the game initialization and when both players are ready.
     */
    public function start(): void
    {
        $this->isPlaying = true;
        $this->dealCards();
        $this->updatePlayerIndex();
        $this->gameDeck->setIsClickable(true);
    }

    /**
     * At the start of a new game, deals the cards from the main deck to the player's decks. Card's are dealt, one
     * at a time, iterating through the players array.
     */
    public function dealCards(): void
    {
        $counter = $this->gameDeck->getSize();

        while ($counter > 0)
        {
            $this->updatePlayerIndex();
            $playerDeck = $this->players[$this->playerIndex]->getPlayerDeck();
            $this->moveCardsToDeck($this->gameDeck, $playerDeck, 1);
            $counter--;
        }
        // Reset default state of $this->playerIndex
        $this->playerIndex = -1;
    }

    // TODO: Ended up only using this for dealing cards at start of game. Refactor?
    /**
     * A helper function used to deal the cards at the start of a game. Moves a card from the top of one deck to the
     * top of another deck.
     * @param Deck $fromDeck : The deck from which a card will be removed.
     * @param Deck $toDeck : The deck to which the card will be received.
     * @param int $numberOfCards
     */
    public function moveCardsToDeck(Deck $fromDeck, Deck $toDeck, int $numberOfCards): void
    {
        if ($numberOfCards <= $fromDeck->getSize())
        {
            $toDeck->addCardsToTop($fromDeck->removeCardsFromTop($numberOfCards));
        }
        else
        {
            echo "Error: Not enough cards.";
        }
    }

    /***
     * A function that allows the player to play one card from their deck to the game deck.
     * @param int $playerID
     */
    public function playCard(int $playerID)
    {
        // TODO: Remove game over is implemented in front-end. // Actually, might be good to leave this in.
        // For testing until game over is implemented on the front-end.
        if ($this->isGameOver)
        {
          return;
        }

        // If it is not the players turn, do nothing.
        if ($this->getIndexOfPlayerID($playerID) != $this->playerIndex)
        {
            return;
        }

        // If player is out of cards and the player has not won the round, the game is over.
        $numCards = $this->players[$this->getIndexOfPlayerID($playerID)]->getPlayerDeck()->getSize();
        if ($numCards === 0 && !$this->isRoundWon)
        {
            $this->gameIsOver();
        }

        // The game is stopped at the last card of the last round, allowing for slaps. If no valid slaps happened,
        // the winner of the previous round is given the game deck cards and a new round begins.
        if ($this->isRoundWon)
        {
            // TODO: Make work with more than 2 players
            // PlayerIndex was updated, so need to get playerIndex from 2 plays ago.
            $this->moveWonCards($this->playerIndex);
            $this->isRoundWon = false;
            $this->isDisplayRoundWinner = false;
            $this->nameOfRoundWinner = null;
            return;
        }

        $player = $this->players[$this->getIndexOfPlayerID($playerID)];
        $this->moveCardsToDeck($player->getPlayerDeck(), $this->gameDeck, 1);

        if ($this->isFaceCardPlayed && $this->requiredPlays > 0)
        {
            $this->requiredPlays--;
        }

        $this->updatePlayerTurn($playerID);
    }

    /***
     * Handles the event when a player has slapped the game deck. If the slap is valid, the game deck is moved to
     * the bottom of the winning player's deck and the winning player starts the next round. If the slap is invalid,
     * the player removes two cards from the bottom of his deck and places it on the bottom of the game deck; player's
     * turn resumes as normal.
     * @param $playerID
     */
    public function playerSlapEvent($playerID)
    {
        if ($this->isGameOver || $this->gameDeck->getSize() < 2)
        {
            return;
        }

        $firstCard = $this->gameDeck->getCards()[$this->gameDeck->getSize() - 1];
        $secondCard = $this->gameDeck->getCards()[$this->gameDeck->getSize() - 2];

        // Slap is valid
        if ($firstCard->value === $secondCard->value)
        {
            // For Debugging
            echo '<h2>Valid Slap!</h2>';
            //===========================

            $this->moveWonCards($playerID);

            if ($playerID === $this->playerIdOfNoCards)
            {
                $this->isPlayerOutOfCards = false;
                $this->playerIdOfNoCards = -1;
            }

            $this->isFaceCardPlayed = false;
            $this->requiredPlays = -1;
            // If a player wins the round, but the last 2 cards were slapped, the player does not win the round.
            $this->isRoundWon = false;
            // TODO: Update this to represent both the round winner and the slap winner.
            // This currently only displays the round winner, not the slap winner.
            $this->isDisplayRoundWinner = false;
            $this->nameOfRoundWinner = null;

            // TODO: Make this more robust to handle more than 2 players
            $this->updateToWinPlayerIndex($playerID);
        }
        // Slap is invalid
        else
        {
            // For debugging
            echo '<h2>NOT Valid Slap!</h2>';
            //==================================

            $this->moveLostCards($playerID);
        }
    }

    /***
     * When a player wins the round, the game deck is added to the bottom of the winning player's deck. The game deck
     * is flipped over and added to the bottom of the winning player's deck. The last played card added to the game
     * deck becomes the very bottom card of the winning player's deck.
     * @param int $playerIndex
     */
    public function moveWonCards (int $playerIndex)
    {
        $gameDeck = $this->getGameDeck();
        $gameDeckSize = $gameDeck->getSize();
        $playerDeck = $this->players[$playerIndex]->getPlayerDeck();

        $playerDeck->addCardsToBottom($gameDeck->removeCardsFromBottom($gameDeckSize));
    }

    /***
     * If a player's slap is invalid, moves 2 cards from the bottom of the player's deck to the bottom of the
     * game deck.
     * @param int $playerIndex
     */
    public function moveLostCards (int $playerIndex)
    {
        $gameDeck = $this->getGameDeck();
        $playerDeck = $this->players[$playerIndex]->getPlayerDeck();

        if ($playerDeck->getSize() === 0)
        {
            return;
        }
        else if ($playerDeck->getSize() === 1)
        {
            $gameDeck->addCardsToBottom(array_reverse($playerDeck->removeCardsFromBottom(1)));
        }
        else
        {
            $gameDeck->addCardsToBottom(array_reverse($playerDeck->removeCardsFromBottom(2)));
        }
    }

    /**
     * A function that determines the current player's turn, taking into account face-cards that have been played.
     */
    // TODO: Remove playerID parameter?
//    public function updatePlayerTurn(int $playerID): void
    public function updatePlayerTurn(int $playerID): void
    {
        // Face card is played
        // TODO: Refactor showTopCards function?
        // Using json_decode because I'm re-using a debug func showTopCards to return a string and didn't want to write new func
        $topCard = json_decode($this->showTopCards($this->gameDeck, 1));

        // Handles logic for a face card being played or not.
        switch ($topCard[0]->value)
        {
            case 'J':
                $this->requiredPlays = 1;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                // FOR DEBUGGING
                //echo '<h3>JACK PLAYED!</h3>';
                break;
            case 'Q':
                $this->requiredPlays = 2;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                // FOR DEBUGGING
                //echo '<h3>QUEEN PLAYED!</h3>';
                break;
            case 'K':
                $this->requiredPlays = 3;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                // FOR DEBUGGING
                //echo '<h3>KING PLAYED!</h3>';
                break;
            case 'A':
                $this->requiredPlays = 4;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                // FOR DEBUGGING
                //echo '<h3>ACE PLAYED!</h3>';
                break;
            default:
                // A face card was played by previous player && curr player is still required to play more
                if ($this->isFaceCardPlayed && $this->requiredPlays > 0)
                {
                        return;
                }
                // A face card was played by previous player && curr player has played the required amount
                else if ($this->isFaceCardPlayed && $this->requiredPlays == 0)
                {
                    $this->isFaceCardPlayed = false;
                    $this->requiredPlays = -1;
                    $this->isRoundWon = true;
                    $this->nameOfRoundWinner = $this->players[$this->getPreviousPlayerIndex()]->getUsername();
                    $this->isDisplayRoundWinner = true;
                    $this->updatePlayerIndex();
                }
                // A face card has not been played. Default play. This should be default case but adding the check for debugging
                // TODO: Make this the last else when finished debugging.
                else if ($this->isFaceCardPlayed == false && $this->requiredPlays == -1) //
                {
                    $this->updatePlayerIndex();
                }
                // TODO: Remove after final debugging.
                // Something went wrong
                else
                {
                    // FOR DEBUGGING
                    echo '<h1>ERROR: Unknown case in Game->updatePlayerTurn().</h1>';
                    $i = $this->playerIndex;
                    $fc = $this->isFaceCardPlayed;
                    $r = $this->requiredPlays;
                    var_dump($fc);
                    echo "Player Index: $i, FaceCardPlayed = $fc, requiredPlays = $r";
                }
        }
    }

    /***
     * Moves to the next player in the array. Updates playerIndex. Sets current player's deck to un-clickable. Sets
     * the new player's deck to clickable.
     */
    public function updatePlayerIndex()
    {
        $nextPlayerID = $this->players[$this->getNextPlayerIndex()]->getPlayerId();
        if ($this->playerIndex === -1)
        {
            $this->playerIndex++;
        }
        else if ($this->isPlayerOutOfCards == true && $this->playerIdOfNoCards == $nextPlayerID)
        {
            $this->gameIsOver();
        }
        else
        {
            $lastPlayersDeck = $this->players[$this->playerIndex]->getPlayerDeck();
            $lastPlayersDeck->setIsClickable(False);

            $this->playerIndex = $this->getNextPlayerIndex();

            $currPlayersDeck = $this->players[$this->playerIndex]->getPlayerDeck();
            $currPlayersDeck->setIsClickable(True);
        }
    }

    /***
     * // TODO: Wrote this multiple times in code, refactor using this function instead.
     * @return int
     */
    public function getNextPlayerIndex(): int
    {
        return ($this->playerIndex === sizeof($this->players) - 1) ? 0 : $this->playerIndex + 1;
    }

    /***
     * Returns the index of the previous player.
     * @return int
     */
    public function getPreviousPlayerIndex(): int
    {
        if ($this->playerIndex === 0)
        {
            return sizeof($this->players) - 1;
        }
        else
        {
            return $this->playerIndex - 1;
        }
    }

    // TODO: Shouldn't need this if we update Player constructor to auto-assign the PlayerId as the Player's Index.
    /***
     * Given a playerID, search through the players array and return the index of the player with
     * a matching playerID.
     * @param int $playerID
     * @return int
     */
    public function getIndexOfPlayerID(int $playerID)
    {
        $index = 0;
        while ($index < sizeof($this->players))
        {
            if ($this->players[$index]->getPlayerId() === $playerID)
            {
                return $index;
            }
            else
            {
                $index++;
            }
        }
    }

    /***
     * After a player wins a round, updates current players turn to the winning player.
     * @param $playerID
     */
    public function updateToWinPlayerIndex($playerID)
    {
        $currentPlayersDeck = $this->players[$this->playerIndex]->getPlayerDeck();
        $currentPlayersDeck->setIsClickable(False);

        // Set playerIndex to winner's index.
        $this->playerIndex = $this->getIndexOfPlayerID($playerID);

        $winnerPlayersDeck = $this->players[$this->playerIndex]->getPlayerDeck();
        $winnerPlayersDeck->setIsClickable(True);
    }

    /***
     * For backend debugging. Displays the top 5 cards of each deck.
     */
    public function displayDecks()
    {
        $topCards = $this->showTopCards($this->gameDeck, 5);
        $deckSize = $this->gameDeck->getSize();
        echo "<p>Game Deck [size: $deckSize] $topCards</p>";

        $topCards = $this->showTopCards($this->players[0]->getPlayerDeck(), 5);
        $deckSize = $this->players[0]->getPlayerDeck()->getSize();
        echo "<p>Player 1 Deck [size: $deckSize] $topCards</p>";

        $topCards = $this->showTopCards($this->players[1]->getPlayerDeck(), 5);
        $deckSize = $this->players[1]->getPlayerDeck()->getSize();
        echo "<p>Player 2 Deck [size: $deckSize] $topCards</p>";
    }

    // TODO: Refactor this now that we are using it in production code?
    /***
     * For backend debugging. Show the top cards of a deck.
     * @param Deck $deck: The deck to use for showing the top cards.
     * @param int $numCards: The number of cards to show from the top of the deck.
     * @return string: A json string.
     */
    public function showTopCards(Deck $deck, int $numCards): string
    {
        $showCards = array();
        $deckCards = $deck->getCards();
        $index = sizeof($deckCards) - 1; // 'top' card
        // Returns $numCards if deck is big enough, if not, returns max possible.
        $maxCards = $index >= $numCards ? $numCards : $deck->getSize();

        for ($i = 0; $i < $maxCards; $i++)
        {
            $showCards[] = $deckCards[$index];
            $index--;
        }

        return json_encode($showCards);
    }

    /***
     * Logic to perform when the game is over.
     */
    public function gameIsOver()
    {
        $this->gameDeck->setIsClickable(false);
        foreach ($this->players as $player)
        {
            $player->getPlayerDeck()->setIsClickable(false);
        }
        $this->isGameOver = true;
        $this->nameOfGameWinner = $this->players[$this->getPreviousPlayerIndex()]->getUsername();
    }

    /***
     * Writes a string representation of the PHP Game object to the DB using serialize().
     */
    public function writeGameToDB()
    {
        $gameObject = serialize($this);
        $dbh = DatabaseConnection::getInstance();

        // CREATE new game in DB
        if ($this->gameId === -1)
        {
            // $sth is the Statement Handler
            $sth = $dbh->prepare('INSERT INTO `EgyptianRatScrew` (`GameObject`) VALUES (:gameObject)');
            $sth->bindParam(':gameObject', $gameObject);
            $sth->execute();

            $this->gameId = $dbh->lastInsertId();
            // Need to call write again to save the GameID into the DB Json data.
            $this->writeGameToDB();
        }
        // UPDATE game in DB
        else
        {
            $sth = $dbh->prepare('UPDATE `EgyptianRatScrew` SET `GameObject` = :gameObject 
                                           WHERE `GameID` = :gameID');
            $sth->bindParam(':gameObject', $gameObject);
            $sth->bindParam(':gameID', $this->gameId);
            $sth->execute();
        }
    }

    /***
     * Returns a PHP Game object using unserialize() if no arguments are passed in, or returns a
     * json string if the argument 'json' is passed in.
     * @param string|null $arg: null (default) returns a php object; 'json' returns a json string.
     * @return mixed: A php Game object, or a json string of a Game object.
     */
    // TODO: Make static function, pass in GameID
    public function readGameFromDB(string $arg = null)
    {
        if ($arg != null && $arg != 'json')
        {
            echo 'Invalid argument to readGameFromDB.';
        }
        else
        {
            $dbh = DatabaseConnection::getInstance();
            $sth = $dbh->prepare('SELECT * FROM `EgyptianRatScrew` WHERE `GameID` = :gameID');
            $sth->bindParam(':gameID', $this->gameId);
            $sth->setFetchMode(PDO::FETCH_ASSOC);
            $sth->execute();
            $row = $sth->fetch();
            $gameObject = unserialize($row['GameObject']);

            return $arg == null ? $gameObject : json_encode($gameObject);
        }
    }

    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players): void
    {
        $this->players = $players;
    }

    /**
     * @return mixed
     */
    public function getGameDeck()
    {
        return $this->gameDeck;
    }

    /**
     * @param mixed $gameDeck
     */
    public function setGameDeck($gameDeck): void
    {
        $this->gameDeck = $gameDeck;
    }

    /**
     * @return mixed
     */
    public function getIsPlaying()
    {
        return $this->isPlaying;
    }

    /**
     * @param mixed $isPlaying
     */
    public function setIsPlaying($isPlaying): void
    {
        $this->isPlaying = $isPlaying;
    }

    /**
     * @return mixed
     */
    public function getPlayerIndex()
    {
        return $this->playerIndex;
    }

    /**
     * @param mixed $playerIndex
     */
    public function setPlayerIndex($playerIndex): void
    {
        $this->playerIndex = $playerIndex;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link https://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'gameId' => $this->gameId,
            'players' => $this->players,
            'gameDeck' => $this->gameDeck,
            'isPlaying' => $this->isPlaying,
            'playerIndex' => $this->playerIndex,
            'isFaceCardPlayed' => $this->isFaceCardPlayed,
            'requiredPlays' => $this->requiredPlays,
            'isPlayerOutOfCards' => $this->isPlayerOutOfCards,
            'playerIdOfNoCards' => $this->playerIdOfNoCards,
            'isRoundWon' => $this->isRoundWon,
            'isDisplayRoundWinner' => $this->isDisplayRoundWinner,
            'nameOfRoundWinner' => $this->nameOfRoundWinner,
            'isGameOver' => $this->isGameOver,
            'nameOfGameWinner' => $this->nameOfGameWinner
        ];
    }
}
