<?php

namespace App\Formatter;

use App\Entity\Team;

class TeamFormatter
{
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

    public function formatMany(array $teams): array
    {
        return array_map(fn($team): array => $this->format($team), $teams);
    }
}