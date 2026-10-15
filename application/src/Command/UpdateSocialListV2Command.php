<?php

namespace App\Command;

use App\Event\CommandGetSocialListEvent;
use App\Services\RknSocialListServiceV2;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsCommand(
    name: 'social-list:actualize-v2',
    description: 'Actualize social list v2.',
)]
class UpdateSocialListV2Command extends Command
{
    /**
     * @param RknSocialListServiceV2 $rknSocialListService
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        private RknSocialListServiceV2     $rknSocialListService,
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
            $io->success('Start update social list v2');
        } catch (Throwable $exception) {
            $io->error('Error: ' . $exception->getMessage());
        }

        return Command::SUCCESS;
    }
}
