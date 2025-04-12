<?php

namespace App\Entity;

use App\Enums\EventTypeEnum;
use App\Repository\EventRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\Table(name: 'event')]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\SequenceGenerator(sequenceName: "event_event_id_seq")]
    #[ORM\Column(name: "event_id", type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $player;

    #[ORM\Column(name: 'event_type', type: Types::STRING, length: 30)]
    private EventTypeEnum $eventType;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $minute;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "team_id",referencedColumnName: "team_id", nullable: false, onDelete: "CASCADE")]
    private Team $team;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "match_id",referencedColumnName: "match_id", nullable: false, onDelete: "CASCADE")]
    private FootballMatch $match;

    /**
     * @param string $player
     * @param EventTypeEnum $eventType
     * @param int $minute
     * @param int $scoreAway
     */
    public function __construct(string $player, EventTypeEnum $eventType, int $minute, Team $team, FootballMatch $match)
    {
        $this->player = $player;
        $this->eventType = $eventType;
        $this->minute = $minute;
        $this->team = $team;
        $this->match = $match;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): string
    {
        return $this->player;
    }

    public function setPlayer(string $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getEventType(): EventTypeEnum
    {
        return $this->eventType;
    }

    public function setEventType(EventTypeEnum $eventType): self
    {
        $this->eventType = $eventType;

        return $this;
    }

    public function getMinute(): int
    {
        return $this->minute;
    }

    public function setMinute(int $minute): self
    {
        $this->minute = $minute;

        return $this;
    }

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;

        return $this;
    }

    public function getMatch(): FootballMatch
    {
        return $this->match;
    }

    public function setMatch(FootballMatch $match): self
    {
        $this->match = $match;

        return $this;
    }
}
