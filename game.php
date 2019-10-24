<?php
require_once 'Player.php';
require_once 'Deck.php';

/*
 * The Game class represents the game of Egyptian Rat Screw.
 */
class Game
{
    /*
     * An array of Player objects that represent each player
     * of the game.
     */
    private $players = array();
    /*
     * A deck object that represents the main deck of the game
     * where players place their cards and can slap.
     */
    private $mainDeck = null;
    /*
     * A bool that determines if the game is in the plays state.
     */
    private $isPlaying = false;
    /*
     * An index to the $players array indicating the turn of the
     * current player.
     */
    private $indexOfPlayersTurn = -1;

    /*
     * A function to create a new player.
     *
     * string $name: The name of the player.
     */
    public function createPlayer(string $name): void
    {

    }

    /*
     * A function to generate a new deck at the beginning
     * of a game.
     */
    public function generateDeck(): void
    {

    }

    /*
     * A function that starts the game after initialization and
     * when both players are ready.
     */
    public function start(): void
    {

    }

    /*
     * A function that moves a card from a player deck to the
     * game deck, or from the game deck to a player deck.
     *
     * Deck $fromDeck: The deck from which a card will be removed.
     * Deck $toDeck: The deck to which the card will be received.
     */
    public function moveCardToDeck(Deck $fromDeck, Deck $toDeck): void
    {

    }

    /*
     * A function that changes $indexOfCurrentPlayer from the
     * current player, to the next player in the array.
     */
    public function updatePlayerTurn(): void
    {

    }
}


