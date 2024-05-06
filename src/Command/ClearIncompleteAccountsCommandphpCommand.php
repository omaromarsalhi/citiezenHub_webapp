<?php

namespace App\Command;

use App\Entity\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'ClearIncompleteAccountsCommandphp',
    description: 'Add a short description for your command',
)]
class ClearIncompleteAccountsCommandphpCommand extends Command
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function configure(): void
    {
        $this
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $incompleteUsers = $this->entityManager->getRepository(User::class)->findIncompleteUsers();
        $currentTime = new DateTime();
        foreach ($incompleteUsers as $user) {
            $userCreationDate = $user->getDate();
            $interval = $currentTime->diff($userCreationDate);
            $minutesDifference = $interval->i + ($interval->h * 60);
            if ($minutesDifference >=5) {

                $this->entityManager->remove($user);
            }
        }

        $this->entityManager->flush();
        $output->writeln('Incomplete accounts cleared successfully.');
        return Command::SUCCESS;
    }
}
