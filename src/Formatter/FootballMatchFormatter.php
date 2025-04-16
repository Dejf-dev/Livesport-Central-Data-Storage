<?php

namespace App\Formatter;

use App\Constants\Constants;
use App\Entity\FootballMatch;

/**
 * The FootballMatchFormatter class is responsible for formatting FootballMatch entities into arrays.
 *
 * @package App\Formatter
 */
class FootballMatchFormatter
{
    /**
     * Formats a FootballMatch object as a response.
     * @param FootballMatch $match The FootballMatch object to format
     * @return array<string, int|string|null> The formatted array of the FootballMatch object
     */
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

    /**
     * Formats FootballMatch objects as a response.
     * @param FootballMatch[] $matches FootballMatch objects to format
     * @return array<array<string, int|string|null>> The array of formatted arrays of the FootballMatch object
     */
    public function formatMany(array $matches): array
    {
        return array_map(fn($match): array => $this->format($match), $matches);
    }
}