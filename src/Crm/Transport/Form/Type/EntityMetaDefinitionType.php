<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\MetaTableTypeInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Custom form field type to edit entity meta field.
 */
final class EntityMetaDefinitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                /** @var MetaTableTypeInterface $definition */
                $definition = $event->getData();

                $attr = [
                    'data-name' => $definition->getName(),
                    'class' => '',
                ];
                $options = $definition->getOptions();

                if (\array_key_exists('attr', $options) && \is_array($options['attr'])) {
                    $attr = array_merge($attr, $options['attr']);
                    unset($options['attr']);
                }

                $required = $definition->isRequired();
                $constraints = $definition->getConstraints();

                // this will prevent that someone deletes the form element from the DOM and submits the form without
                if ($required) {
                    $constraints[] = new NotNull();
                }

                $event->getForm()->add('value', $definition->getType(), array_merge([
                    'label' => $definition->getLabel(),
                    'constraints' => $constraints,
                    'required' => $required,
                    'attr' => $attr,
                    'row_attr' => [
                        'class' => 'p-0',
                    ],
                ], $options));
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MetaTableTypeInterface::class,
        ]);
    }
}
