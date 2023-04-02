<?php

declare(strict_types=1);

namespace App\Painter;

use App\Vector;

class Canvas
{
    private array $lines;

    private int $width;

    private int $height;

    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->init();
    }

    public function getSize(): Vector
    {
        return new Vector($this->width, $this->height);
    }

    public function add(string $symbol, Vector $position): void
    {
        $this->lines[$position->y][$position->x] = $symbol;
    }

    public function toString(): string
    {
        $picture = '';
        foreach ($this->lines as $line) {
            foreach ($line as $symbol) {
                $picture .= $symbol;
            }
            $picture .= "\n";
        }
        return $picture;
    }

    public function clear(): void
    {
        $this->init();
    }

    private function init(): void
    {
        $this->lines = [];
        for ($i = 0; $i < $this->height; $i++) {
            $line = [];
            for ($j = 0; $j < $this->width; $j++) {
                $line[] = " ";
            }
            $this->lines[] = $line;
        }
    }
}
