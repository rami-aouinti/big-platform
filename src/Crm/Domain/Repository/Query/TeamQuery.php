<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\Project;
use App\User\Domain\Entity\User;

use function count;

/**
 * @package App\Crm\Domain\Repository\Query
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class TeamQuery extends BaseQuery
{
    public const array TEAM_ORDER_ALLOWED = ['name'];

    /**
     * @var User[]
     */
    private array $users = [];
    /**
     * @var array<Customer>
     */
    private array $customers = [];
    /**
     * @var array<Project>
     */
    private array $projects = [];
    /**
     * @var array<Activity>
     */
    private array $activities = [];

    public function __construct()
    {
        $this->setDefaults([
            'orderBy' => 'name',
            'users' => [],
            'customers' => [],
            'projects' => [],
            'activities' => [],
        ]);
    }

    public function hasUsers(): bool
    {
        return !empty($this->users);
    }

    public function addUser(User $user): void
    {
        $this->users[$user->getId()] = $user;
    }

    public function removeUser(User $user): void
    {
        if (isset($this->users[$user->getId()])) {
            unset($this->users[$user->getId()]);
        }
    }

    /**
     * @return User[]
     */
    public function getUsers(): array
    {
        return array_values($this->users);
    }

    public function hasCustomers(): bool
    {
        return count($this->customers) > 0;
    }

    /**
     * @return Customer[]
     */
    public function getCustomers(): array
    {
        return $this->customers;
    }

    /**
     * @param array<Customer> $customers
     */
    public function setCustomers(array $customers): void
    {
        $this->customers = $customers;
    }

    public function addCustomer(Customer $customer): void
    {
        $this->customers[] = $customer;
    }

    public function hasProjects(): bool
    {
        return count($this->projects) > 0;
    }

    /**
     * @return Project[]
     */
    public function getProjects(): array
    {
        return $this->projects;
    }

    /**
     * @param array<Project> $projects
     */
    public function setProjects(array $projects): void
    {
        $this->projects = $projects;
    }

    public function addProject(Project $project): void
    {
        $this->projects[] = $project;
    }

    public function hasActivities(): bool
    {
        return count($this->activities) > 0;
    }

    /**
     * @return Activity[]
     */
    public function getActivities(): array
    {
        return $this->activities;
    }

    /**
     * @param array<Activity> $activities
     */
    public function setActivities(array $activities): void
    {
        $this->activities = $activities;
    }

    public function addActivity(Activity $activity): void
    {
        $this->activities[] = $activity;
    }
}
