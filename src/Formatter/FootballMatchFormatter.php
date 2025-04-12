<?php

namespace App\Formatter;

use App\Constants\Constants;
use App\Entity\FootballMatch;

class FootballMatchFormatter
{
    public function format(FootballMatch $match): array
    {
        return [
            'match_id' => $match->getId(),
            'match_date' => $match->getMatchDate()->format(Constants::MATCH_DATE_FORMAT),
            'stadium' => $match->getStadium(),
            'score_home' => $match->getScoreHome(),
            'score_away' => $match->getScoreAway(),
        ];
    }

    public function formatMany(array $matches): array
    {
        return array_map(fn($match): array => $this->format($match), $matches);
    }
}