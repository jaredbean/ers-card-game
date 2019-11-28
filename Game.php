<?php
require_once 'Player.php';
require_once 'Deck.php';
require_once 'db_connection.php';
require_once 'DatabaseConnection.php';

/**
 * The Game class represents the game of Egyptian Rat Screw.
 */
class Game
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
     * A deck object that represents the discard deck of the game where players place their cards and can slap.
     */
    //public $discardDeck = null;
    /**
     * A bool that determines if the game is in the plays state.
     */
    public $isPlaying = false;
    /**
     * An index to the $players array indicating the turn of the current player.
     */
    private $playerIndex = -1;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->discardDeck = new Deck();
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
     * A function that starts the game after initialization and when both players are ready.
     */
    public function start(): void
    {
        $this->dealCards();
        $this->isPlaying = true;
        $this->updatePlayerTurn();
    }

    /**
     * Deals the cards from the main deck to the player's decks.
     */
    public function dealCards(): void
    {
        $counter = $this->gameDeck->getSize();

        while ($counter > 0)
        {
            $this->updatePlayerTurn();
            $playerDeck = $this->players[$this->playerIndex]->getPlayerDeck();
            $this->moveCardsToDeck($this->gameDeck, $playerDeck, 1);
            $counter--;
        }
        // Reset default state of $this->playerIndex
        $this->playerIndex = -1;
    }

    /**
     * A function that moves a card from the top of one deck to the top of another.
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

    public function playCard(int $playerID)
    {
        $player = $this->players[$this->getIndexOfPlayerID($playerID)];
        $this->moveCardsToDeck($player->getPlayerDeck(), $this->gameDeck, 1);
        $this->updatePlayerTurn();
    }

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

    /**
     * A function that changes $indexOfCurrentPlayer from the current player, to the next player in the array.
     */
    public function updatePlayerTurn(): void
    {
        // TODO: Current players deck becomes unclickable, next players deck becomes clickable. Check for index val -1.
        // TODO: After first players turn, gamedeck become clickable.
        // TODO: Handle player's turns when a face card is played.
        // TODO: Handle if a face card is played, all required cards are played (1-4), and no slap

        if ($this->playerIndex === -1)
        {
            $this->playerIndex++;
        }
        else
        {
            // Set previous player's deck to unclickable
            $this->players[$this->playerIndex]->getPlayerDeck()->setIsClickable(False);
            // Change index of players turn
            ($this->playerIndex == sizeof($this->players) - 1) ? $this->playerIndex = 0 : $this->playerIndex++;
            // Set new player's deck to clickable
            $this->players[$this->playerIndex]->getPlayerDeck()->setIsClickable(True);
        }

    }

    public function playerSlapEvent($playerID)
    {
        // TODO: Check if slap is valid.
        if ($this->isSlapValid($playerID))
        {
            // TODO: Player adds game discard deck to bottom of his own deck. Think about adding cards in correct order.
        }
        else
        {
            // TODO: Player discards two cards from the bottom of his deck to the bottom of the game deck.
        }

    }

    // TODO: Implement. Might not need to be its own method depending on how much code.
    public function isSlapValid($playerID)
    {

    }

    public function writeGameToDB()
    {
        $gameObject = json_encode($this);
        $dbh = DatabaseConnection::getInstance();
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

    public function readGameFromDB()
    {
        $dbh = DatabaseConnection::getInstance();
        $sth = $dbh->prepare('SELECT * FROM `EgyptianRatScrew` WHERE `GameID` = :gameID');
        $sth->bindParam(':gameID', $this->gameId);
        $sth->setFetchMode(PDO::FETCH_ASSOC);
        $sth->execute();
        $row = $sth->fetch();
        return $row['GameObject'];
    }

    public function displayDecks()
    {
        echo "<h2>Game Deck</h2>";
        echo $this->showTopCards($this->gameDeck, 5);
        echo "<p>Size: " . $this->gameDeck->getSize() . "</p>";

        echo "<h2>Player 1 Deck</h2>";
        echo $this->showTopCards($this->players[0]->getPlayerDeck(), 5);

        echo "<h2>Player 2 Deck</h2>";
        echo $this->showTopCards($this->players[1]->getPlayerDeck(), 5);
    }

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
    public function getDiscardDeck()
    {
        return $this->discardDeck;
    }

    /**
     * @param mixed $discardDeck
     */
    public function setDiscardDeck($discardDeck): void
    {
        $this->discardDeck = $discardDeck;
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
}
