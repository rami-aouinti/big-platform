<?php

declare(strict_types=1);

namespace App\Crm\Transport\API\Export;

use App\Crm\Application\Utils\FileHelper;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use App\User\Domain\Entity\User;

use function count;

/**
 * @package App\Crm\Transport\API\Export
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ExportFilename
{
    private ?string $filename = null;
    private ?Customer $customer = null;
    private ?Project $project = null;
    private ?User $user = null;

    public function __construct(TimesheetQuery $query)
    {
        $customers = $query->getCustomers();
        if (count($customers) === 1) {
            $this->customer = $customers[0];
        }

        $projects = $query->getProjects();
        if (count($projects) === 1) {
            $this->project = $projects[0];
        }

        $users = $query->getUsers();
        if (count($users) === 1) {
            $this->user = $users[0];
        }
    }

    public function __toString(): string
    {
        return $this->getFilename();
    }

    public function getFilename(): string
    {
        if ($this->filename === null) {
            $filename = date('Ymd');
            $hasName = false;

            if ($this->customer !== null) {
                $filename .= '-' . $this->convert($this->getCustomerName($this->customer));
                $hasName = true;
            }

            if ($this->project !== null) {
                if (!$hasName) {
                    $filename .= '-' . $this->convert($this->getCustomerName($this->project->getCustomer()));
                }
                $filename .= '-' . $this->convert($this->project->getName());
                $hasName = true;
            }

            if ($this->user !== null) {
                $filename .= '-' . $this->convert($this->user->getDisplayName());
                $hasName = true;
            }

            if (!$hasName) {
                $filename .= '-kimai-export';
            }

            $filename = str_replace(['/', '\\'], '-', $filename);

            $this->filename = $filename;
        }

        return $this->filename;
    }

    private function getCustomerName(Customer $customer): string
    {
        $company = $customer->getCompany();
        if (empty($company)) {
            $company = $customer->getName();
        }

        return $company;
    }

    private function convert(string $filename): string
    {
        return FileHelper::convertToAsciiFilename($filename);
    }
}
