<?php

namespace App\Service;

use App\Entity\Event;
use App\Entity\FootballMatch;
use App\Enums\EventTypeEnum;
use App\Form\Request\EventRequest;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class EventService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly EventRepository $eventRepository,
        private readonly TeamService $teamService,
    ) {}

    public function getEventById(int $eventId): ?Event
    {
        return $this->eventRepository->findById($eventId);
    }

    public function getAllEvents(): array
    {
        return $this->eventRepository->findAll();
    }

    public function getAllEventsByMatchId(int $matchId): array
    {
        return $this->eventRepository->findAllByMatchId($matchId);
    }

    public function getEventByMatchIdAndEventId(int $matchId, int $eventId): ?Event
    {
        return $this->eventRepository->findByMatchIdAndEventId($matchId, $eventId);
    }

    public function create(EventRequest $request, FootballMatch $match): ?Event {
        $team = $this->teamService->getTeamById($request->teamId);

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

    public function update(EventRequest $request, Event $event, FootballMatch $match): ?Event {
        $team = $this->teamService->getTeamById($request->teamId);

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