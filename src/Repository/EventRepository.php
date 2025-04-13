<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findById(int $id): ?Event
    {
        return $this->findOneBy(["id" => $id]);
    }

    public function findAllByMatchId(int $matchId): array
    {
        return $this->createQueryBuilder('e')
            ->select('e')
            ->join('e.match', 'm')
            ->where('m.id = :val')
            ->setParameter('val', $matchId)
            ->getQuery()
            ->getArrayResult();
    }

    public function findByMatchIdAndEventId(int $matchId, int $eventId): ?Event
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->join('e.match', 'm')
            ->where('m.id = :matchId')
            ->andWhere('e.id = :eventId')
            ->setParameter('matchId', $matchId)
            ->setParameter('eventId', $eventId);

        /** @var ?Event $res */
        $res = $qb->getQuery()->getOneOrNullResult(AbstractQuery::HYDRATE_OBJECT);
        return $res;
    }
}
