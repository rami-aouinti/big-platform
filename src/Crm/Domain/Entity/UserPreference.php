<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\Crm\Transport\Form\Type\YesNoType;
use App\User\Domain\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @package App\Crm\Domain\Entity
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[ORM\Table(name: 'kimai2_user_preferences')]
#[ORM\UniqueConstraint(columns: ['user_id', 'name'])]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
#[Serializer\ExclusionPolicy('all')]
class UserPreference
{
    public const string HOURLY_RATE = 'hourly_rate';
    public const string INTERNAL_RATE = 'internal_rate';
    public const string SKIN = 'skin';
    public const string LANGUAGE = 'language';
    public const string LOCALE = 'locale';
    public const string TIMEZONE = 'timezone';
    public const string FIRST_WEEKDAY = 'first_weekday';
    public const string WORK_HOURS_MONDAY = 'work_monday';
    public const string WORK_HOURS_TUESDAY = 'work_tuesday';
    public const string WORK_HOURS_WEDNESDAY = 'work_wednesday';
    public const string WORK_HOURS_THURSDAY = 'work_thursday';
    public const string WORK_HOURS_FRIDAY = 'work_friday';
    public const string WORK_HOURS_SATURDAY = 'work_saturday';
    public const string WORK_HOURS_SUNDAY = 'work_sunday';
    public const string WORK_STARTING_DAY = 'work_start_day';
    public const string PUBLIC_HOLIDAY_GROUP = 'public_holiday_group';
    public const string HOLIDAYS_PER_YEAR = 'holidays';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: 'id', type: 'integer')]
    private ?int $id = null;
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'preferences')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $user = null;
    #[ORM\Column(name: 'name', type: 'string', length: 50, nullable: false)]
    #[Assert\NotNull]
    #[Assert\Length(min: 2, max: 50)]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private string $name;
    #[Assert\Length(max: 250)]
    #[Serializer\Expose]
    #[Serializer\Groups(['Default'])]
    private string|int|float|bool|null $value = null;
    private ?string $type = null;
    private bool $enabled = true;
    /**
     * @var Constraint[]
     */
    private array $constraints = [];
    /**
     * An array of options for the form element
     */
    private array $options = [];
    private int $order = 1000;
    private string $section = 'default';

    public function __construct(string $name, string|int|float|bool|null $value = null)
    {
        $this->name = $this->sanitizeName($name);
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getName(): ?string
    {
        $sanitized = $this->sanitizeName($this->name);

        if ($sanitized !== $this->name) {
            $this->name = $sanitized;
        }

        return $this->name;
    }

    public function matches(string $name): bool
    {
        return $this->sanitizeName($name) === $this->getName();
    }

    public function getValue(): bool|int|float|string|null
    {
        return match ($this->type) {
            YesNoType::class, CheckboxType::class => (bool)$this->value,
            IntegerType::class => (int)$this->value,
            NumberType::class => (float)$this->value,
            default => $this->value,
        };
    }

    /**
     * Given $value will not be serialized before its stored, so it should be one of the types:
     * integer, float, string, boolean or null
     *
     * @param mixed $value
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Sets the form type to edit that setting.
     */
    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Set the constraints which are used for validation of the value.
     *
     * @param Constraint[] $constraints
     * @return $this
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = $constraints;

        return $this;
    }

    /**
     * Adds a constraint which is used for validation of the value.
     *
     * @return $this
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[] = $constraint;

        return $this;
    }

    /**
     * @return Constraint[]
     */
    public function getConstraints(): array
    {
        return $this->constraints;
    }

    /**
     * Set an array of options for the FormType.
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Returns an array with options for the FormType.
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    public function getLabel(): ?string
    {
        if (isset($this->options['label'])) {
            return $this->options['label'];
        }

        return $this->name;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;

        return $this;
    }

    public function setSection(string $section): self
    {
        $this->section = $section;

        return $this;
    }

    public function getSection(): string
    {
        return $this->section;
    }

    private function sanitizeName(?string $name): string
    {
        return str_replace(['.', '-'], '_', $name);
    }
}
