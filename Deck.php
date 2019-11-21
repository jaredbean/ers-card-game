<?php
require_once "Card.php";

/**
 * A class that represents a deck of cards.
 */
class Deck
{
    /**
     * An array of Card objects that are contained in the deck.
     */
    private $cards = null;

    /**
     * An int representing the number of cards in the deck.
     */
    private $size = 0;

    /**
     * A bool that represents if the deck is able to be clicked.
     */
    private $isClickable = false;

    /**
     * Deck constructor.
     * @param array $cards
     */
    public function __construct(array $cards = null)
    {
        if ($cards = null)
        {
            $this->cards = array();
        }
        else
        {
            $this->cards = $cards;
        }
    }

    /**
     * A function to generate a new deck at the beginning of a game.
     * @return Deck
     */
    public function generateGameDeck()
    {
        $gameDeck = new Deck();

        $clubs = $this->generateSuit('C');
        $gameDeck->addCardsToTop($clubs);
        $diamonds = $this->generateSuit('D');
        $gameDeck->addCardsToTop($diamonds);
        $hearts = $this->generateSuit('H');
        $gameDeck->addCardsToTop($hearts);
        $spades = $this->generateSuit('S');
        $gameDeck->addCardsToTop($spades);

        $gameDeck->shuffleDeck();

        $this->cards = $gameDeck;
    }

    public function generateSuit(string $suit): array
    {
        $cardsOfSuit = array();

        for ($i = 2; $i <= 14; $i++)
        {
            $value = '';
            switch($i) {
                case 14:
                    $value = 'A';
                    break;
                case 13:
                    $value = 'K';
                    break;
                case 12:
                    $value = 'Q';
                    break;
                case 11:
                    $value = 'J';
                    break;
                default:
                    $value = $i;
            }
            $cardsOfSuit[] = new Card($suit, $value);
        }
        return $cardsOfSuit;
    }

    public function shuffleDeck()
    {
        $numberOfCards = sizeof($this->cards);
        for ($i = 0; $i < $numberOfCards; $i++)
        {
            $randIndex = rand(0, $numberOfCards -  1);
            $temp = $this->cards[$i];
            $this->cards[$i] = $this->cards[$randIndex];
            $this->cards[$randIndex] = $temp;
        }
    }

    /**
     * A function that adds an array of Cards to the top of the deck.
     * @param array $cards : An array of cards to be added to the deck.
     */
    public function addCardsToTop(array $cards): void
    {
        foreach ($cards as $card)
        {
            $this->cards[] = $card; // alternatively: array_push($this->cards, $card);
            $this->size++;
        }
    }

    /**
     * A function that adds an array of Cards to the bottom of the deck.
     * @param array $cards : An array of cards to be added to the deck.
     */
    public function addCardsToBottom(array $cards): void
    {
        foreach ($cards as $card)
        {
            // TODO: Check if this is how to do it!
            array_shift($this->cards, $card);
            $this->size++;
        }
    }

    /**
     * A function that removes a card from the top of the deck.
     * @param int $numCards: The number of cards to be removed from the top.
     * @return array : An array of card removed from the top of the deck.
     */
    public function removeCardsFromTop(int $numCards): array
    {
        if ($this->size >= 0)
        {
            $cardArray = array();
            $counter = $numCards;
            while ($counter > 0)
            {
                $cardArray[] = array_pop($this->card);
                $counter--;
            }
            $this->size -= $numCards;

            return $cardArray;
        }
        else
        {
            echo "Error: Not enough cards.";
        }
    }

    /**
     * A function that removes cards from the bottom of the deck.
     * @param int $num: The number of cards to remove from the deck.
     * @return array: An array of Card objects that were removed from the deck.
     */
    public function removeCardsFromBottom(int $num): array
    {

        // TODO: Update size of deck
    }

    /**
     * @return null
     */
    public function getCards()
    {
        return $this->cards;
    }

    /**
     * @param null $cards
     */
    public function setCards($cards): void
    {
        $this->cards = $cards;
    }

    /**
     * A function that gets the total number of cards in the deck.
     * @return int: An integer representing the number of cards in the deck.
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * A function that gets the value representing if the deck is able to be clicked.
     * @return bool: A bool representing if the deck is able to be clicked.
     */
    public function getIsClickable(): bool
    {
        return $this->isClickable;
    }

    /**
     * A function that sets the value representing if the deck is able to be clicked.
     * @param bool $flag: A bool representing if the deck should be able to be clicked.
     */
    public function setClickable(bool $flag)
    {
        $this->isClickable = $flag;
    }
}