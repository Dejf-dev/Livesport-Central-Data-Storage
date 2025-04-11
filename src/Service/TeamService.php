<?php

namespace App\Service;

use App\Entity\Team;
use App\Form\Request\TeamRequest;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

class TeamService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamRepository $teamRepository,
    ) {}

    public function getTeamById(int $teamId): ?Team
    {
        return $this->teamRepository->findById($teamId);
    }

    public function getAllTeams(): array
    {
        return $this->teamRepository->findAll();
    }

    public function create(TeamRequest $request): ?Team {
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

    public function update(TeamRequest $request, Team $team): ?Team {
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