<?php

namespace App;

use App\Events\Event;

class EventBus
{
    /**
     * @var Event[]
     */
    private array $events;

    /**
     * @var array<string, callable>
     */
    private array $subscribers;

    public function run(): void
    {
        foreach ($this->events as $event) {
            if (!$handler = $this->subscribers[$event->getType()] ?? null) {
                throw new \Exception('unhandled event ' . $event->getType());
            }
            $handler($event);
        }
    }

    public function push(Event $event):void
    {
        $this->events[] = $event;
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function deleteEventByType(string $type): void
    {
        foreach ($this->events as $index => $event) {
            if ($event->getType === $type) {
                unset($this->events[$index]);
            }
        }
        $this->events = array_values($this->events);
    }

    public function clearEvents(): void
    {
        $this->events = [];
    }
}