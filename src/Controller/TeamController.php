<?php

namespace App\Controller;

use App\Constants\ErrorMessages;
use App\Form\TeamType;
use App\Formatter\TeamFormatter;
use App\Service\TeamService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for teams
 *
 * @package App\Controller
 */
#[Route('/teams')]
final class TeamController extends AbstractController
{
    public function __construct(
        private readonly TeamService $teamService,
        private readonly TeamFormatter $teamFormatter
    ) {}

    /**
     * Get team by team ID
     *
     * @param int $teamId ID of team
     * @return JsonResponse the response
     */
    #[Route(path: '/{teamId}', name: 'getTeam', requirements: ['teamId' => '\d+'], methods: ['GET'])]
    public function getTeam(int $teamId): JsonResponse
    {
        $team = $this->teamService->getTeamById($teamId);

        if ($team === null) {
            return $this->json(['errors' => ErrorMessages::TEAM_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $result = $this->teamFormatter->format($team);
        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * Get all teams
     *
     * @return JsonResponse the response
     */
    #[Route(path: '', name: 'getTeams', methods: ['GET'])]
    public function getTeams(): JsonResponse
    {
        $teams = $this->teamService->getAllTeams();

        $result = $this->teamFormatter->formatMany($teams);
        return $this->json($result, Response::HTTP_OK);
    }

    /**
     * Create a new team
     *
     * @param Request $request the HTTP request
     * @return JsonResponse the response
     */
    #[Route(path: '', name: 'createTeam', methods: ['POST'])]
    public function createTeam(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(TeamType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $team = $this->teamService->create($formRequest);

        $result = $this->teamFormatter->format($team);
        return $this->json($result, Response::HTTP_CREATED);
    }

    /**
     * Update team
     *
     * @param Request $request the HTTP request
     * @param int $teamId ID of team
     * @return JsonResponse the response
     */
    #[Route(path: '/{teamId}', name: 'updateTeam', requirements: ['teamId' => '\d+'], methods: ['PUT'])]
    public function updateTeam(Request $request, int $teamId): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(TeamType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $team = $this->teamService->getTeamById($teamId);
        if ($team === null) {
            return $this->json(['errors' => ErrorMessages::TEAM_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $this->teamService->update($formRequest, $team);

        return $this->json(new \stdClass(), Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete a team
     *
     * @param int $teamId ID of team
     * @return JsonResponse The response
     */
    #[Route(path: '/{teamId}', name: 'deleteTeam', requirements: ['teamId' => '\d+'], methods: ['DELETE'])]
    public function deleteTeam(int $teamId): JsonResponse
    {
        $team = $this->teamService->getTeamById($teamId);

        if ($team === null) {
            return $this->json(['errors' => ErrorMessages::TEAM_NOT_FOUND], Response::HTTP_NOT_FOUND);
        }

        $this->teamService->delete($team);
        return $this->json(new \stdClass(), Response::HTTP_NO_CONTENT);
    }
}
