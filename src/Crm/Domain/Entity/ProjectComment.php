<?php

declare(strict_types=1);

namespace App\Crm\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'kimai2_projects_comments')]
#[ORM\Index(columns: ['project_id'])]
#[ORM\Entity]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class ProjectComment implements CommentInterface
{
    use CommentTableTypeTrait;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private Project $project;

    public function __construct(Project $project)
    {
        $this->createdAt = new \DateTime();
        $this->project = $project;
    }

    public function getProject(): Project
    {
        return $this->project;
    }
}
