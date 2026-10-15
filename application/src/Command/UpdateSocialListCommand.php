<?php

namespace App\Command;

use App\Event\CommandGetSocialListEvent;
use App\Services\RknSocialListService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsCommand(
    name: 'social-list:actualize',
    description: 'Actualize social list.',
)]
class UpdateSocialListCommand extends Command
{
    /**
     * @param RknSocialListService $rknSocialListService
     */
    public function __construct(
        private RknSocialListService     $rknSocialListService,
        private EventDispatcherInterface $eventDispatcher,
    )
    {
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->eventDispatcher->dispatch(new CommandGetSocialListEvent());

        $io = new SymfonyStyle($input, $output);

        try {
            $this->rknSocialListService->syncSocialList();
            $io->success('Start update social list');
        } catch (Throwable $exception) {
            $io->error('Error: ' . $exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
