<?php
require_once "Deck.php";

/*
 * A class that represents a player in the game.
 */
class Player
{
    /*
     * A string that represents the players username.
     */
    private $username = "";

    /*
     * A string that represents the players IP address.
     */
    private $playerIp = "";

    /*
     * A Deck object that represents the players deck.
     */
    private $playerDeck = null;


    /*
     * A function that gets the players username.
     *
     * returns: A string.
     */
    public function getUsername(): string
    {

    }

    /*
     * A function that gets the user's IP address.
     *
     * returns: A string.
     */
    public function getIp(): string
    {

    }
}