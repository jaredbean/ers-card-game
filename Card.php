<?php

/**
 * A class that represents a card.
 */
class Card implements JsonSerializable
{
    /**
     * A string that represents the suit of a card ('C', 'D', 'H', 'S'; Clubs, Diamonds, Hearts, Spades).
     */
    public $suit = "";

    /**
     * A string that represents the value of a card ('1', '2', ..., '10', 'J', 'Q', 'K', 'A'; Jack, Queen, King, Ace).
     */
    public $value = "";

    /**
     * Card constructor.
     * @param string $suit
     * @param string $value
     */
    public function __construct(string $suit, string $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getSuit(): string
    {
        return $this->suit;
    }

    /**
     * @param string $suit
     */
    public function setSuit(string $suit): void
    {
        $this->suit = $suit;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
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
            'suit' => $this->suit,
            'value' => $this->value
        ];
    }
}