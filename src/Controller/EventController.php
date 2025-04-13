<?php

namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
use App\Formatter\EventFormatter;
use App\Service\EventService;
use App\Service\FootballMatchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/matches/{matchId}/events', requirements: ['matchId' => '\d+'])]
final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly EventFormatter $eventFormatter,
        private readonly FootballMatchService $footballMatchService
    ) {}

    #[Route(path: '/{eventId}', name: 'getEventOfMatch', requirements: ['eventId' => '\d+'], methods: ['GET'])]
    public function getEventOfMatch(int $matchId, int $eventId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventByMatchIdAndEventId($matchId, $eventId);

        if ($match === null || $event === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $result = $this->eventFormatter->format($event);
        return $this->json($result, Response::HTTP_OK);
    }

    #[Route(path: '', name: 'getEventsOfMatch', methods: ['GET'])]
    public function getEventsOfMatch(int $matchId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        /** @var Event[] $events */
        $events = $this->eventService->getAllEventsByMatchId($matchId);

        $result = $this->eventFormatter->formatMany($events);
        return $this->json($result, Response::HTTP_OK);
    }

    #[Route(path: '', name: 'createEventToMatch', methods: ['POST'])]
    public function createEventToMatch(Request $request, int $matchId): JsonResponse
    {
        $form = $this->createForm(EventType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $match = $this->footballMatchService->getMatchById($matchId);
        if ($match === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $formRequest = $form->getData();
        $event = $this->eventService->create($formRequest, $match);
        if ($event === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $result = $this->eventFormatter->format($event);
        return $this->json($result, Response::HTTP_CREATED);
    }

    #[Route(path: '/{eventId}', name: 'updateEventOfMatch', requirements: ['eventId' => '\d+'], methods: ['PUT'])]
    public function updateEventOfMatch(Request $request, int $matchId, int $eventId): JsonResponse
    {
        $form = $this->createForm(EventType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventByMatchIdAndEventId($matchId, $eventId);
        if ($match === null || $event === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $formRequest = $form->getData();
        $newEvent = $this->eventService->update($formRequest, $event, $match);

        if ($newEvent === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/{eventId}', name: 'deleteEventOfMatch', requirements: ['eventId' => '\d+'], methods: ['DELETE'])]
    public function deleteEventOfMatch(int $matchId, int $eventId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventByMatchIdAndEventId($matchId, $eventId);

        if ($match === null || $event === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $this->eventService->delete($event);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
