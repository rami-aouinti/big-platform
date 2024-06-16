<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Calendar\Domain\Entity\UserPreference;
use App\User\Domain\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to edit user preferences.
 * @extends AbstractType<User>
 */
final class UserPreferencesCollectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var ArrayCollection<UserPreference> $collection */
                $collection = $event->getData();
                foreach ($collection as $collectionItem) {
                    $collection->removeElement($collectionItem);

                    if (!($collectionItem instanceof UserPreference)) {
                        continue;
                    }

                    // prevents unconfigured values from showing up in the form
                    if ($collectionItem->getType() === null) {
                        continue;
                    }

                    $collection->set($collectionItem->getName(), $collectionItem);
                }
            },
            // must be a higher priority then the listener in UserPreferenceType
            100
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => UserPreferenceType::class,
            'entry_options' => [
                'label' => false,
            ],
            'allow_add' => false,
            'allow_delete' => false,
            'label' => false,
            'delete_empty' => false,
        ]);
    }

    public function getParent(): string
    {
        return CollectionType::class;
    }
}
