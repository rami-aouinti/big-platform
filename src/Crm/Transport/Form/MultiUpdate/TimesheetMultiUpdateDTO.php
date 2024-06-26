<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\MultiUpdate;

use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\EntityWithMetaFields;
use App\Crm\Domain\Entity\MetaTableTypeInterface;
use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Entity\Tag;
use App\Crm\Domain\Entity\TimesheetMeta;
use App\User\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @internal
 */
#[\App\Crm\Transport\Validator\Constraints\TimesheetMultiUpdate]
final class TimesheetMultiUpdateDTO extends MultiUpdateTableDTO implements EntityWithMetaFields
{
    /**
     * @var iterable<Tag>
     */
    private iterable $tags = [];
    private bool $replaceTags = false;
    private bool $recalculateRates = false;
    private ?Customer $customer = null;
    private ?Project $project = null;
    private ?Activity $activity = null;
    private ?User $user = null;
    private ?bool $exported = null;
    private ?bool $billable = null;
    private ?float $fixedRate = null;
    private ?float $hourlyRate = null;
    /**
     * @var Collection<TimesheetMeta>
     */
    private Collection $meta;
    /**
     * @var array<string>
     */
    private array $updateMeta = [];
    private bool $replaceDescription = false;
    private ?string $description = null;

    public function __construct()
    {
        $this->meta = new ArrayCollection();
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): void
    {
        $this->project = $project;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity): void
    {
        $this->activity = $activity;
    }

    /**
     * @return iterable<Tag>
     */
    public function getTags(): iterable
    {
        return $this->tags;
    }

    public function setTags(iterable $tags): void
    {
        $this->tags = $tags;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function isExported(): ?bool
    {
        return $this->exported;
    }

    public function setExported(?bool $exported): void
    {
        $this->exported = $exported;
    }

    public function isBillable(): ?bool
    {
        return $this->billable;
    }

    public function setBillable(?bool $billable): void
    {
        $this->billable = $billable;
    }

    public function isRecalculateRates(): bool
    {
        return $this->recalculateRates;
    }

    public function setRecalculateRates(bool $recalculateRates): void
    {
        $this->recalculateRates = $recalculateRates;
    }

    public function isReplaceTags(): bool
    {
        return $this->replaceTags;
    }

    public function setReplaceTags(bool $replaceTags): void
    {
        $this->replaceTags = $replaceTags;
    }

    public function isReplaceDescription(): bool
    {
        return $this->replaceDescription;
    }

    public function setReplaceDescription(bool $replaceDescription): void
    {
        $this->replaceDescription = $replaceDescription;
    }

    public function getFixedRate(): ?float
    {
        return $this->fixedRate;
    }

    public function setFixedRate(?float $fixedRate): void
    {
        $this->fixedRate = $fixedRate;
    }

    public function getHourlyRate(): ?float
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(?float $hourlyRate): void
    {
        $this->hourlyRate = $hourlyRate;
    }

    /**
     * @return TimesheetMeta[]|Collection
     */
    public function getMetaFields(): Collection
    {
        return $this->meta;
    }

    public function getMetaField(string $name): ?MetaTableTypeInterface
    {
        foreach ($this->meta as $field) {
            if (strtolower($field->getName()) === strtolower($name)) {
                return $field;
            }
        }

        return null;
    }

    public function setMetaField(MetaTableTypeInterface $meta): EntityWithMetaFields
    {
        $this->updateMeta[$meta->getName()] = $meta->getName();
        if (null === ($current = $this->getMetaField($meta->getName()))) {
            $this->meta->add($meta);

            return $this;
        }

        $current->merge($meta);

        return $this;
    }

    public function setUpdateMeta(array $names): EntityWithMetaFields
    {
        $this->updateMeta = $names;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getUpdateMeta(): array
    {
        return $this->updateMeta;
    }
}
