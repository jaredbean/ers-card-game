<?php
require_once "Card.php";

/*
 * A class that represents a deck of cards.
 */
class Deck
{
    /*
     * An array of Card objects that are contained in the deck.
     */
    private $cards = array();

    /*
     * An int representing the number of cards in the deck.
     */
    private $size = 0;

    /*
     * A bool that represents if the deck is able to be clicked.
     */
    private $isInteractable = false;

    /*
     * A function that adds an array of Cards to the bottom of
     * the deck.
     *
     * array $cards: An array of cards to be added to the deck.
     */
    public function addCards(array $cards): void
    {

    }

    /*
     * A function that removes removes cards from the top of the
     * deck.
     *
     * int $num: The number of cards to remove from the deck.
     * returns: An array of Card objects that were removed from
     *   the deck.
     */
    public function removeCards(int $num): array
    {

    }

    /*
     * A function that gets the total number of cards in the deck.
     *
     * returns: An integer representing the number of cards in the
     *   deck.
     */
    public function getSize(): int
    {

    }

    /*
     * A function that gets the value representing if the deck is
     * able to be clicked.
     *
     * returns: A bool representing if the deck is able to be
     *   clicked.
     */
    public function getInteractable(): bool
    {

    }

    /*
     * A function that sets the value representing if the deck
     * is able to be clicked.
     *
     * bool $flag: A bool representing if the deck should be able
     *   to be clicked.
     */
    public function setInteractable(bool $flag)
    {

    }
}