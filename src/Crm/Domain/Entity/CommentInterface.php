<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use App\User\Domain\Entity\User;

interface CommentInterface
{
    public function getId(): ?int;

    public function getMessage(): ?string;

    public function setMessage(string $message): void;

    public function getCreatedBy(): ?User;

    public function setCreatedBy(User $createdBy): void;

    public function getCreatedAt(): ?\DateTime;

    public function setCreatedAt(\DateTime $createdAt): void;

    public function isPinned(): bool;

    public function setPinned(bool $pinned): void;
}
