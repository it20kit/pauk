<?php

declare(strict_types=1);

namespace App;

use App\Events\Event;
use App\Painter\Painter;
use App\Painter\BorderPainter\ArrowBorderPainter;
use App\Systems\GameManagerSystem;
use App\Systems\GraphicSystem;
use App\Systems\InputSystem;

class Game1
{
    public function run(): void
    {
        $painter = new Painter(187,47);
        $painter->addBorderPainter(new ArrowBorderPainter());
        $projectManager = new ProjectManager();
        $deckFactory = new DeckFactory();
        $keyBoard = new Keyboard();
        $eventBus = new EventBus();
        $systems = [
                    new GraphicSystem($painter,$projectManager),
                    new GameManagerSystem($deckFactory),
                    new InputSystem($keyBoard)
        ];
        $systemCount = count($systems);
        $displayEvent = new Event();
        $displayEvent->setType("displayScreenWithGameRules");
        $eventBus->push($displayEvent);
        $i = 0;

        while (count($eventBus->getEvents()) > 0) {
            $systems[$i]->processEvents($eventBus);
            $i++;
            if ($i === $systemCount) {
                $i = 0;
            }
        }
    }
}
