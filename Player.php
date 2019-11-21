<?php
require_once "Deck.php";

/**
 * A class that represents a player in the game.
 */
class Player
{
    /**
     * A string that represents the players username.
     */
    private $username = "";

    /**
     * A string that represents the players IP address.
     */
    private $playerIP = "";

    /**
     * A Deck object that represents the players deck.
     */
    private $playerDeck = null;

    /**
     * Player constructor.
     * @param string $username
     * @param string $playerIp
     */
    public function __construct(string $username, string $playerIp)
    {
        $this->username = $username;
        $this->playerIP = $playerIp;
        $this->playerDeck = new Deck();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getPlayerIP(): string
    {
        return $this->playerIP;
    }

    /**
     * @param string $playerIP
     */
    public function setPlayerIP(string $playerIP): void
    {
        $this->playerIP = $playerIP;
    }

    /**
     * @return Deck
     */
    public function getPlayerDeck(): Deck
    {
        return $this->playerDeck;
    }

    /**
     * @param null $playerDeck
     */
    public function setPlayerDeck($playerDeck): void
    {
        $this->playerDeck = $playerDeck;
    }
}