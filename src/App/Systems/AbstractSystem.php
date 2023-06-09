<?php

declare(strict_types=1);

namespace App\Systems;

use App\EventBus;
use App\EventPusher;
use App\Events\Event;

abstract class AbstractSystem
{
    protected array $events;

    protected EventPusher $eventPusher;

    /**
     * @return array<string, callable>
     */
    abstract public function getSubscriptions(): array;

    public function setEventPusher(EventPusher $eventPusher): void
    {
        $this->eventPusher = $eventPusher;
    }

    public function processEvents(EventBus $eventBus):void
    {
        foreach ($eventBus->getEvents() as $event) {
            if (isset($this->events[$event->getType()])) {
                $handlerMethod = $this->events[$event->getType()];
                $this->{$handlerMethod}($event, $eventBus);
            }
        }
    }

    public function createEvent(): Event
    {
        $event = new Event();
        return $event;
    }

    public function addDataToEvent(Event $event, array|string $data, string $keyByData):void
    {
        $event->setData($data, $keyByData);
    }

    public function clearEvenBus(EventBus $eventBus): void
    {
        $eventBus->clearEvents();
    }

    public function addEventInEventBus(EventBus $eventBus, Event $event):void
    {
        $eventBus->push($event);
    }
}