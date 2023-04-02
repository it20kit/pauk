<?php

declare(strict_types=1);

namespace App;

class Keyboard
{
    public function inputPlayer(): string
    {
        $input = \readline();

        return is_bool($input) ? '' : $input;
    }
}
