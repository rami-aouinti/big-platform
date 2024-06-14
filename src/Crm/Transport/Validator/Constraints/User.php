<?php

declare(strict_types=1);

namespace App\Crm\Transport\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @package App\Crm\Transport\Validator\Constraints
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
final class User extends Constraint
{
    public const string USER_EXISTING_EMAIL = 'kimai-user-00';
    public const string USER_EXISTING_NAME = 'kimai-user-01';
    public const string USER_EXISTING_EMAIL_AS_NAME = 'kimai-user-02';
    public const string USER_EXISTING_NAME_AS_EMAIL = 'kimai-user-03';

    protected const array ERROR_NAMES = [
        self::USER_EXISTING_EMAIL => 'The email is already used.',
        self::USER_EXISTING_NAME => 'The username is already used.',
        self::USER_EXISTING_EMAIL_AS_NAME => 'An equal username is already used.',
        self::USER_EXISTING_NAME_AS_EMAIL => 'An equal email is already used.',
    ];

    public string $message = 'The user has invalid settings.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
