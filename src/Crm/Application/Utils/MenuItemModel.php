<?php

declare(strict_types=1);

namespace App\Crm\Application\Utils;

use KevinPapst\TablerBundle\Model\MenuItemInterface;

final class MenuItemModel implements MenuItemInterface
{
    private string $identifier;
    private string $label;
    private ?string $route;
    private array $routeArgs;
    private bool $isActive = false;
    /**
     * @var array<MenuItemModel>
     */
    private array $children = [];
    private ?string $icon;
    private ?MenuItemModel $parent = null;
    private ?string $badge = null;
    private ?string $badgeColor = null;
    private static int $dividerId = 0;
    private bool $divider = false;
    private bool $lastWasDivider = false;
    private bool $expanded = false;
    private string $translationDomain = 'messages';

    private array $childRoutes = [];

    public function __construct(
        string $id,
        string $label,
        ?string $route = null,
        array $routeArgs = [],
        ?string $icon = null
    ) {
        $this->identifier = $id;
        $this->label = $label;
        $this->route = $route;
        $this->routeArgs = $routeArgs;
        $this->icon = $icon;
    }

    /**
     * @return MenuItemModel[]
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    public function getChild(string $id): ?self
    {
        foreach ($this->children as $child) {
            if ($child->getIdentifier() === $id) {
                return $child;
            }
        }

        return null;
    }

    /**
     * @param array<MenuItemModel> $children
     */
    public function setChildren(array $children): void
    {
        $this->children = $children;
    }

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function setIcon(string $icon): void
    {
        $this->icon = $icon;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->getParent()?->setIsActive($isActive);

        $this->isActive = $isActive;
    }

    public function hasParent(): bool
    {
        return $this->parent !== null;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(MenuItemInterface $parent): void
    {
        if (!($parent instanceof self)) {
            throw new \Exception('MenuItemModel::setParent() expects a MenuItemModel');
        }
        $this->parent = $parent;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(?string $route): void
    {
        $this->route = $route;
    }

    public function getRouteArgs(): array
    {
        return $this->routeArgs;
    }

    public function setRouteArgs(array $routeArgs): void
    {
        $this->routeArgs = $routeArgs;
    }

    public function hasChildren(): bool
    {
        if (\count($this->children) < 1) {
            return false;
        }

        foreach ($this->children as $child) {
            if (!$child->isDivider()) {
                return true;
            }
        }

        return false;
    }

    public function addChild(MenuItemInterface $child): void
    {
        if (!($child instanceof self)) {
            throw new \Exception('MenuItemModel::addChild() expects a MenuItemModel');
        }

        // first item should not be a divider
        if (!$this->hasChildren() && $child->isDivider()) {
            return;
        }

        // two divider should not be added as direct siblings
        if ($this->lastWasDivider && $child->isDivider()) {
            return;
        }
        $this->lastWasDivider = $child->isDivider();

        $child->setParent($this);
        $this->children[] = $child;
    }

    public function removeChild(MenuItemInterface $child): void
    {
        if (false !== ($key = array_search($child, $this->children))) {
            unset($this->children[$key]);
        }
    }

    public function findChild(string $identifier): ?self
    {
        return $this->find($identifier, $this);
    }

    public function getActiveChild(): ?self
    {
        foreach ($this->children as $child) {
            if ($child->isActive()) {
                return $child;
            }
        }

        return null;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setBadge(?string $badge): void
    {
        $this->badge = $badge;
    }

    public function setBadgeColor(?string $badgeColor): void
    {
        $this->badgeColor = $badgeColor;
    }

    public function getBadge(): ?string
    {
        return $this->badge;
    }

    public function getBadgeColor(): ?string
    {
        return $this->badgeColor;
    }

    public function setChildRoutes(array $routes): self
    {
        $this->childRoutes = $routes;

        return $this;
    }

    public function addChildRoute(string $route): self
    {
        $this->childRoutes[] = $route;

        return $this;
    }

    public function isChildRoute(string $route): bool
    {
        return \in_array($route, $this->childRoutes);
    }

    public static function createDivider(): self
    {
        $model = new self('divider_' . self::$dividerId++, '');
        $model->setDivider(true);

        return $model;
    }

    public function isDivider(): bool
    {
        return $this->divider;
    }

    public function setDivider(bool $divider): void
    {
        $this->divider = $divider;
    }

    public function isExpanded(): bool
    {
        return $this->expanded;
    }

    public function setExpanded(bool $expanded): void
    {
        $this->expanded = $expanded;
    }

    public function getTranslationDomain(): string
    {
        return $this->translationDomain;
    }

    public function setTranslationDomain(string $translationDomain): void
    {
        $this->translationDomain = $translationDomain;
    }

    private function find(string $identifier, self $menu): ?self
    {
        if ($menu->getIdentifier() === $identifier) {
            return $this;
        }

        foreach ($menu->getChildren() as $child) {
            if ($child->getIdentifier() === $identifier) {
                return $child;
            }
            if ($child->hasChildren()) {
                if (($tmp = $this->find($identifier, $child)) !== null) {
                    return $tmp;
                }
            }
        }

        return null;
    }
}
