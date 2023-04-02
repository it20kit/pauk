<?php

declare(strict_types=1);
namespace App\Systems;

use App\EventBus;
use App\Events\CheckInputEvent;
use App\Events\Event;
use App\Events\InputEvent;
use App\Keyboard;

class InputSystem extends AbstractSystem
{
    private Keyboard $keyboard;

    protected array $events = [
        "inputPlayer" => "acceptPlayerInput"
    ];

    public function __construct(Keyboard $keyboard)
    {
        $this->keyboard = $keyboard;
    }

    public function getSubscriptions(): array
    {
        return [
            CheckInputEvent::class => function () {
                $this->acceptPlayerInput();
            },
        ];
    }

    public function acceptPlayerInput(): void
    {
        $acceptedData = $this->keyboard->inputPlayer();
        $this->eventPusher->push(new InputEvent($acceptedData));
        echo "Hello!!!\n";
//        $dataAfterFiltration = $this->filterPlayerInput($acceptedData);
//        $this->addPlayerInputInEventBus($eventBus, $dataAfterFiltration);
    }

    public function addPlayerInputInEventBus()
    {
        $eventBus->clearEvents();
        $logicEvent = $this->createEvent();
        $typeEvent = "checkPlayerInput";
        $keyForData = "acceptedData";
        $logicEvent->setType($typeEvent);
        $logicEvent->setData($acceptedData, $keyForData);
        $eventBus->push($logicEvent);
    }

    private function filterPlayerInput(string $acceptedData): array
    {
        if (strlen($acceptedData) === 0) {
            return [];
        }
        if (strlen($acceptedData) === 1 && !is_numeric($acceptedData)) {
            return [$acceptedData];
        } else {
            $processedData = [];
            $resultExplode =  explode(" ", $acceptedData);
            foreach ($resultExplode as $data) {
                if (is_numeric($data)) {
                    $data = (int)$data;
                    $processedData[] = $data;
                }
            }
            return $processedData;
        }
    }

}