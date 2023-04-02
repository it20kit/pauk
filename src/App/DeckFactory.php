<?php

namespace App;

use App\GameObject\Deck;
use App\GameObject\Card;

class DeckFactory
{
    private const MAX_CARD_QUANTITY = 104;

    private const
        ACE = 1,
        JACK = 11,
        LADY = 12,
        KING = 13,
        TEN = 10;

    private const CARD_VALUES = [
        self::ACE => "T",
        self::JACK => "B",
        self::LADY => "D",
        self::KING => "K",
        self::TEN => "t",
    ];

    private const MAX_CARD_VALUE = 13;

    public function createMainDeck(): Deck
    {
        $mainDeck = new Deck();
        for ($cardValue = 1; $mainDeck->countingCard() < self::MAX_CARD_QUANTITY; $cardValue++) {
            $mainDeck->addCards([new Card(self::CARD_VALUES[$cardValue] ?? (string)$cardValue, $cardValue)]);
            if ($cardValue === self::MAX_CARD_VALUE) {
                $cardValue = 0;
            }
        }
        return $mainDeck;
    }

    /**
     * @var  Card[] $cards
     */
    public function createDeck(array $cards): Deck
    {
        $deck = new Deck();
        $lastKeyCard = array_key_last($cards);

        foreach ($cards as $key  => $card) {
            $card->setReversedCard(true);
            if ($key === $lastKeyCard) {
                $card->setReversedCard(false);
            }
            $deck->addCards([$card]);
        }
        return  $deck;
    }

    public function createKingCard(): Card
    {
        return new Card("K", 13);
    }

}