<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Calendar\Domain\Entity\Project;
use App\User\Transport\Form\Type\Console\TeamType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProjectTeamPermissionForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('teams', TeamType::class, [
                'multiple' => true,
                'expanded' => true,
                'by_reference' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'admin_project_teams_edit',
            'attr' => [
                'data-form-event' => 'kimai.projectTeamUpdate',
            ],
        ]);
    }
}
