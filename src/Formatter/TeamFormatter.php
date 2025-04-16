<?php

namespace App\Formatter;

use App\Entity\Team;

/**
 * The TeamFormatter class is responsible for formatting Team entities into arrays.
 *
 * @package App\Formatter
 */
class TeamFormatter
{
    /**
     * Formats a Team object as a response.
     * @param Team $team The Team object to format
     * @return array<string, int|string|null> The formatted array of the Team object
     */
    public function format(Team $team): array
    {
        return [
            'team_id' => $team->getId(),
            'name' => $team->getName(),
            'city' => $team->getCity(),
            'founded' => $team->getFounded(),
            'stadium' => $team->getStadium(),
        ];
    }

    /**
     * Formats a Team object as a shorter response than original.
     * @param Team $team The Team object to format
     * @return array<string, int|string|null> The formatted array of the Team object
     */
    public function formatShort(Team $team): array
    {
        return [
            'team_id' => $team->getId(),
            'name' => $team->getName(),
        ];
    }

    /**
     * Formats Team objects as a response.
     * @param Team[] $teams Team objects to format
     * @return array<array<string, int|string|null>> The array of formatted arrays of the Team object
     */
    public function formatMany(array $teams): array
    {
        return array_map(fn($team): array => $this->format($team), $teams);
    }
}