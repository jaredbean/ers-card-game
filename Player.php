<?php
require_once "Deck.php";

/**
 * A class that represents a player in the game.
 */
class Player implements JsonSerializable
{
    /**
     * A string that represents the players username.
     */
    public $username = "";

    /**
     * A number to identify the player.
     */
    public $playerId = -1;

    /**
     * A Deck object that represents the players deck.
     */
    public $playerDeck = null;

    /**
     * Player constructor.
     * @param string $username
     * @param int $playerId
     */
    public function __construct(string $username, int $playerId)
    {
        $this->username = $username;
        $this->playerId = $playerId;
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
     * @return int
     */
    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    /**
     * @param int $playerId
     */
    public function setPlayerId(int $playerId): void
    {
        $this->playerId = $playerId;
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
            'userName' => $this->username,
            'playerId' => $this->playerId,
            'playerDeck' => $this->playerDeck
        ];
    }
}