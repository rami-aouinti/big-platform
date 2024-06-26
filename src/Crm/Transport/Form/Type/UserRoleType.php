<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Admin\Auth\Security\RoleService;
use App\User\Domain\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select a user role.
 * @extends AbstractType<User>
 */
final class UserRoleType extends AbstractType
{
    public function __construct(
        private RoleService $roles
    ) {
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'roles',
            'include_default' => false,
        ]);

        $resolver->setDefault('choices', function (Options $options): array {
            $roles = [];
            foreach ($this->roles->getAvailableNames() as $name) {
                $roles[$name] = $name;
            }

            if ($options['include_default'] !== true && isset($roles[User::DEFAULT_ROLE])) {
                unset($roles[User::DEFAULT_ROLE]);
            }

            return $roles;
        });
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
