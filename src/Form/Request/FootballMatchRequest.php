<?php

namespace App\Form\Request;

use Symfony\Component\Validator\Constraints as Assert;

class FootballMatchRequest
{
    #[Assert\NotBlank]
    #[Assert\Regex(
        pattern: '/^\d{4}-\d{2}-\d{2}$/',
        message: 'Match date must be in the format YYYY-MM-DD !'
    )]
    public string $matchDate;

    #[Assert\NotBlank]
    #[Assert\Length(min: 2, max: 100, minMessage: 'Stadium has to have at least 2 characters!',
                    maxMessage: 'Stadium has to have at most 100 characters!')]
    public string $stadium;

    #[Assert\PositiveOrZero(message: 'Number of scored goals by home team must be positive or zero!')]
    public int $scoreHome;

    #[Assert\PositiveOrZero(message: 'Number of scored goals by away team must be positive or zero!')]
    public int $scoreAway;

    #[Assert\Positive(message: 'Id of home team must be positive!')]
    public int $homeTeamId;

    #[Assert\Positive(message: 'Id of away team must be positive!')]
    public int $awayTeamId;
}