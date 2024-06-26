<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\User\Domain\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used to set the user password.
 * @extends AbstractType<User>
 */
final class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
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
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['PasswordUpdate'],
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'edit_user_password',
        ]);
    }
}
