<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

trait VisibilityTrait
{
    private int $visibility = VisibilityInterface::SHOW_VISIBLE;

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    public function setVisibility(int $visibility): void
    {
        if (!\in_array($visibility, VisibilityInterface::ALLOWED_VISIBILITY_STATES, true)) {
            throw new \InvalidArgumentException('Unknown visibility given');
        }
        $this->visibility = $visibility;
    }

    public function isShowHidden(): bool
    {
        return $this->visibility === VisibilityInterface::SHOW_HIDDEN;
    }

    public function isShowVisible(): bool
    {
        return $this->visibility === VisibilityInterface::SHOW_VISIBLE;
    }

    public function setShowBoth(): void
    {
        $this->setVisibility(VisibilityInterface::SHOW_BOTH);
    }

    public function isShowBoth(): bool
    {
        return $this->visibility === VisibilityInterface::SHOW_BOTH;
    }
}
