<?php

namespace App\Service;

use App\Constants\Constants;
use App\Entity\FootballMatch;
use App\Form\Request\FootballMatchRequest;
use App\Repository\FootballMatchRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class FootballMatchService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly FootballMatchRepository $footballMatchRepository,
        private readonly TeamService $teamService,
    ) {}

    public function getMatchById(int $matchId): ?FootballMatch
    {
        return $this->footballMatchRepository->findById($matchId);
    }

    public function getAllMatches(): array
    {
        return $this->footballMatchRepository->findAll();
    }

    public function create(FootballMatchRequest $request): ?FootballMatch {
        $homeTeam = $this->teamService->getTeamById($request->homeTeamId);
        $awayTeam = $this->teamService->getTeamById($request->awayTeamId);

        if ($homeTeam === null || $awayTeam === null) {
            return null;
        }

        $match = new FootballMatch(DateTimeImmutable::createFromFormat(Constants::MATCH_DATE_FORMAT, $request->matchDate),
                                   $request->stadium, $request->scoreHome, $request->scoreAway, $homeTeam, $awayTeam);

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($match);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        return $match;
    }

    public function update(FootballMatchRequest $request, FootballMatch $match): ?FootballMatch {
        $homeTeam = $this->teamService->getTeamById($request->homeTeamId);
        $awayTeam = $this->teamService->getTeamById($request->awayTeamId);

        if ($homeTeam === null || $awayTeam === null) {
            return null;
        }

        $match->setMatchDate(DateTimeImmutable::createFromFormat(Constants::MATCH_DATE_FORMAT, $request->matchDate));
        $match->setStadium($request->stadium);
        $match->setScoreHome($request->scoreHome);
        $match->setScoreAway($request->scoreAway);
        $match->setHomeTeam($homeTeam);
        $match->setAwayTeam($awayTeam);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw $e;
        }

        return $match;
    }

    public function delete(FootballMatch $match): void {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->remove($match);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}