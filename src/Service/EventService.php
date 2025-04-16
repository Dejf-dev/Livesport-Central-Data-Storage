<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\FootballMatch;
use App\Enums\EventTypeEnum;
use App\Form\Request\EventRequest;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

/**
 * Service class for managing events
 *
 * @package App\Service
 */
class EventService
{
    public function __construct(
        /**
         * @var EntityManagerInterface $entityManager The entity manager for database operations
         */
        private readonly EntityManagerInterface $entityManager,
        /**
         * @var EventRepository $eventRepository The repository for events
         */
        private readonly EventRepository $eventRepository,
        /**
         * @var TeamService $teamService The service of teams
         */
        private readonly TeamService $teamService,
    ) {}

    /**
     * Get event by its id
     *
     * @param int $eventId ID of event
     * @return Event|null null - not available, Event - found event
     */
    public function getEventById(int $eventId): ?Event
    {
        return $this->eventRepository->findById($eventId);
    }

    /**
     * Get all existing events
     *
     * @return Event[] found events
     */
    public function getAllEvents(): array
    {
        return $this->eventRepository->findAll();
    }

    /**
     * Get all existing events under specific match
     *
     * @param int $matchId ID of match
     * @return Event[] found events under the match
     */
    public function getAllEventsByMatchId(int $matchId): array
    {
        return $this->eventRepository->findAllByMatchId($matchId);
    }

    /**
     * Get event by its id and under specific match
     *
     * @param int $matchId ID of match
     * @param int $eventId ID of event
     * @return Event|null null - not available, Event - found event under the match
     */
    public function getEventByMatchIdAndEventId(int $matchId, int $eventId): ?Event
    {
        return $this->eventRepository->findByMatchIdAndEventId($matchId, $eventId);
    }

    /**
     * Create a event by event request under some match
     *
     * @param EventRequest $request HTTP request containing information about event
     * @param FootballMatch $match match where the event belongs
     * @return Event|null null - invalid/not existing team, Event - a new created event
     */
    public function create(EventRequest $request, FootballMatch $match): ?Event {
        $team = $this->teamService->getTeamById($request->teamId);

        // invalid team
        if ($team === null) {
            return null;
        }

        $event = new Event($request->player, EventTypeEnum::from($request->eventType), $request->minute, $team, $match);

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($event);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        return $event;
    }

    /**
     * Update a event by event request under some match
     *
     * @param EventRequest $request HTTP request containing information about match
     * @param Event $event former event to be updated
     * @param FootballMatch $match match where the event belongs
     * @return FootballMatch|null null - invalid/not existing team, Event - updated event
     */
    public function update(EventRequest $request, Event $event, FootballMatch $match): ?Event {
        $team = $this->teamService->getTeamById($request->teamId);

        // invalid team
        if ($team === null) {
            return null;
        }

        $event->setPlayer($request->player);
        $event->setEventType(EventTypeEnum::from($request->eventType));
        $event->setMinute($request->minute);
        $event->setTeam($team);
        $event->setMatch($match);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw $e;
        }

        return $event;
    }

    /**
     * Delete a event
     *
     * @param Event $event event to be deleted
     * @return void
     */
    public function delete(Event $event): void {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->remove($event);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}