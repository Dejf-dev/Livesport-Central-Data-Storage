<?php

namespace App\Controller;

use App\Form\EventType;
use App\Formatter\EventFormatter;
use App\Service\EventService;
use App\Service\FootballMatchService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

final class EventController extends AbstractFOSRestController
{


    public function __construct(
        private EventService $eventService,
        private EventFormatter $eventFormatter,
        private FootballMatchService $footballMatchService
    ) {}

    #[Rest\Get(
        path: "/matches/{matchId}/events/{eventId}",
        name: "getEventOfMatch",
        requirements: ["matchId" => "\d+", "eventId" => "\d+"]
    )]
    public function getEventOfMatch(int $matchId, int $eventId): View
    {
        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventById($eventId);

        if ($match === null || $event === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $result = $this->eventFormatter->format($event);
        return $this->view($result, Response::HTTP_OK);
    }

    #[Rest\Get(
        path: "/matches/{matchId}/events",
        name: "getEventsOfMatch",
        requirements: ["matchId" => "\d+"]
    )]
    public function getEventsOfMatch(int $matchId): View
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $events = $this->eventService->getAllEvents();

        $result = $this->eventFormatter->formatMany($events);
        return $this->view($result, Response::HTTP_OK);
    }

    #[Rest\Post(
        path: "/matches/{matchId}/events",
        name: "createEventToMatch",
        requirements: ["matchId" => "\d+"]
    )]
    public function createEventToMatch(Request $request, int $matchId): View
    {
        $form = $this->createForm(EventType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $match = $this->footballMatchService->getMatchById($matchId);
        if ($match === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $formRequest = $form->getData();
        $event = $this->eventService->create($formRequest, $match);
        if ($event === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $result = $this->eventFormatter->format($event);
        return $this->view($result, Response::HTTP_CREATED);
    }

    #[Rest\Put(
        path: "/matches/{matchId}/events/{eventId}",
        name: "updateEventOfMatch",
        requirements: ["matchId" => "\d+", "eventId" => "\d+"]
    )]
    public function updateEventOfMatch(Request $request, int $matchId, int $eventId): View
    {
        $form = $this->createForm(EventType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventById($eventId);
        if ($match === null || $event === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $formRequest = $form->getData();
        $newEvent = $this->eventService->update($formRequest, $event, $match);

        if ($newEvent === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        return $this->view(statusCode: Response::HTTP_NO_CONTENT);
    }

    #[Rest\Delete(
        path: "/matches/{matchId}/events/{eventId}",
        name: "deleteEventOfMatch",
        requirements: ["matchId" => "\d+", "eventId" => "\d+"]
    )]
    public function deleteEventOfMatch(int $matchId, int $eventId): View
    {
        $match = $this->footballMatchService->getMatchById($matchId);
        $event = $this->eventService->getEventById($eventId);

        if ($match === null || $event === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $this->eventService->delete($event);

        return $this->view(Response::HTTP_NO_CONTENT);
    }
}
