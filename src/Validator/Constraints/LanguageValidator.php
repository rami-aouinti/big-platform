<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use App\Service\LocalizationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

use function in_array;

/**
 * Class LanguageValidator
 *
 * @package App\Validator\Constraints
 */
class LanguageValidator extends ConstraintValidator
{
    public function __construct(
        private LocalizationService $localization,
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (in_array($value, $this->localization->getLanguages(), true) !== true) {
            $this->context
                ->buildViolation(Language::MESSAGE)
                ->setParameter('{{ language }}', (string)$value)
                ->setCode(Language::INVALID_LANGUAGE)
                ->addViolation();
        }
    }
}
