<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Transport\Form\Type\UserRoleType;
use App\User\Domain\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to set roles for a User.
 * @extends AbstractType<User>
 */
final class UserRolesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('roles', UserRoleType::class, [
                'multiple' => true,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['RolesUpdate'],
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'edit_user_roles',
        ]);
    }
}
