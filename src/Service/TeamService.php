<?php

namespace App\Service;

use App\Entity\Team;
use App\Form\Request\TeamRequest;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service class for managing teams
 *
 * @package App\Service
 */
class TeamService
{
    public function __construct(
        /**
         * @var EntityManagerInterface $entityManager The entity manager for database operations
         */
        private readonly EntityManagerInterface $entityManager,
        /**
         * @var TeamRepository $teamRepository The repository for teams
         */
        private readonly TeamRepository $teamRepository,
    ) {}

    /**
     * Get team by its id
     *
     * @param int $teamId ID of team
     * @return Team|null null - not available, Team - found team
     */
    public function getTeamById(int $teamId): ?Team
    {
        return $this->teamRepository->findById($teamId);
    }

    /**
     * Get all existing teams
     *
     * @return Team[] found teams
     */
    public function getAllTeams(): array
    {
        return $this->teamRepository->findAll();
    }

    /**
     * Get count of all teams
     *
     * @return int count of all existing teams
     */
    public function getCountOfMatches(): int
    {
        return $this->teamRepository->findCountOfTeams();
    }

    /**
     * Get all IDs of matches
     *
     * @return array IDs of all teams
     */
    public function getAllIds(): array
    {
        return array_map(fn($item): int => $item["id"], $this->teamRepository->findIdsOfTeams());
    }

    /**
     * Create a new team by team request
     *
     * @param TeamRequest $request HTTP request containing information about team
     * @return Team a new created team
     */
    public function create(TeamRequest $request): Team {
        $team = new Team($request->name, $request->city, $request->founded, $request->stadium);

        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->persist($team);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }

        return $team;
    }

    /**
     * Update a team by team request
     *
     * @param TeamRequest $request HTTP request containing information about team
     * @param Team $team former team to be updated
     * @return Team updated team
     */
    public function update(TeamRequest $request, Team $team): Team {
        $team->setName($request->name);
        $team->setCity($request->city);
        $team->setFounded($request->founded);
        $team->setStadium($request->stadium);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw $e;
        }

        return $team;
    }

    /**
     * Delete a team
     *
     * @param Team $team team to be deleted
     * @return void
     */
    public function delete(Team $team): void {
        $this->entityManager->beginTransaction();
        try {
            $this->entityManager->remove($team);
            $this->entityManager->flush();
            $this->entityManager->commit();
        } catch (Throwable $e) {
            $this->entityManager->rollback();
            throw $e;
        }
    }
}