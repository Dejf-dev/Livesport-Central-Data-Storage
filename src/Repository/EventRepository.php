<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository responsible for managing events and interacting with them
 *
 * @extends ServiceEntityRepository<Event>
 *
 * @method Event|null find($id, $lockMode = null, $lockVersion = null)
 * @method Event|null findOneBy(array $criteria, array $orderBy = null)
 * @method Event[]    findAll()
 * @method Event[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @package App\Repository
 */
class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    /**
     * Finds event by id
     *
     * @param int $id ID of event
     * @return Event|null null - not available, Team - found event
     */
    public function findById(int $id): ?Event
    {
        return $this->findOneBy(["id" => $id]);
    }

    /**
     * Finds all events by match ID
     *
     * @param int $matchId ID of match
     * @return Event[] all events under specific match
     */
    public function findAllByMatchId(int $matchId): array
    {
        $qb = $this->createQueryBuilder('e')
            ->select('e')
            ->join('e.match', 'm')
            ->where('m.id = :val')
            ->setParameter('val', $matchId);

        /** @var Event[] $res */
        $res = $qb->getQuery()->getResult();
        return $res;
    }

    /**
     * Finds event by match ID and event ID
     *
     * @param int $matchId ID of match
     * @param int $eventId ID of event
     * @return Event|null null - not available, Event - event under specific match
     */
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
