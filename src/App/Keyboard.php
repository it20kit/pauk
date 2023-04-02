<?php

declare(strict_types=1);

namespace App;

class Keyboard
{
    public function inputPlayer()
    {
        return \readline();
    }
}
