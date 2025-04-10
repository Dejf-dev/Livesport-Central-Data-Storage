<?php

namespace App\Entity;

use App\Enums\EventType;
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

    #[ORM\Column(enumType: EventType::class)]
    private EventType $eventType;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $minute;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $scoreAway;

    /**
     * @param string $player
     * @param EventType $eventType
     * @param int $minute
     * @param int $scoreAway
     */
    public function __construct(string $player, EventType $eventType, int $minute, int $scoreAway)
    {
        $this->player = $player;
        $this->eventType = $eventType;
        $this->minute = $minute;
        $this->scoreAway = $scoreAway;
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

    public function getEventType(): EventType
    {
        return $this->eventType;
    }

    public function setEventType(EventType $eventType): self
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

    public function getScoreAway(): int
    {
        return $this->scoreAway;
    }

    public function setScoreAway(int $scoreAway): self
    {
        $this->scoreAway = $scoreAway;

        return $this;
    }


}
