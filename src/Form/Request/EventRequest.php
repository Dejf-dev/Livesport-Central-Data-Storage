<?php

namespace App\Form\Request;

use App\Enums\EventType;
use Symfony\Component\Validator\Constraints as Assert;

class EventRequest
{
    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Player has to have at least 2 characters!',
        maxMessage: 'Player has to have at most 100 characters!')]
    public string $player;

    #[Assert\NotBlank]
    #[Assert\Choice(
        callback: 'getPossibleEventTypeValues',
        message: "The event type is not valid!"
    )]
    public string $eventType;

    #[Assert\PositiveOrZero(message: 'Minute must be positive or zero!')]
    #[Assert\Range(
        min: 0,
        max: 120,
        notInRangeMessage: 'Minute must be in range from 0 to 120!',
    )]
    public int $minute;

    #[Assert\Positive(message: 'Team ID must be positive!')]
    public int $teamId;

    #[Assert\Positive(message: 'Match ID must be positive!')]
    public int $matchId;

    private static function getPossibleEventTypeValues(): array
    {
        return array_column(EventType::cases(), 'value');
    }
}