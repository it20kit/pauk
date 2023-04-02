<?php

declare(strict_types=1);

namespace App\Painter\BorderPainter;

use App\Vector;
use App\Painter\Canvas;

class SimpleBorderPainter implements BorderPainterInterface
{
    private string $symbol;

    public function __construct(string $symbol)
    {
        $this->symbol = $symbol;
    }

    public function addBorderToCanvas(Canvas $canvas): void
    {
        $size = $canvas->getSize();
        [$width, $height] = [$size->x, $size->y];
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                if ($i === 0 || $i === $width - 1) {
                    $canvas->add($this->symbol, new Vector($i, $j));
                }
                if ($j === 0 || $j === $height - 1) {
                    $canvas->add($this->symbol, new Vector($i, $j));
                }
            }
        }
    }
}
