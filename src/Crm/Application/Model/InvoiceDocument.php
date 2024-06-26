<?php

declare(strict_types=1);

namespace App\Crm\Application\Model;

final class InvoiceDocument
{
    public function __construct(
        private \SplFileInfo $file
    ) {
    }

    public function getId(): string
    {
        $file = $this->file->getFilename();

        return substr($file, 0, strpos($file, '.'));
    }

    public function getName(): string
    {
        return basename($this->getFilename());
    }

    public function getFilename(): string
    {
        $path = $this->file->getRealPath();
        if ($path === false) {
            throw new \Exception('Invoice template got deleted from filesystem: ' . $this->file->getFilename());
        }

        return $path;
    }

    public function isTwig(): bool
    {
        return $this->getFileExtension() === 'twig';
    }

    public function getFileExtension(): string
    {
        return $this->file->getExtension();
    }

    public function getLastChange(): int
    {
        $modified = $this->file->getMTime();
        if ($modified === false) {
            throw new \Exception('Invoice template got deleted from filesystem: ' . $this->file->getFilename());
        }

        return $modified;
    }
}
