<?php

namespace App\Service;

use App\Constants\Constants;
use App\Entity\FootballMatch;
use App\Form\Request\FootballMatchRequest;
use App\Repository\FootballMatchRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service class for managing matches
 *
 * @package App\Service
 */
class FootballMatchService
{
    public function __construct(
        /**
         * @var EntityManagerInterface $entityManager The entity manager for database operations
         */
        private readonly EntityManagerInterface $entityManager,
        /**
         * @var FootballMatchRepository $footballMatchRepository The repository for matches
         */
        private readonly FootballMatchRepository $footballMatchRepository,
        /**
         * @var TeamService $teamService The service of teams
         */
        private readonly TeamService $teamService,
    ) {}

    /**
     * Get match by its id
     *
     * @param int $matchId ID of match
     * @return FootballMatch|null null - not available, FootballMatch - found match
     */
    public function getMatchById(int $matchId): ?FootballMatch
    {
        return $this->footballMatchRepository->findById($matchId);
    }

    /**
     * Get all existing matches
     *
     * @return FootballMatch[] found matches
     */
    public function getAllMatches(): array
    {
        return $this->footballMatchRepository->findAll();
    }

    /**
     * Create a match by match request
     *
     * @param FootballMatchRequest $request HTTP request containing information about match
     * @return FootballMatch|null null - invalid/not existing teams, FootballMatch - a new created match
     */
    public function create(FootballMatchRequest $request): ?FootballMatch {
        $homeTeam = $this->teamService->getTeamById($request->homeTeamId);
        $awayTeam = $this->teamService->getTeamById($request->awayTeamId);

        // invalid teams
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

    /**
     * Update a match by match request
     *
     * @param FootballMatchRequest $request HTTP request containing information about match
     * @param FootballMatch $match former match to be updated
     * @return FootballMatch|null null - invalid/not existing teams, FootballMatch - updated match
     */
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

    /**
     * Delete a match
     *
     * @param FootballMatch $match match to be deleted
     * @return void
     */
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