<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Transport\Form\Type\TeamMemberType;
use App\Crm\Domain\Entity\Team;
use App\User\Transport\Form\Type\Console\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TeamEditForm
 *
 * @package App\Crm\Transport\Form
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class TeamEditForm extends AbstractType
{
    use ColorTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users = [];

        /** @var Team|null $team */
        $team = $options['data'] ?? null;
        if ($team !== null) {
            $users = $team->getUsers();
        }

        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'attr' => [
                    'autofocus' => 'autofocus',
                ],
                'documentation' => [
                    'type' => 'string',
                    'description' => 'Name of the team',
                ],
            ]);

        $this->addColor($builder);

        $builder->add('members', CollectionType::class, [
            'entry_type' => TeamMemberType::class,
            'entry_options' => [
                'label' => false,
                'include_users' => $users,
            ],
            'allow_add' => true,
            'by_reference' => false,
            'allow_delete' => true,
            'label' => 'team.member',
            'translation_domain' => 'teams',
            'documentation' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'properties' => [
                        'user' => [
                            'type' => 'integer',
                            'description' => 'User ID',
                        ],
                        'teamlead' => [
                            'type' => 'boolean',
                            'description' => 'Whether the user is a teamlead',
                        ],
                    ],
                ],
                'description' => 'All team members',
            ],
        ]);

        $builder->add('users', UserType::class, [
            'label' => 'add_user.label',
            'help' => 'team.add_user.help',
            'mapped' => false,
            'multiple' => false,
            'expanded' => false,
            'required' => false,
            'ignore_users' => $team !== null ? $team->getUsers() : [],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'admin_team_edit',
            'attr' => [
                'data-form-event' => 'kimai.teamUpdate',
            ],
        ]);
    }
}
