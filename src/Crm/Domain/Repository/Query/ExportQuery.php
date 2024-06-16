<?php

declare(strict_types=1);

namespace App\Crm\Domain\Repository\Query;

class ExportQuery extends TimesheetQuery
{
    private ?string $renderer = null;
    private bool $markAsExported = false;

    public function __construct()
    {
        parent::__construct();
        $this->setDefaults([
            'order' => BaseQuery::ORDER_ASC,
            'state' => TimesheetQuery::STATE_STOPPED,
            'exported' => TimesheetQuery::STATE_NOT_EXPORTED,
        ]);
    }

    public function getRenderer(): ?string
    {
        return $this->renderer;
    }

    public function setRenderer(string $renderer): self
    {
        $this->renderer = $renderer;

        return $this;
    }

    public function isMarkAsExported(): bool
    {
        return $this->markAsExported;
    }

    public function setMarkAsExported(?bool $markAsExported): self
    {
        if ($markAsExported === null) {
            $markAsExported = false;
        }
        $this->markAsExported = $markAsExported;

        return $this;
    }
}
