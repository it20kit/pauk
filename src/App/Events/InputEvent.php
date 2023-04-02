<?php

declare(strict_types=1);

namespace App\Events;

class InputEvent extends Event
{
    public function __construct(
        private string $input
    ) {}

    public function getInput(): string
    {
        return $this->input;
    }
}
