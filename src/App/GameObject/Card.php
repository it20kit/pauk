<?php

declare(strict_types=1);

namespace App\GameObject;

class Card
{
    private string $type;

    private int $nominalValue;

    private bool $isThisCardReversed = false;

    public function __construct(string $type, int $nominalValue)
    {
        $this->type = $type;
        $this->nominalValue = $nominalValue;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNominalValue(): int
    {
        return $this->nominalValue;
    }

    public function isReversed(): bool
    {
        return $this->isThisCardReversed;
    }

    public function setReversedCard(bool $state): void
    {
        $this->isThisCardReversed = $state;
    }

}
