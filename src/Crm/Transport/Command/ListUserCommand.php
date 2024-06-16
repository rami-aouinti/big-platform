<?php

declare(strict_types=1);

namespace App\Crm\Transport\Command;

use App\Crm\Domain\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'kimai:user:list', description: 'List all users')]
final class ListUserCommand extends Command
{
    public function __construct(
        private UserRepository $repository
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output = new SymfonyStyle($input, $output);
        $users = $this->repository->findAll();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                $user->getUserIdentifier(),
                $user->getEmail(),
                implode(', ', $user->getRoles()),
                $user->isEnabled() ? 'X' : '',
                $user->getPasswordRequestedAt()?->format('Y-m-d H:i:s'),
            ];
        }

        $header = ['Username', 'Email', 'Roles', 'Active', 'PW Reset'];

        $output->table($header, $data);

        return Command::SUCCESS;
    }
}
