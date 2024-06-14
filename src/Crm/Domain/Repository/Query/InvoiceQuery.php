<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

use App\Crm\Application\Service\Invoice\Activity;
use App\Crm\Application\Service\Invoice\Customer;
use App\Crm\Application\Service\Invoice\InvoiceTemplate;
use App\Crm\Application\Service\Invoice\Project;

/**
 * Find items (e.g. timesheets) for creating a new invoice.
 */
class InvoiceQuery extends TimesheetQuery
{
    private ?InvoiceTemplate $template = null;
    private ?\DateTime $invoiceDate = null;
    private bool $allowTemplateOverwrite = true;

    public function __construct()
    {
        parent::__construct();
        $this->setDefaults([
            'order' => self::ORDER_ASC,
            'exported' => self::STATE_NOT_EXPORTED,
            'state' => self::STATE_STOPPED,
            'billable' => true,
            'invoiceDate' => null,
        ]);
    }

    public function getTemplate(): ?InvoiceTemplate
    {
        return $this->template;
    }

    public function setTemplate(InvoiceTemplate $template): self
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Helper method, because many templates access {{ model.query.customer }} directly.
     */
    public function getCustomer(): ?Customer
    {
        $customers = $this->getCustomers();
        if (\count($customers) === 1) {
            return $customers[0];
        }

        return null;
    }

    /**
     * Helper method, because many templates access {{ model.query.project }} directly.
     */
    public function getProject(): ?Project
    {
        $projects = $this->getProjects();
        if (\count($projects) === 1) {
            return $projects[0];
        }

        return null;
    }

    /**
     * Helper method, because many templates access {{ model.query.activity }} directly.
     */
    public function getActivity(): ?Activity
    {
        $activities = $this->getActivities();
        if (\count($activities) === 1) {
            return $activities[0];
        }

        return null;
    }

    public function getInvoiceDate(): ?\DateTime
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(?\DateTime $invoiceDate): void
    {
        $this->invoiceDate = $invoiceDate;
    }

    public function isAllowTemplateOverwrite(): bool
    {
        return $this->allowTemplateOverwrite;
    }

    public function setAllowTemplateOverwrite(bool $allowTemplateOverwrite): void
    {
        $this->allowTemplateOverwrite = $allowTemplateOverwrite;
    }
}
