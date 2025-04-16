<?php

namespace App\Repository;

use App\Entity\FootballMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository responsible for managing matches and interacting with them
 *
 * @extends ServiceEntityRepository<FootballMatch>
 *
 * @method FootballMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method FootballMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method FootballMatch[]    findAll()
 * @method FootballMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class FootballMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FootballMatch::class);
    }

    /**
     * Finds match by its id
     *
     * @param int $id ID of match
     * @return FootballMatch|null null - not available, Team - found match
     */
    public function findById(int $id): ?FootballMatch
    {
        return $this->findOneBy(["id" => $id]);
    }
}
