<?php

namespace App\Controller;

use App\Form\FootballMatchType;
use App\Formatter\FootballMatchFormatter;
use App\Service\FootballMatchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/matches')]
final class FootballMatchController extends AbstractController
{
    public function __construct(
        private readonly FootballMatchService $footballMatchService,
        private readonly FootballMatchFormatter $footballMatchFormatter
    ) {}

    #[Route(path: '/{matchId}', name: 'getMatch', requirements: ['matchId' => '\d+'], methods: ['GET'])]
    public function getMatch(int $matchId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $result = $this->footballMatchFormatter->format($match);
        return $this->json($result, Response::HTTP_OK);
    }

    #[Route(path: '', name: 'getMatches', methods: ['GET'])]
    public function getMatches(): JsonResponse
    {
        $matches = $this->footballMatchService->getAllMatches();
        $result = $this->footballMatchFormatter->formatMany($matches);

        return $this->json($result, Response::HTTP_OK);
    }

    #[Route(path: '', name: 'createMatch', methods: ['POST'])]
    public function createMatch(Request $request): JsonResponse
    {
        $form = $this->createForm(FootballMatchType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $match = $this->footballMatchService->create($formRequest);

        if ($match === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $result = $this->footballMatchFormatter->format($match);
        return $this->json($result, Response::HTTP_CREATED);
    }

    #[Route(path: '/{matchId}', name: 'updateMatch', requirements: ['matchId' => '\d+'], methods: ['PUT'])]
    public function updateMatch(Request $request, int $matchId): JsonResponse
    {
        $form = $this->createForm(FootballMatchType::class);
        $form->submit($request->request->all());

        if (!$form->isValid()) {
            return $this->json(['errors' => (string) $form->getErrors(true, false)], Response::HTTP_BAD_REQUEST);
        }

        $formRequest = $form->getData();
        $match = $this->footballMatchService->getMatchById($matchId);
        if ($match === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $newMatch = $this->footballMatchService->update($formRequest, $match);
        if ($newMatch === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

    #[Route(path: '/{matchId}', name: 'deleteMatch', requirements: ['matchId' => '\d+'], methods: ['DELETE'])]
    public function deleteMatch(int $matchId): JsonResponse
    {
        $match = $this->footballMatchService->getMatchById($matchId);

        if ($match === null) {
            return $this->json(null, Response::HTTP_NOT_FOUND);
        }

        $this->footballMatchService->delete($match);

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
