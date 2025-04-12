<?php

namespace App\Controller;

use App\Form\FootballMatchType;
use App\Formatter\FootballMatchFormatter;
use App\Service\FootballMatchService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

final class FootballMatchController extends AbstractFOSRestController
{
    public function __construct(
        private readonly FootballMatchService $footballMatchService,
        private readonly FootballMatchFormatter $footballMatchFormatter
    ) {}

    #[Rest\Get(
        path: "/matches/{matchId}",
        name: "getMatch",
        requirements: ["matchId" => "\d+"]
    )]
    public function getMatch(int $matchId): View
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $result = $this->footballMatchFormatter->format($match);
        return $this->view($result, Response::HTTP_OK);
    }

    #[Rest\Get(
        path: "/matches",
        name: "getMatches"
    )]
    public function getMatches(): View
    {
        $matches = $this->footballMatchService->getAllMatches();

        $result = $this->footballMatchFormatter->formatMany($matches);
        return $this->view($result, Response::HTTP_OK);
    }

    #[Rest\Post(
        path: "/matches",
        name: "createMatch"
    )]
    public function createMatch(Request $request): View
    {
        $form = $this->createForm(FootballMatchType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $match = $this->footballMatchService->create($formRequest);

        if ($match === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $result = $this->footballMatchFormatter->format($match);
        return $this->view($result, Response::HTTP_CREATED);
    }

    #[Rest\Put(
        path: "/matches/{matchId}",
        name: "updateMatch",
        requirements: ["matchId" => "\d+"]
    )]
    public function updateMatch(Request $request, int $matchId): View
    {
        $form = $this->createForm(FootballMatchType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $match = $this->footballMatchService->getMatchById($matchId);
        if ($match === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $newMatch = $this->footballMatchService->update($formRequest, $match);
        if ($newMatch === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        return $this->view(statusCode: Response::HTTP_NO_CONTENT);
    }

    #[Rest\Delete(
        path: "/matches/{matchId}",
        name: "deleteMatch",
        requirements: ["matchId" => "\d+"]
    )]
    public function deleteMatch(int $matchId): View
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $this->footballMatchService->delete($match);

        return $this->view(Response::HTTP_NO_CONTENT);
    }
}
