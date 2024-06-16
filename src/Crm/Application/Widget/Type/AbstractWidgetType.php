<?php

declare(strict_types=1);

namespace App\Crm\Application\Widget\Type;

abstract class AbstractWidgetType extends AbstractWidget
{
    private ?string $id = null;
    private string $title = '';
    /**
     * @var array<string>
     */
    private array $permissions = [];

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        if (!empty($this->id)) {
            return $this->id;
        }

        return (new \ReflectionClass($this))->getShortName();
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setOptions(array $options): void
    {
        foreach ($options as $key => $value) {
            $this->setOption($key, $value);
        }
    }

    public function getPermissions(): array
    {
        return $this->permissions;
    }

    public function setPermissions(array $permissions): void
    {
        $this->permissions = $permissions;
    }
}
