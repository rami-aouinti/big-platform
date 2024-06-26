<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Helper;

use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Entity\Activity;

final class ActivityHelper
{
    public const PATTERN_NUMBER = '{number}';
    public const PATTERN_NAME = '{name}';
    public const PATTERN_COMMENT = '{comment}';
    public const PATTERN_SPACER = '{spacer}';
    public const SPACER = ' - ';

    private ?string $pattern = null;

    public function __construct(
        private readonly SystemConfiguration $configuration
    ) {
    }

    public function getChoicePattern(): string
    {
        if ($this->pattern === null) {
            $this->pattern = $this->configuration->find('activity.choice_pattern');

            if ($this->pattern === null || stripos($this->pattern, '{') === false || stripos($this->pattern, '}') === false) {
                $this->pattern = self::PATTERN_NAME;
            }

            $this->pattern = str_replace(self::PATTERN_SPACER, self::SPACER, $this->pattern);
        }

        return $this->pattern;
    }

    public function getChoiceLabel(Activity $activity): string
    {
        $name = $this->getChoicePattern();
        $name = str_replace(self::PATTERN_NAME, $activity->getName(), $name);
        $name = str_replace(self::PATTERN_NUMBER, $activity->getNumber() ?? '', $name);
        $name = str_replace(self::PATTERN_COMMENT, $activity->getComment() ?? '', $name);

        while (str_starts_with($name, self::SPACER)) {
            $name = substr($name, \strlen(self::SPACER));
        }

        while (str_ends_with($name, self::SPACER)) {
            $name = substr($name, 0, -\strlen(self::SPACER));
        }

        if ($name === '' || $name === self::SPACER) {
            $name = $activity->getName();
        }

        return substr($name, 0, 110);
    }
}
