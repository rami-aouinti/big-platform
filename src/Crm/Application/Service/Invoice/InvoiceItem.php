<?php

declare(strict_types=1);

namespace App\Crm\Application\Service\Invoice;

use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\Project;
use App\User\Domain\Entity\User;
use DateTime;

/**
 * @package App\Crm\Application\Service\Invoice
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class InvoiceItem
{
    private ?float $fixedRate = null;
    private ?float $hourlyRate = null;
    private float $rate = 0.00;
    private float $rateInternal = 0.00;
    private float $amount = 0.00;
    private ?string $description = null;
    private int $duration = 0;
    private ?DateTime $begin = null;
    private ?DateTime $end = null;
    private ?User $user = null;
    private ?Activity $activity = null;
    private ?Project $project = null;
    /**
     * @var array<string, mixed>
     */
    private array $additionalFields = [];
    private ?string $type = null;
    private ?string $category = null;
    /**
     * @var string[]
     */
    private array $tags = [];

    public function addAdditionalField(string $name, mixed $value): self
    {
        $this->additionalFields[$name] = $value;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAdditionalFields(): array
    {
        return $this->additionalFields;
    }

    public function getAdditionalField(string $name, mixed $default = null): mixed
    {
        if (\array_key_exists($name, $this->additionalFields)) {
            return $this->additionalFields[$name];
        }

        return $default;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): self
    {
        $this->activity = $activity;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function isFixedRate(): bool
    {
        return $this->getFixedRate() !== null;
    }

    public function getFixedRate(): ?float
    {
        return $this->fixedRate;
    }

    public function setFixedRate(?float $fixedRate): self
    {
        $this->fixedRate = $fixedRate;

        return $this;
    }

    public function getHourlyRate(): ?float
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(float $hourlyRate): self
    {
        $this->hourlyRate = $hourlyRate;

        return $this;
    }

    public function getRate(): float
    {
        return $this->rate;
    }

    public function setRate(float $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getInternalRate(): float
    {
        return $this->rateInternal;
    }

    public function setInternalRate(float $rateInternal): self
    {
        $this->rateInternal = $rateInternal;

        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getBegin(): ?\DateTime
    {
        return $this->begin;
    }

    public function setBegin(\DateTime $begin): self
    {
        $this->begin = $begin;

        return $this;
    }

    public function getEnd(): ?\DateTime
    {
        return $this->end;
    }

    public function setEnd(?\DateTime $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

        return $this;
    }

    public function addTag(string $tag): void
    {
        foreach ($this->tags as $t) {
            if (strcasecmp($tag, $t) === 0) {
                return;
            }
        }

        $this->tags[] = $tag;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }
}
