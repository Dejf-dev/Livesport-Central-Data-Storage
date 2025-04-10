<?php

namespace App\Entity;

use App\Repository\FootballMatchRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: FootballMatchRepository::class)]
#[ORM\Table(name: 'match')]
class FootballMatch
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\SequenceGenerator(sequenceName: "match_match_id_seq")]
    #[ORM\Column(name: "match_id", type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $matchDate;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $stadium;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $scoreHome;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $scoreAway;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "team_id",referencedColumnName: "team_id", nullable: false, onDelete: "CASCADE")]
    private Team $homeTeam;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: "team_id",referencedColumnName: "team_id", nullable: false, onDelete: "CASCADE")]
    private Team $awayTeam;

    /**
     * @param DateTimeImmutable $matchDate
     * @param string $stadium
     * @param int $scoreHome
     * @param int $scoreAway
     * @param Team $homeTeam
     * @param Team $awayTeam
     */
    public function __construct(DateTimeImmutable $matchDate, string $stadium, int $scoreHome, int $scoreAway, Team $homeTeam, Team $awayTeam)
    {
        $this->matchDate = $matchDate;
        $this->stadium = $stadium;
        $this->scoreHome = $scoreHome;
        $this->scoreAway = $scoreAway;
        $this->homeTeam = $homeTeam;
        $this->awayTeam = $awayTeam;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMatchDate(): DateTimeImmutable
    {
        return $this->matchDate;
    }

    public function setMatchDate(DateTimeImmutable $matchDate): self
    {
        $this->matchDate = $matchDate;

        return $this;
    }

    public function getStadium(): string
    {
        return $this->stadium;
    }

    public function setStadium(string $stadium): self
    {
        $this->stadium = $stadium;

        return $this;
    }

    public function getScoreHome(): int
    {
        return $this->scoreHome;
    }

    public function setScoreHome(int $scoreHome): self
    {
        $this->scoreHome = $scoreHome;

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

    public function getHomeTeam(): ?Team
    {
        return $this->homeTeam;
    }

    public function setHomeTeam(Team $homeTeam): self
    {
        $this->homeTeam = $homeTeam;

        return $this;
    }

    public function getAwayTeam(): Team
    {
        return $this->awayTeam;
    }

    public function setAwayTeam(Team $awayTeam): self
    {
        $this->awayTeam = $awayTeam;

        return $this;
    }
}
