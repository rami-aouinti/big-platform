<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Crm\Application\Service\Activity\ActivityStatisticService;
use App\Crm\Application\Service\Customer\CustomerStatisticService;
use App\Crm\Application\Service\Invoice\Hydrator\InvoiceItemDefaultHydrator;
use App\Crm\Application\Service\Invoice\Hydrator\InvoiceModelActivityHydrator;
use App\Crm\Application\Service\Invoice\Hydrator\InvoiceModelCustomerHydrator;
use App\Crm\Application\Service\Invoice\Hydrator\InvoiceModelDefaultHydrator;
use App\Crm\Application\Service\Invoice\Hydrator\InvoiceModelProjectHydrator;
use App\Crm\Application\Service\Invoice\Hydrator\InvoiceModelUserHydrator;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\ExportableItem;
use App\Crm\Domain\Entity\InvoiceTemplate;
use App\Crm\Domain\Repository\Query\InvoiceQuery;
use App\User\Domain\Entity\User;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Exception;

/**
 * InvoiceModel is the ONLY value that a RendererInterface receives for generating the invoice,
 * besides the InvoiceDocument which is used as a "template".
 */
final class InvoiceModel
{
    private ?Customer $customer = null;
    private ?InvoiceQuery $query = null;
    /**
     * @var ExportableItem[]
     */
    private array $entries = [];
    private ?InvoiceTemplate $template = null;
    private ?CalculatorInterface $calculator = null;
    private ?NumberGeneratorInterface $generator = null;
    private DateTimeInterface $invoiceDate;
    private ?User $user = null;
    private InvoiceFormatter $formatter;
    /**
     * @var InvoiceModelHydrator[]
     */
    private array $modelHydrator = [];
    /**
     * @var InvoiceItemHydrator[]
     */
    private array $itemHydrator = [];
    private ?string $invoiceNumber = null;
    private bool $hideZeroTax = false;

    /**
     * @internal use InvoiceModelFactory
     */
    public function __construct(
        InvoiceFormatter $formatter,
        CustomerStatisticService $customerStatistic,
        ProjectStatisticService $projectStatistic,
        ActivityStatisticService $activityStatistic
    ) {
        $this->invoiceDate = new DateTimeImmutable();
        $this->formatter = $formatter;
        $this->addModelHydrator(new InvoiceModelDefaultHydrator());
        $this->addModelHydrator(new InvoiceModelCustomerHydrator($customerStatistic));
        $this->addModelHydrator(new InvoiceModelProjectHydrator($projectStatistic));
        $this->addModelHydrator(new InvoiceModelActivityHydrator($activityStatistic));
        $this->addModelHydrator(new InvoiceModelUserHydrator());
        $this->addItemHydrator(new InvoiceItemDefaultHydrator());
    }

    public function getQuery(): ?InvoiceQuery
    {
        return $this->query;
    }

    public function setQuery(InvoiceQuery $query): void
    {
        $this->query = $query;
    }

    /**
     * Returns the raw data from the model.
     *
     * Do not use this method for rendering the invoice, use getCalculator()->getEntries() instead.
     *
     * @return ExportableItem[]
     */
    public function getEntries(): array
    {
        return $this->entries;
    }

    /**
     * @param ExportableItem[] $entries
     */
    public function addEntries(array $entries): self
    {
        $this->entries = array_merge($this->entries, $entries);

        return $this;
    }

    public function addModelHydrator(InvoiceModelHydrator $hydrator): self
    {
        $this->modelHydrator[] = $hydrator;

        return $this;
    }

    public function addItemHydrator(InvoiceItemHydrator $hydrator): self
    {
        $hydrator->setInvoiceModel($this);

        $this->itemHydrator[] = $hydrator;

        return $this;
    }

    public function getTemplate(): ?InvoiceTemplate
    {
        return $this->template;
    }

    public function setTemplate(InvoiceTemplate $template): void
    {
        $this->template = $template;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * Requires the template and invoice date to be set
     *
     * @throws Exception
     */
    public function getDueDate(): DateTimeInterface
    {
        $date = DateTimeImmutable::createFromInterface($this->getInvoiceDate());

        $dueDays = 14;
        if ($this->getTemplate() !== null) {
            $dueDays = $this->getTemplate()->getDueDays();
        }

        return $date->add(new DateInterval('P' . $dueDays . 'D'));
    }

    public function getInvoiceDate(): DateTimeInterface
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(DateTimeInterface $date): void
    {
        $this->invoiceDate = $date;
    }

    /**
     * @throws Exception
     */
    public function getInvoiceNumber(): string
    {
        if ($this->generator === null) {
            throw new Exception('InvoiceModel::getInvoiceNumber() cannot be called before calling setNumberGenerator()');
        }

        if ($this->invoiceNumber === null) {
            $this->invoiceNumber = $this->generator->getInvoiceNumber();
        }

        return $this->invoiceNumber;
    }

    public function setNumberGenerator(NumberGeneratorInterface $generator): self
    {
        $this->generator = $generator;
        $this->generator->setModel($this);

        return $this;
    }

    public function setCalculator(CalculatorInterface $calculator): self
    {
        $this->calculator = $calculator;
        $this->calculator->setModel($this);

        return $this;
    }

    public function getCalculator(): ?CalculatorInterface
    {
        return $this->calculator;
    }

    /**
     * Returns the user who is currently creating the invoice.
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getFormatter(): InvoiceFormatter
    {
        return $this->formatter;
    }

    public function setFormatter(InvoiceFormatter $formatter): self
    {
        $this->formatter = $formatter;

        return $this;
    }

    public function getCurrency(): string
    {
        if ($this->getCustomer() !== null && $this->getCustomer()->getCurrency() !== null) {
            return $this->getCustomer()->getCurrency();
        }

        return Customer::DEFAULT_CURRENCY;
    }

    public function toArray(): array
    {
        $values = [];

        foreach ($this->modelHydrator as $hydrator) {
            $values = array_merge($values, $hydrator->hydrate($this));
        }

        return $values;
    }

    public function itemToArray(InvoiceItem $invoiceItem): array
    {
        $values = [];

        foreach ($this->itemHydrator as $hydrator) {
            $values = array_merge($values, $hydrator->hydrate($invoiceItem));
        }

        return $values;
    }

    public function isHideZeroTax(): bool
    {
        return $this->hideZeroTax;
    }

    public function setHideZeroTax(bool $hideZeroTax): void
    {
        $this->hideZeroTax = $hideZeroTax;
    }
}
