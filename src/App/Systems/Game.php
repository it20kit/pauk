<?php

declare(strict_types=1);

namespace App\Systems;

use App\Events\UpdateGameStateEvent;

abstract class Game extends AbstractSystem
{
    private bool $running = true;

    public function getSubscriptions(): array
    {
        return [
            UpdateGameStateEvent::class => function () {
                $this->run();
            },
        ];
    }

    protected function stop(): void
    {
        $this->running = false;
    }

    private function run(): void
    {
        $this->update();
        if ($this->running) {
            $this->eventPusher->push(new UpdateGameStateEvent());
        }
    }

    abstract protected function update(): void;
}
