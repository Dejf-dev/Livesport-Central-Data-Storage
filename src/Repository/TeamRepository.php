<?php

namespace App\Repository;


use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository responsible for managing teams and interacting with them
 *
 * @extends ServiceEntityRepository<Team>
 *
 * @method Team|null find($id, $lockMode = null, $lockVersion = null)
 * @method Team|null findOneBy(array $criteria, array $orderBy = null)
 * @method Team[]    findAll()
 * @method Team[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    /**
     * Finds team by its id
     *
     * @param int $id ID of team
     * @return Team|null null - not available, Team - found team
     */
    public function findById(int $id): ?Team
    {
        return $this->findOneBy(["id" => $id]);
    }

    /**
     * Finds count of all teams
     *
     * @return int count of teams
     */
    public function findCountOfTeams(): int
    {
        return $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Finds IDs of all teams
     *
     * @return array IDs of teams
     */
    public function findIdsOfTeams(): array
    {
        return $this->createQueryBuilder('t')
            ->select('t.id')
            ->getQuery()
            ->getResult();
    }
}