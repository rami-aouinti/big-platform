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
 * Defines the form used to set the users API password.
 * @extends AbstractType<User>
 */
final class UserApiPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainApiToken', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'api_token',
                ],
                'second_options' => [
                    'label' => 'api_token_repeat',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'validation_groups' => ['ApiTokenUpdate'],
            'data_class' => User::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'edit_user_password_token',
        ]);
    }
}
