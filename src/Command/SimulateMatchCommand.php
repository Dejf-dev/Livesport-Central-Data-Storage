<?php

namespace App\Command;

use App\Entity\Event;
use App\Entity\FootballMatch;
use App\Entity\Team;
use App\Enums\EventTypeEnum;
use App\Service\TeamService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Command for launching simulation of match script
 *
 * @package App\Command
 */
#[AsCommand(
    name: 'app:simulate-match',
    description: 'Creates a new simulated match with new created events on that match.',
)]
class SimulateMatchCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TeamService $teamService,
    )
    {
        parent::__construct();
    }

    /**
     * Configure command options
     *
     * @return void
     */
    protected function configure(): void
    {
        $this->setHelp('This command allows you to create a match and it will generate some events to the same match')
             ->addArgument('cntEvents', InputArgument::OPTIONAL, 'Number of events', 2);
    }

    /**
     * Execute simulate-match script/command
     *
     * @param InputInterface $input input of command
     * @param OutputInterface $output output of command
     * @return int return code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cntEvents = $input->getArgument('cntEvents');
        $io = new SymfonyStyle($input, $output);

        $this->entityManager->beginTransaction();

        // getting IDs of teams
        $cntTeams = $this->teamService->getCountOfMatches();

        if ($cntTeams < 2) {
            $io->error('Not enough teams to generate matches!');
            $this->entityManager->rollback();

            return Command::INVALID;
        }

        $homeTeamId = rand(1, $cntTeams - 1);
        $awayTeamId = rand(1, $cntTeams - 1);

        // checks if both IDs are not same
        while ($homeTeamId === $awayTeamId) {
            $awayTeamId = rand(1, $cntTeams - 1);
        }

        $homeTeam = $this->teamService->getTeamById($homeTeamId);
        $awayTeam = $this->teamService->getTeamById($awayTeamId);
        $players = Player::givePlayers($homeTeam, $awayTeam);

        // generate random match date
        $startDate = strtotime("2020-01-01");
        $endDate = strtotime("2025-01-01");
        $randomTimestamp = mt_rand($startDate, $endDate);
        $randomDate = new DateTimeImmutable('@' . $randomTimestamp);

        $match = new FootballMatch($randomDate, $homeTeam->getStadium(), 0, 0, $homeTeam, $awayTeam);
        $events = $this->generateEvents($players, $cntEvents, $homeTeam, $match);

        // persisting to DB
        $this->entityManager->persist($match);
        foreach ($events as $event) {
            $this->entityManager->persist($event);
        }

        $this->entityManager->flush();
        $this->entityManager->commit();

        $io->success('You have successfully created a simulated match!');
        return Command::SUCCESS;
    }

    /**
     * Generates random events, holds still all football rules to be valid football match
     *
     * @param Player[] $players all players
     * @param int $cntEvents number of events to be generated, except the mandatory ones
     * @param Team $homeTeam team which is playing at home
     * @param FootballMatch $match match where generated events will belong to
     * @return Event[] generated events included with mandatory ones
     */
    private function generateEvents(array $players, int $cntEvents, Team $homeTeam, FootballMatch $match): array
    {
        $events = $this->generateMandatoryEvents($players, $homeTeam, $match);

        $minutes = range(0, 90);
        shuffle($minutes);
        $rndMinutes = array_slice($minutes, 0, $cntEvents);
        sort($rndMinutes);

        for ($i = 0; $i < $cntEvents;) {
            $rndIdx = rand(0, count($players) - 1);
            $eventTypes = EventTypeEnum::cases();
            $rndEvent = $eventTypes[rand(0, count($eventTypes) - 1)];
            $player = $players[$rndIdx];
            $rndMinute = $rndMinutes[$i];
            $isPlaying = $player->getExclusionMinute() > $rndMinute && $player->getMinuteOnBench() > $rndMinute;
            $formerSize = count($events);

            // yellow card
            if ($rndEvent === EventTypeEnum::YELLOW_CARD) {
                $player->incrementCntYellowCards();
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);

                // won't be playing
                if ($player->getCntYellowCards() == 2) {
                    $player->setExclusionMinute($rndMinute);
                    $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);
                }
            }

            // all types of goals
            if ($isPlaying && in_array($rndEvent, [EventTypeEnum::GOAL, EventTypeEnum::PENALTY_GOAL])) {
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);
                $player->getTeam() === $homeTeam ? $match->incrementScoreHome() : $match->incrementScoreAway();
            }

            // own goal
            if ($isPlaying && $rndEvent === EventTypeEnum::OWN_GOAL) {
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);
                $player->getTeam() === $homeTeam ? $match->incrementScoreAway() : $match->incrementScoreHome();
            }

            // penalty miss
            if ($isPlaying && $rndEvent === EventTypeEnum::PENALTY_MISS) {
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);
            }

            // red card
            if ($isPlaying && $rndEvent === EventTypeEnum::RED_CARD) {
                $player->setExclusionMinute($rndMinute);
                $player->setCntYellowCards(2);
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);
            }

            // substitution out
            if ($isPlaying && $rndEvent === EventTypeEnum::SUBSTITUTION_OUT) {
                $sub = Player::givePlayerOnTeam($players, $player->getTeam(), true);

                $player->setMinuteOnBench($rndMinute);
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);

                $sub->setMinuteOnBench(91);
                $events[] = new Event($sub->getName(), EventTypeEnum::SUBSTITUTION_IN, $rndMinute, $sub->getTeam(), $match);
            }

            // substitution in
            if ($player->getMinuteOnBench() == -1 && $player->getExclusionMinute() > $rndMinute
                && $rndEvent === EventTypeEnum::SUBSTITUTION_IN) {
                $sub = Player::givePlayerOnTeam($players, $player->getTeam(), false);

                $player->setMinuteOnBench(91);
                $events[] = new Event($player->getName(), $rndEvent, $rndMinute, $player->getTeam(), $match);

                $sub->setMinuteOnBench($rndMinute);
                $events[] = new Event($sub->getName(), EventTypeEnum::SUBSTITUTION_OUT, $rndMinute, $sub->getTeam(), $match);
            }

            // check if new event was appended
            if ($formerSize != count($events)) {
                $i++;
            }
        }

        return $events;
    }

    /**
     * Generates the mandatory stated events which simulated match should have
     *
     * @param array $players all players
     * @param Team $homeTeam team which is playing at home
     * @param FootballMatch $match match where generated events will belong to
     * @return Event[] generated mandatory events
     */
    private function generateMandatoryEvents(array $players, Team $homeTeam, FootballMatch $match): array {
        $events = [];
        $rndIdx = rand(0, 22);
        /** @var Player $player */
        $player = $players[$rndIdx];
        $rndMinute = rand(0, 90);

        // mandatory red card
        $events[] = new Event($player->getName(), EventTypeEnum::RED_CARD, $rndMinute, $player->getTeam(), $match);
        $player->setExclusionMinute($rndMinute);

        // mandatory 3 goals
        for ($i = 0; $i < 3; $i++) {
            $rndIdx = rand(0, 22);
            /** @var Player $player */
            $player = $players[$rndIdx];
            $rndMinute = rand(0, 90);

            // checks if player plays
            if ($player->getExclusionMinute() > $rndMinute && $player->getMinuteOnBench() > $rndMinute) {
                $events[] = new Event($player->getName(), EventTypeEnum::GOAL, $rndMinute, $player->getTeam(), $match);
                $player->getTeam() === $homeTeam ? $match->incrementScoreAway() : $match->incrementScoreHome();
            }
        }
        // mandatory yellow card
        $rndIdx = rand(0, 22);
        /** @var Player $player */
        $player = $players[$rndIdx];
        $rndMinute = rand(0, 90);

        // checks if player is not already excluded
        if ($player->getExclusionMinute() > $rndMinute) {
            $events[] = new Event($player->getName(), EventTypeEnum::YELLOW_CARD, $rndMinute, $player->getTeam(), $match);
        }

        return $events;
    }
}
