<?php
require_once 'Player.php';
require_once 'Deck.php';

/**
 * The Game class represents the game of Egyptian Rat Screw.
 */
class Game
{
    /**
     * An array of Player objects that represent each player of the game.
     */
    private $players = null;
    /**
     * A deck object that represents the the 52 card deck before the cards are dealt to players.
     */
    private $gameDeck = null;
    /**
     * A deck object that represents the main deck of the game where players place their cards and can slap.
     */
    private $discardDeck = null;
    /**
     * A bool that determines if the game is in the plays state.
     */
    private $isPlaying = false;
    /**
     * An index to the $players array indicating the turn of the current player.
     */
    private $indexOfPlayersTurn = -1;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->discardDeck = new Deck();
        $this->gameDeck = new Deck();
        $this->gameDeck->generateGameDeck();
        print_r($this->gameDeck);
    }

    /**
     * A function to create a new player.
     * @param string $name: The name of the player.
     * @param string $playerIP: IP of the player.
     */
    public function addPlayer(string $name, string $playerIP): void
    {
        $this->players[] = new Player($name, $playerIP);
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
        $numberOfCards = $this->gameDeck->getSize();
        var_dump($numberOfCards);
        $player = 0;
        while ($numberOfCards > 0)
        {
            // Remove a card from the top of the game deck
            $gameDeckCard = $this->gameDeck->removeCardsFromTop(1);
            // Get the player deck
            $playerDeck = $this->players[$player]->getPlayerDeck();
            var_dump($playerDeck);
            // Add the game deck card to the player deck
            $playerDeck->addCardsToTop($gameDeckCard);
            // Update the player deck
            $this->players[$player]->setPlayerDeck($playerDeck);

            // Decrement loop counter
            $numberOfCards--;
            // Alternates who is being dealt the card.
            $player == 0 ? $player = 1 : $player = 0;
        }
    }

//    /**
//     * A function that moves a card from a player deck to the game deck, or from the game deck to a player deck.
//     * @param Deck $fromDeck: The deck from which a card will be removed.
//     * @param Deck $toDeck: The deck to which the card will be received.
//     */
//    public function moveCardToDeck(Deck $fromDeck, Deck $toDeck): void
//    {
//        $toDeck->addCardsToTop($fromDeck->removeCardsFromTop(1));
//    }

    /**
     * A function that changes $indexOfCurrentPlayer from the current player, to the next player in the array.
     */
    public function updatePlayerTurn(): void
    {
        if ($this->indexOfPlayersTurn == sizeof($this->players) - 1)
        {
            $this->indexOfPlayersTurn++;
        }
        else
        {
            $this->indexOfPlayersTurn = 0;
        }
    }
}


