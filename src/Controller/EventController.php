<?php

namespace App\Controller;

use App\Constants\ErrorMessages;
use App\Entity\Event;
use App\Form\EventType;
use App\Form\Request\EventRequest;
use App\Formatter\EventFormatter;
use App\Service\EventService;
use App\Service\FootballMatchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for events
 *
 * @package App\Controller
 */
#[Route(path: '/matches/{matchId}/events', requirements: ['matchId' => '\d+'])]
final class EventController extends AbstractController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly EventFormatter $eventFormatter,
        private readonly FootballMatchService $footballMatchService
    ) {}

    /**
     * Get event of specific match
     *
     * @param int $matchId ID of match
     * @param int $eventId ID of event
     * @return JsonResponse the response
     */
    #[Route(path: '/{eventId}', name: 'getEventOfMatch', requirements: ['eventId' => '\d+'], methods: ['GET'])]
    public function getEventOfMatch(int $matchId, int $eventId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventByMatchIdAndEventId($matchId, $eventId);

        if ($match === null || $event === null) {
            $errMsg = ['errors' => $match === null ? ErrorMessages::MATCH_NOT_FOUND : ErrorMessages::EVENT_NOT_FOUND];

            return $this->json($errMsg, Response::HTTP_NOT_FOUND);
        }

        $result = $this->eventFormatter->format($event);
        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * Get all events of specific match
     *
     * @param int $matchId ID of match
     * @return JsonResponse the response
     */
    #[Route(path: '', name: 'getEventsOfMatch', methods: ['GET'])]
    public function getEventsOfMatch(int $matchId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->json(['errors' => ErrorMessages::MATCH_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        /** @var Event[] $events */
        $events = $this->eventService->getAllEventsByMatchId($matchId);

        $result = $this->eventFormatter->formatMany($events);
        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * Create a new event of specific match
     *
     * @param Request $request the HTTP request
     * @param int $matchId ID of match
     * @return JsonResponse the response
     */
    #[Route(path: '', name: 'createEventToMatch', methods: ['POST'])]
    public function createEventToMatch(Request $request, int $matchId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(EventType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $match = $this->footballMatchService->getMatchById($matchId);
        if ($match === null) {
            return $this->json(['errors' => ErrorMessages::MATCH_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        /** @var EventRequest $formRequest */
        $formRequest = $form->getData();

        // team in event not occurred in teams in match
        if ($match->getHomeTeam()->getId() !== $formRequest->teamId
            && $match->getAwayTeam()->getId() !== $formRequest->teamId) {
            return $this->json(['errors' => ErrorMessages::EVENT_TEAM_NOT_RELATED], Response::HTTP_BAD_REQUEST);
        }

        $event = $this->eventService->create($formRequest, $match);
        if ($event === null) {
            return $this->json(['errors' => ErrorMessages::TEAM_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $result = $this->eventFormatter->format($event);
        return $this->json($result, Response::HTTP_CREATED);
    }

    /**
     * Update event of specific match
     *
     * @param Request $request the HTTP request
     * @param int $matchId ID of match
     * @param int $eventId ID of event
     * @return JsonResponse the response
     */
    #[Route(path: '/{eventId}', name: 'updateEventOfMatch', requirements: ['eventId' => '\d+'], methods: ['PUT'])]
    public function updateEventOfMatch(Request $request, int $matchId, int $eventId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(EventType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventByMatchIdAndEventId($matchId, $eventId);
        if ($match === null || $event === null) {
            $errMsg = ['errors' => $match === null ? ErrorMessages::MATCH_NOT_FOUND : ErrorMessages::EVENT_NOT_FOUND];

            return $this->json($errMsg, Response::HTTP_NOT_FOUND);
        }

        $formRequest = $form->getData();

        // team in event not occurred in teams in match
        if ($match->getHomeTeam()->getId() !== $formRequest->teamId
            && $match->getAwayTeam()->getId() !== $formRequest->teamId) {
            return $this->json(['errors' => ErrorMessages::EVENT_TEAM_NOT_RELATED], Response::HTTP_BAD_REQUEST);
        }

        $newEvent = $this->eventService->update($formRequest, $event, $match);

        if ($newEvent === null) {
            return $this->json(['errors' => ErrorMessages::TEAM_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        return $this->json(new \stdClass(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete event of some specific match
     *
     * @param int $matchId ID of match
     * @param int $eventId ID of event
     * @return JsonResponse the response
     */
    #[Route(path: '/{eventId}', name: 'deleteEventOfMatch', requirements: ['eventId' => '\d+'], methods: ['DELETE'])]
    public function deleteEventOfMatch(int $matchId, int $eventId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventByMatchIdAndEventId($matchId, $eventId);

        if ($match === null || $event === null) {
            $errMsg = ['errors' => $match === null ? ErrorMessages::MATCH_NOT_FOUND : ErrorMessages::EVENT_NOT_FOUND];

            return $this->json($errMsg, Response::HTTP_NOT_FOUND);
        }

        $this->eventService->delete($event);

        return $this->json(new \stdClass(), Response::HTTP_NO_CONTENT);
    }
}
