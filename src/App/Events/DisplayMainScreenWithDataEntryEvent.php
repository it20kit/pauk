<?php

declare(strict_types=1);

namespace App\Events;

class DisplayMainScreenWithDataEntryEvent extends Event
{
    private array $gameData;

    public function getGameData(): array
    {
        return $this->gameData;
    }

    public function setGameData(array $gameData): void
    {
        $this->gameData = $gameData;
    }
}
