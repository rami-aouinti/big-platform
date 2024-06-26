<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Transport\Form\Type\YesNoType;
use App\User\Transport\Form\Type\Console\TeamType;
use App\User\Transport\Form\Type\Console\UserRoleType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to create Users.
 */
class UserCreateType extends UserEditType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('username', null, [
            'label' => 'username',
            'required' => true,
            'attr' => [
                'autofocus' => 'autofocus',
            ],
        ]);

        $builder->add('plainPassword', RepeatedType::class, [
            'required' => true,
            'type' => PasswordType::class,
            'first_options' => [
                'label' => 'password',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'block_prefix' => 'secret',
            ],
            'second_options' => [
                'label' => 'password_repeat',
                'attr' => [
                    'autocomplete' => 'new-password',
                ],
                'block_prefix' => 'secret',
            ],
        ]);

        parent::buildForm($builder, $options);

        if ($options['include_teams'] === true) {
            $builder->add('teams', TeamType::class, [
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ]);
        }

        if ($options['include_roles'] === true) {
            $builder->add('roles', UserRoleType::class, [
                'multiple' => true,
                'expanded' => false,
                'required' => false,
            ]);
        }

        $builder->add('requiresPasswordReset', YesNoType::class, [
            'label' => 'force_password_change',
            'help' => 'force_password_change_help',
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'validation_groups' => ['UserCreate', 'Registration'],
            'include_roles' => false,
            'include_teams' => false,
        ]);
    }
}
