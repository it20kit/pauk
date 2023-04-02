<?php

declare(strict_types=1);

namespace App\GameObject;

class Deck
{
    /**
     * @var Card[]
     */
    public array $storage = [];

    public function addCards(array $cards): void
    {
        foreach ($cards as $addCard) {
            $this->storage[] = $addCard;
        }
    }

    /**
     * @return Card[]
     */
    public function getCards(): array
    {
        return $this->storage;
    }

    public function takeCards(int $numberCard): array
    {
        $cards = [];
        foreach ($this->storage as $key => $card) {
            if ($key >= $numberCard) {
                $cards[] = $card;
            }
        }
        return $cards;
    }

    public function deletCardsInDeck(int $numberCard): void
    {
        foreach ($this->storage as $key => $card) {
            if ($key >= $numberCard) {
                unset($this->storage[$key]);
            }
        }
        if (count($this->storage) !== 0) {
            $lastKey = array_key_last($this->storage);
            $this->storage[$lastKey]->setReversedCard(false);
        }
        $this->storage = array_values($this->storage);
    }

    public function isCanYouPut(array $cards): bool
    {
        $ferstCardInArray = $cards[0];

        if ($ferstCardInArray === null) {
            return false;
        }

        $ferstCard = $ferstCardInArray->getNominalValue();
        $lastCardInDeck = end($this->storage);
        if ($lastCardInDeck === false) {
            $lastCard = $ferstCard + 1;
        } else {
            $lastCard = $lastCardInDeck->getNominalValue();
        }
        if ($lastCard - $ferstCard !== 1) {
            return false;
        }
        return true;
    }

    public function isCardCanBeTaken(int $numberCard): bool
    {
        if (!isset($this->storage[$numberCard])) {
            return false;
        }

        $interval = 1;
        $cardsInOrder = null;
        $keyLastElm = array_key_last($this->storage);
        $cardsStandingInOrder = $keyLastElm - $numberCard;
        $cardThatTaken = $this->storage[$numberCard];
        if ($cardThatTaken->isReversed()) {
            return false;
        }
        if ($keyLastElm === $numberCard) {
            return true;
        }
        foreach ($this->storage as $key => $card) {
            if ($key > $numberCard) {
                if ($cardThatTaken->getNominalValue() - $card->getNominalValue() === $interval) {
                    $cardsInOrder++;
                    $interval++;
                }
            }
        }

        if ($cardsInOrder !== $cardsStandingInOrder) {
            return false;
        }

        return true;
    }

    public function thisDeckCompleted(): bool
    {
        $storage = $this->storage;
        $interval = 1;
        $counter = null;

        if (count($this->storage) === 0) {
            return false;
        }

        $searchKingInDeck = function () {
            $storage = $this->storage;
            foreach ($storage as $key => $card) {
                if (!$card->isReversed()) {
                    if ($card->getNominalValue() === 13) {
                        return $key;
                    }
                }
            }
            return false;
        };

        if ($searchKingInDeck === false) {
            return false;
        }
        $j = $searchKingInDeck();
        $a = $j + 1;

        for ($i = $a; $i < count($storage); $i++) {
            $rezult = $storage[$j]->getNominalValue() - $storage[$i]->getNominalValue();
            if ($rezult === $interval) {
                $interval++;
                $counter++;
                if ($counter === 12) {
                    return true;
                }
            } else {
                $counter = 0;
                $interval = 1;
                $j++;
            }
        }
        return false;
    }

    public function searchKeyKingInCompletedDeck(): int
    {
        $keyLastCard = array_key_last($this->storage);
        $keyCardsKing = $keyLastCard - 12;
        return $keyCardsKing;
    }

    public function reversed(): void
    {
        foreach ($this->storage as $key => $card) {
            $card->setReversedCard(false);
        }
    }

    public function isDeckEmpty(): bool
    {
        return count($this->storage) === 0;
    }

    public function countingCard(): int
    {
        return count($this->storage);
    }

    public function getCard(int $numberCard): Card
    {
        return $this->storage[$numberCard];
    }

    public function setStateReversed(int $numberCard, bool $isReversed): void
    {
        $this->storage[$numberCard]->setReversedCard($isReversed);
    }

    public function giveCards(int $numberOfCards): array
    {
        $cards = [];
        while (count($cards) < $numberOfCards) {
            $cards[] = array_pop($this->storage);
        }
        shuffle($this->storage);
        return $cards;
    }

    public function giveASpecificCard(int $nominalValue): ?Card
    {
        foreach ($this->storage as $card) {
            if ($card->getNominalValue() === $nominalValue) {
                return $card;
            }
        }
        return null;
    }

}
