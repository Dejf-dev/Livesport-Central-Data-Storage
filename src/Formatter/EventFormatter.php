<?php

namespace App\Formatter;

use App\Entity\Event;

class EventFormatter
{
    public function format(Event $event): array
    {
        return [
            'event_id' => $event->getId(),
            'player' => $event->getPlayer(),
            'event_type' => $event->getEventType()->value,
            'minute' => $event->getMinute(),
        ];
    }

    public function formatMany(array $events): array
    {
        return array_map(fn($event): array => $this->format($event), $events);
    }
}