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

    /**
     * Game constructor.
     */
    public function __construct()
    {
        //$this->discardDeck = new Deck();
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
        $this->isPlaying = true;
        $this->dealCards();
        $this->updatePlayerIndex();
        $this->gameDeck->setIsClickable(true);
    }

    /**
     * Deals the cards from the main deck to the player's decks when starting a new game.
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

    public function moveWonCards (int $playerIndex)
    {
        $gameDeck = $this->getGameDeck();
        $gameDeckSize = $gameDeck->getSize();
        $playerDeck = $this->players[$playerIndex]->getPlayerDeck();

        $playerDeck->addCardsToBottom($gameDeck->removeCardsFromBottom($gameDeckSize));
    }

    /***
     * A function that allows the player to play one card from their deck to the game deck.
     * @param int $playerID
     */
    public function playCard(int $playerID)
    {
        // TODO: Add the if check when finished with player constructor
//        if ($playerID === $this->playerIndex)
//        {
            $player = $this->players[$this->getIndexOfPlayerID($playerID)];
            $this->moveCardsToDeck($player->getPlayerDeck(), $this->gameDeck, 1);

            // **DEBUGGING**
    //            $p = $player->getPlayerId();
    //            $c = json_decode($this->showTopCards($this->gameDeck, 1));
    //            echo "<h3>Player $p played a " . $c[0]->value . "</h3>";

            if ($this->isFaceCardPlayed && $this->requiredPlays > 0)
            {
                $this->requiredPlays--;
            }

            $this->updatePlayerTurn();
//        }
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
        // TODO: Handle if a face card is played, all required cards are played (1-4), and no slap

        // Face card is played
        // Using json_decode because I coded showTopCards to return a string and didn't want to write new func
        $topCard = json_decode($this->showTopCards($this->gameDeck, 1));
        //var_dump($topCard[0]->value);

        switch ($topCard[0]->value)
        {
            case 'J':
                $this->requiredPlays = 1;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                //echo '<h3>JACK PLAYED!</h3>';
                break;
            case 'Q':
                $this->requiredPlays = 2;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                //echo '<h3>QUEEN PLAYED!</h3>';
                break;
            case 'K':
                $this->requiredPlays = 3;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
                //echo '<h3>KING PLAYED!</h3>';
                break;
            case 'A':
                $this->requiredPlays = 4;
                $this->isFaceCardPlayed = true;
                $this->updatePlayerIndex();
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
                    // TODO: Move gamedeck to winning players deck.
                    $this->moveWonCards($this->getPreviousPlayer());
                    $this->isFaceCardPlayed = false;
                    $this->requiredPlays = -1;
                    $this->updatePlayerIndex();

                    // For debugging
                    $winPlayer = $this->playerIndex == 0 ? '1' : '2';
                    //echo "<h1>Player $winPlayer wins the game deck!</h1>";
                }
                // A face card has not been played. Default play.
                else if ($this->isFaceCardPlayed == false && $this->requiredPlays == -1) // this should be default but adding the check for debugging
                {
                    $this->updatePlayerIndex();
                }
                // Something went wrong
                else
                {
                    echo '<h1>ERROR: Unknown case in Game->updatePlayerTurn().</h1>';
                    $i = $this->playerIndex;
                    $fc = $this->isFaceCardPlayed;
                    $r = $this->requiredPlays;
                    var_dump($fc);
                    echo "Player Index: $i, FaceCardPlayed = $fc, requiredPlays = $r";
                }
        }
    }

    public function updatePlayerIndex()
    {
        if ($this->playerIndex === -1)
        {
            $this->playerIndex++;
        }
        else {
            $lastPlayersDeck = $this->players[$this->playerIndex]->getPlayerDeck();
            $lastPlayersDeck->setIsClickable(False);

            // Change index of players turn, wrapping from the last index.
            $lastIndex = sizeof($this->players) - 1;
            ($this->playerIndex === $lastIndex) ? $this->playerIndex = 0 : $this->playerIndex++;

            $currPlayersDeck = $this->players[$this->playerIndex]->getPlayerDeck();
            $currPlayersDeck->setIsClickable(True);
        }
    }

    /***
     * Returns the index of the previous player.
     * @return int
     */
    public function getPreviousPlayer(): int
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

    /***
     * Writes a string representation of the string object to the DB using serialize().
     */
    public function writeGameToDB()
    {
        $gameObject = serialize($this);
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
            'playerIndex' => $this->playerIndex
        ];
    }
}
