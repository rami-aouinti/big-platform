<?php

declare(strict_types=1);

namespace App\User;

use App\Crm\Domain\Repository\TeamRepository;

final class TeamService
{
    /**
     * @var array<string, int>
     */
    private array $cache = [];

    public function __construct(
        private TeamRepository $repository
    ) {
    }

    public function countTeams(): int
    {
        if (!\array_key_exists('count', $this->cache)) {
            $this->cache['count'] = $this->repository->count([]);
        }

        return $this->cache['count'];
    }

    public function hasTeams(): bool
    {
        return $this->countTeams() > 0;
    }
}
