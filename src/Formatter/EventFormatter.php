<?php

namespace App\Formatter;

use App\Entity\Event;

/**
 * The EventFormatter class is responsible for formatting Event entities into arrays.
 *
 * @package App\Formatter
 */
class EventFormatter
{
    public function __construct(
        private readonly TeamFormatter $teamFormatter,
    ) {}


    /**
     * Formats a Event object as a response.
     * @param Event $event The Event object to format
     * @return array<string, int|string|null> The formatted array of the Event object
     */
    public function format(Event $event): array
    {
        return [
            'event_id' => $event->getId(),
            'player' => $event->getPlayer(),
            'event_type' => $event->getEventType()->value,
            'minute' => $event->getMinute(),
            'team' => $this->teamFormatter->formatShort($event->getTeam())
        ];
    }

    /**
     * Formats Event objects as a response.
     * @param Event[] $events Event objects to format
     * @return array<array<string, int|string|null>> The array of formatted arrays of the Event object
     */
    public function formatMany(array $events): array
    {
        return array_map(fn($event): array => $this->format($event), $events);
    }
}