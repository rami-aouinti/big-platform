<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Extension;

use App\User\Domain\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserExtension extends AbstractTypeExtension
{
    public function __construct(
        private Security $security
    ) {
    }

    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['user']);
        // null needs to be allowed, as there is no user for anonymous forms (like "forgot password" and "registration")
        $resolver->setAllowedTypes('user', [User::class, 'null']);
        $resolver->setDefault('user', $this->security->getUser());
    }
}
