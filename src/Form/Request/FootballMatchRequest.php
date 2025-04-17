<?php

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * A request object for FootballMatch
 *
 * @package App\Form\Request
 */
class FootballMatchRequest
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}-\d{2}-\d{2}$/',
        message: 'Match date must be in the format YYYY-MM-DD !'
    )]
    public ?string $matchDate = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Stadium has to have at least 2 characters!',
                    maxMessage: 'Stadium has to have at most 100 characters!')]
    public ?string $stadium = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: 'Number of scored goals by home team must be positive or zero!')]
    public ?int $scoreHome = null;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero(message: 'Number of scored goals by away team must be positive or zero!')]
    public ?int $scoreAway = null;

    #[Assert\NotNull]
    #[Assert\Positive(message: 'Id of home team must be positive!')]
    public ?int $homeTeamId = null;

    #[Assert\NotNull]
    #[Assert\Positive(message: 'Id of away team must be positive!')]
    public ?int $awayTeamId = null;
}