<?php

declare(strict_types=1);

namespace App\Painter\BorderPainter;

use App\Vector;
use App\Painter\Canvas;

class ArrowBorderPainter implements BorderPainterInterface
{
    public function addBorderToCanvas(Canvas $canvas): void
    {
        $size = $canvas->getSize();
        [$width, $height] = [$size->x, $size->y];
        for ($i = 0; $i < $width; $i++) {
            for ($j = 0; $j < $height; $j++) {
                if (($j === 0 && $i % 2 === 0) || ($j === $height - 1 && $i % 2 === 0)) {
                    $canvas->add('/', new Vector($i, $j));
                }
                if (($j === 0 && $i % 2 !== 0) || ($j === $height - 1 && $i % 2 !== 0)) {
                    $canvas->add('\\', new Vector($i, $j));
                }
                if (($i === 0 && $j % 2 === 0) || ($i === 1 && $j % 2 !== 0)) {
                    $canvas->add('/', new Vector($i, $j));
                }
                if (($i === 1 && $j % 2 === 0) || ($i === 0 && $j % 2 !== 0)) {
                    $canvas->add('\\', new Vector($i, $j));
                }
                if (($i === $width - 2 && $j % 2 === 0) || ($i === $width - 1 && $j % 2 !== 0)) {
                    $canvas->add('\\', new Vector($i, $j));
                }
                if (($i === $width - 1 && $j % 2 === 0) || ($i === $width - 2 && $j % 2 !== 0)) {
                    $canvas->add('/', new Vector($i, $j));
                }
            }
        }
    }
}
