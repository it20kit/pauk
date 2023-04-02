<?php

declare(strict_types=1);
namespace App\Events;

class Event
{
    protected string $type = "";

    protected array $data = [];

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function setClearType(): void
    {
        $this->type = "";
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData($data, string $key): void
    {
        $this->data[$key] = $data;
    }
}