<?php

namespace App\Command;

use App\Entity\FootballMatch;
use App\Entity\Team;
use function PHPSTORM_META\map;

class Player
{
    private int $cntYellowCards = 0;
    private int $exclusionMinute = 91;

    public function __construct(
        private readonly string $name,
        private int            $minuteOnBench,
        private Team            $team
    )
    {
    }

    public function getCntYellowCards(): int
    {
        return $this->cntYellowCards;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function incrementCntYellowCards(): void
    {
        $this->cntYellowCards++;
    }

    public function getExclusionMinute(): int
    {
        return $this->exclusionMinute;
    }

    public function setExclusionMinute(int $exclusionMinute): void
    {
        $this->exclusionMinute = $exclusionMinute;
    }

    public function setCntYellowCards(int $cntYellowCards): void
    {
        $this->cntYellowCards = $cntYellowCards;
    }

    public function getMinuteOnBench(): int
    {
        return $this->minuteOnBench;
    }

    public function setMinuteOnBench(int $minuteOnBench): void
    {
        $this->minuteOnBench = $minuteOnBench;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }



    public static function givePlayers(Team $homeTeam, Team $awayTeam): array
    {
        // would be better to use player's names from database, but we could not have table for player
        $playerNames = ['John Smith', 'Carlos Ramirez', 'Alex Johnson', 'David Brown', 'Ethan Taylor', 'Marc Foster',
                    'Thomas Miller', 'Jake Williams', 'Ryan Wilson', 'Lucas Martinez', 'Oliver King', 'Mason Lee',
                    'James Davis', 'Daniel Harris', 'Michael Moore', 'Benjamin Clark', 'Samuel Turner', 'Henry Adams',
                    'William Scott', 'Isaac Young', 'Jack Green', 'Matthew Perez', 'Joseph Hall', 'Charles Evans',
                    'Noah Carter', 'David Murphy', 'Luke Mitchell', 'Dylan Perez', 'Liam White', 'Gabriel King',
                    'Sebastian Thompson'
        ];

        shuffle($playerNames);
        $players = [];

        for ($i = 0; $i < count($playerNames); $i++) {
            $isPlaying = $i < 22;
            $isHomePlayer = $i < 11 || ($i > 21 && $i < ceil((count($playerNames) - 22) / 2));

            $players[] = new Player($playerNames[$i], $isPlaying ? 91 : -1, $isHomePlayer
                                    ? $homeTeam : $awayTeam);
        }

        return $players;
    }

    /**
     * @param Player[] $players
     * @param Team $team
     * @param bool $isOnBench
     * @return Player
     */
    public static function givePlayerOnTeam(array $players, Team $team, bool $isOnBench): Player
    {
        $activePlayers = array_values(array_filter($players, fn(Player $player) => $isOnBench ? $player->getMinuteOnBench() == -1
                                                    : $player->getMinuteOnBench() != 0 && $player->getTeam() === $team ));
        $rndIdx = rand(0, count($activePlayers) - 1);

        return $activePlayers[$rndIdx];
    }
}