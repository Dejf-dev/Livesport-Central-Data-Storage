<?php

namespace App\Form\Request;

use App\Enums\EventTypeEnum;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A request object for Event
 *
 * @package App\Form\Request
 */
class EventRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Player has to have at least 2 characters!',
        maxMessage: 'Player has to have at most 100 characters!')]
    public ?string $player = null;

    #[Assert\NotBlank]
    #[Assert\Choice(
        callback: 'getPossibleEventTypeValues',
        message: "The event type is not valid!"
    )]
    public ?string $eventType = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: 'Minute must be positive or zero!')]
    #[Assert\Range(
        notInRangeMessage: 'Minute must be in range from 0 to 90!',
        min: 0,
        max: 90,
    )]
    public ?int $minute = null;

    #[Assert\NotNull]
    #[Assert\Positive(message: 'Team ID must be positive!')]
    public ?int $teamId = null;

    /**
     * Get all values from enum EventType
     *
     * @return array all values from enum EventType
     */
    private static function getPossibleEventTypeValues(): array
    {
        return array_column(EventTypeEnum::cases(), 'value');
    }
}