<?php

declare(strict_types=1);

namespace App\Factory;

use App\DeckFactory;
use App\Systems\PaukGame;

class GameFactory implements FactoryInterface
{
    public function build(): object
    {
        return new PaukGame(new DeckFactory());
    }
}
