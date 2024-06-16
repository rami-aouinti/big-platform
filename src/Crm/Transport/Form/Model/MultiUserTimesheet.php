<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Model;

use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Entity\Timesheet;
use App\User\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @package App\Form\Model
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[\App\Crm\Transport\Validator\Constraints\TimesheetMultiUser]
final class MultiUserTimesheet extends Timesheet
{
    /**
     * @var Collection<User>
     */
    private Collection $users;
    /**
     * @var Collection<Team>
     */
    private Collection $teams;

    public function __construct()
    {
        parent::__construct();
        $this->users = new ArrayCollection();
        $this->teams = new ArrayCollection();
    }

    /**
     * @return Collection<User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): void
    {
        $this->users->add($user);
    }

    public function removeUser(User $user): void
    {
        if ($this->users->contains($user)) {
            $this->users->remove($user);
        }
    }

    /**
     * @return Collection<Team>
     */
    public function getTeams(): Collection
    {
        return $this->teams;
    }

    public function addTeam(Team $team): void
    {
        $this->teams->add($team);
    }

    public function removeTeam(Team $team): void
    {
        if ($this->teams->contains($team)) {
            $this->teams->remove($team);
        }
    }
}
