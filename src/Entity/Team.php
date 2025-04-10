<?php

namespace App\Entity;

use App\Repository\TeamRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;


#[ORM\Entity(repositoryClass: TeamRepository::class)]
#[ORM\Table(name: 'team')]
class Team
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\SequenceGenerator(sequenceName: "team_team_id_seq")]
    #[ORM\Column(name: "team_id", type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $city;

    #[ORM\Column(type: Types::SMALLINT)]
    private int $founded;

    #[ORM\Column(type: Types::STRING, length: 100)]
    private string $stadium;

    /**
     * @param string $name
     * @param string $city
     * @param int $founded
     * @param string $stadium
     */
    public function __construct(string $name, string $city, int $founded, string $stadium)
    {
        $this->name = $name;
        $this->city = $city;
        $this->founded = $founded;
        $this->stadium = $stadium;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getFounded(): int
    {
        return $this->founded;
    }

    public function setFounded(int $founded): void
    {
        $this->founded = $founded;
    }

    public function getStadium(): string
    {
        return $this->stadium;
    }

    public function setStadium(string $stadium): void
    {
        $this->stadium = $stadium;
    }
}