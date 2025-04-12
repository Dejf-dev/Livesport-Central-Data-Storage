<?php

namespace App\Controller;

use App\Form\TeamType;
use App\Formatter\TeamFormatter;
use App\Service\TeamService;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;


final class TeamController extends AbstractFOSRestController
{
    public function __construct(
        private readonly TeamService $teamService,
        private readonly TeamFormatter $teamFormatter
    ) {}

    #[Rest\Get(
        path: "/teams/{teamId}",
        name: "getTeam",
        requirements: ["teamId" => "\d+"]
    )]
    public function getTeam(int $teamId): View
    {
        $team = $this->teamService->getTeamById($teamId);

        if ($team === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $result = $this->teamFormatter->format($team);
        return $this->view($result, Response::HTTP_OK);
    }

    #[Rest\Get(
        path: "/teams",
        name: "getTeams"
    )]
    public function getTeams(): View
    {
        $teams = $this->teamService->getAllTeams();

        $result = $this->teamFormatter->formatMany($teams);
        return $this->view($result, Response::HTTP_OK);
    }

    #[Rest\Post(
        path: "/teams",
        name: "createTeam"
    )]
    public function createTeam(Request $request): View
    {
        $form = $this->createForm(TeamType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $team = $this->teamService->create($formRequest);

        $result = $this->teamFormatter->format($team);
        return $this->view($result, Response::HTTP_CREATED);
    }

    #[Rest\Put(
        path: "/teams/{teamId}",
        name: "updateTeam",
        requirements: ["teamId" => "\d+"]
    )]
    public function updateTeam(Request $request, int $teamId): View
    {
        $form = $this->createForm(TeamType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->view($form->getErrors(), Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $team = $this->teamService->getTeamById($teamId);
        if ($team === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $this->teamService->update($formRequest, $team);

        return $this->view(statusCode: Response::HTTP_NO_CONTENT);
    }

    #[Rest\Delete(
        path: "/teams/{teamId}",
        name: "deleteTeam",
        requirements: ["teamId" => "\d+"]
    )]
    public function deleteTeam($teamId): View
    {
        $team = $this->teamService->getTeamById($teamId);

        if ($team === null) {
            return $this->view(statusCode: Response::HTTP_NOT_FOUND);
        }

        $this->teamService->delete($team);

        return $this->view(Response::HTTP_NO_CONTENT);
    }
}
