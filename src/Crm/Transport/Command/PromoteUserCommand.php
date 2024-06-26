<?php

declare(strict_types=1);

namespace App\Crm\Transport\Command;

use App\User\Domain\Entity\User;
use App\User\UserService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'kimai:user:promote')]
final class PromoteUserCommand extends AbstractRoleCommand
{
    protected function configure(): void
    {
        parent::configure();

        $this
            ->setDescription('Promotes a user by adding a role')
            ->setHelp(
                <<<'EOT'
                    The <info>kimai:user:promote</info> command promotes a user by adding a role

                      <info>php %command.full_name% susan_super ROLE_TEAMLEAD</info>
                      <info>php %command.full_name% --super susan_super</info>
                    EOT
            );
    }

    protected function executeRoleCommand(UserService $manipulator, SymfonyStyle $output, User $user, bool $super, $role): void
    {
        $username = $user->getUserIdentifier();
        if ($super) {
            if (!$user->isSuperAdmin()) {
                $user->setSuperAdmin(true);
                $manipulator->saveUser($user);
                $output->success(sprintf('User "%s" has been promoted as a super administrator.', $username));
            } else {
                $output->warning(sprintf('User "%s" does already have the super administrator role.', $username));
            }
        } else {
            if (!$user->hasRole($role)) {
                $user->addRole($role);
                $manipulator->saveUser($user);
                $output->success(sprintf('Role "%s" has been added to user "%s".', $role, $username));
            } else {
                $output->warning(sprintf('User "%s" did already have "%s" role.', $username, $role));
            }
        }
    }
}
