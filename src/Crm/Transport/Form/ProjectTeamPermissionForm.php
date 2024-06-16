<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Domain\Entity\Project;
use App\Crm\Transport\Form\Type\TeamType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class ProjectTeamPermissionForm
 *
 * @package App\Crm\Transport\Form
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
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
