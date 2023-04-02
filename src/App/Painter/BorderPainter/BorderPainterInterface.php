<?php

declare(strict_types=1);

namespace App\Painter\BorderPainter;

use App\Painter\Canvas;

interface BorderPainterInterface
{
    public function addBorderToCanvas(Canvas $canvas): void;
}
