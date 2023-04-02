<?php

declare(strict_types=1);

namespace App\Painter;

use App\Vector;
use App\Painter\BorderPainter\BorderPainterInterface;

class Painter
{
    private Canvas $canvas;

    private ?BorderPainterInterface $borderPainter = null;

    public function __construct(int $width, int $height)
    {
        $this->canvas = new Canvas($width, $height);
    }

    public function addBorderPainter(BorderPainterInterface $borderPainter): void
    {
        $this->borderPainter = $borderPainter;
    }

    public function addPicture(string $picture, int $x, int $y): void
    {
        $x += 1;
        $y -= 1;
        $initialX = $x;

        for ($i = 0; $i < strlen($picture); $i++) {
            if ($picture[$i] === "\n") {
                $y += 1;
                $x = $initialX;
                continue;
            }
            $this->canvas->add($picture[$i], new Vector($x, $y));
            $x++;
        }
    }

    public function clear(): void
    {
        $this->canvas->clear();
        system('clear');
    }

    public function display(): void
    {
        if ($this->borderPainter) {
            $this->borderPainter->addBorderToCanvas($this->canvas);
        }
        echo $this->canvas->toString();
    }
}
