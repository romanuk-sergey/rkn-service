<?php

namespace App\Command;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'admin:create',
    description: 'Creating an admin user.',
)]
class AdminCreateCommand extends Command
{
    /**
     * @param UserPasswordHasherInterface $hasher
     * @param EntityManagerInterface $manager
     */
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private EntityManagerInterface      $manager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('username', InputArgument::REQUIRED, 'User username')
            ->addArgument('password', InputArgument::REQUIRED, 'User password');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $this->createUser($username, $password);

        $io->success('A new user has been created successfully. Login - ' . $username . ', password - ' . $password . '.');

        return Command::SUCCESS;
    }

    /**
     * @param string $username
     * @param string $password
     */
    private function createUser(string $username, string $password)
    {
        $user = (new User())
            ->setUsername($username)
            ->setRoles([User::ROLE_ADMIN]);
        $hashPassword = $this->hasher->hashPassword($user, $password);

        $user->setPassword($hashPassword);

        $this->manager->persist($user);
        $this->manager->flush();
    }
}
