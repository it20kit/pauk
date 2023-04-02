<?php

declare(strict_types=1);

namespace App\Factory;

use App\Painter\BorderPainter\ArrowBorderPainter;
use App\Painter\Painter;
use App\ProjectManager;
use App\Systems\GraphicSystem;

class GraphicSystemFactory implements FactoryInterface
{
    public function build(): object
    {
        $painter = new Painter(187,47);
        $painter->addBorderPainter(new ArrowBorderPainter());
        $projectManager = new ProjectManager();

        return new GraphicSystem($painter, $projectManager);
    }
}
