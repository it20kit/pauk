<?php

declare(strict_types=1);

namespace App\Factory;

use App\DeckFactory;
use App\Systems\GameManagerSystem;

class GameManagerSystemFactory implements FactoryInterface
{
    public function build(): object
    {
        return new GameManagerSystem(new DeckFactory());
    }
}
