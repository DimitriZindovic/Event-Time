<?php

namespace App\Service;

class EventService
{
    private $events = [];

    public function __construct()
    {
        $this->events = [
            ["id" => 1, "name" => "Concert", "startAt" => new \DateTime("2021-06-01 20:00:00"), "endAt" => new \DateTime("2021-06-01 23:00:00")],
            ["id" => 2, "name" => "Cinéma", "startAt" => new \DateTime("2023-12-24 10:00:00"), "endAt" => new \DateTime("2023-12-27 20:00:00")],
            ["id" => 3, "name" => "Plage", "startAt" => new \DateTime("2024-06-03 14:00:00"), "endAt" => new \DateTime("2024-06-03 18:00:00")],
        ];
    }

    public function getEvents(): array
    {
        return $this->events;
    }

    public function find($id): ?array
    {
        foreach ($this->events as $event) {
            if ($event["id"] == $id) {
                return $event;
            }
        }
        return null;
    }
}

?>