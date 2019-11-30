<?php
require_once "Card.php";

/**
 * A class that represents a deck of cards.
 */
class Deck implements JsonSerializable
{
    /**
     * An array of Card objects that are contained in the deck.
     */
    public $cards = array();

    /**
     * An int representing the number of cards in the deck.
     */
    public $size = 0;

    /**
     * A bool that represents if the deck is able to be clicked.
     */
    public $isClickable = false;

    /**
     * Deck constructor.
     *
     */
    public function __construct()
    {
        $this->cards = array();
    }

    /**
     * A function to generate a new deck at the beginning of a game.
     */
    public function generateGameDeck()
    {
        $suits = array('C', 'D', 'H', 'S');
        $newGameDeck = $this->generateSuits($suits);
        $this->addCardsToTop($newGameDeck);
        $this->shuffleDeck();
    }

    /**
     * A function that generates a deck with 14 cards for each suit.
     * @param array $suits: The suits of the deck.
     * @return array: A deck with all required suits.
     */
    public function generateSuits(array $suits): array
    {
        $gameDeck = array();

        foreach ($suits as $suit)
        {
            for ($i = 2; $i <= 14; $i++)
            {
                $cardValue = '';
                switch ($i)
                {
                    case 14:
                        $cardValue = 'A';
                        break;
                    case 13:
                        $cardValue = 'K';
                        break;
                    case 12:
                        $cardValue = 'Q';
                        break;
                    case 11:
                        $cardValue = 'J';
                        break;
                    default:
                        $cardValue = $i;
                }
                $gameDeck[] = new Card($suit, $cardValue);
            }
        }
        return $gameDeck;
    }

    /***
     * Shuffles the existing deck in random order.
     */
    public function shuffleDeck()
    {
        for ($i = 0; $i < $this->size; $i++)
        {
            $randIndex = rand(0, $this->size -  1);
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
            array_unshift($this->cards, $card);
            $this->size++;
        }
    }

    // TODO: Handle running out of cards; win condition.
    /**
     * A function that removes cards from the top of the deck and returns them.
     * @param int $numCards: The number of cards to be removed from the top.
     * @return array : An array of card removed from the top of the deck.
     */
    public function removeCardsFromTop(int $numCards): array
    {
        if ($this->size >= $numCards)
        {
            $cardArray = array();
            // TODO: Remove these lines?
            //$counter = $numCards;
            //while ($counter > 0)
            while ($numCards > 0)
            {
                $cardArray[] = array_pop($this->cards);
                //$counter--;
                $numCards--;
                $this->size--;
            }
            return $cardArray;
        }
        else
        {
            echo "Error: Not enough cards.";
        }
    }

    // TODO: Handle running out of cards; win condition.
    /**
     * A function that removes cards from the bottom of the deck and returns them.
     * @param int $numCards: The number of cards to remove from the deck.
     * @return array: An array of Card objects that were removed from the deck.
     */
    public function removeCardsFromBottom(int $numCards): array
    {
        if ($this->size >= $numCards)
        {
            $cardArray = array();
            // TODO: Remove these lines?
            //$counter = $numCards;
            //while ($counter > 0)
            while ($numCards > 0)
            {
                $cardArray[] = array_shift($this->cards);
                //$counter--;
                $numCards--;
                $this->size--;
            }
            return $cardArray;
        }
        else
        {
            echo "Error: Not enough cards.";
        }
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
        return sizeof($this->cards);
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
    public function setIsClickable(bool $flag)
    {
        $this->isClickable = $flag;
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
            'cards' => $this->cards,
            'size' => $this->size,
            'isClickable' => $this->isClickable
        ];
    }
}