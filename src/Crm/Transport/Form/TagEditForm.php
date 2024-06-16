<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Domain\Entity\ColorTrait;
use App\Crm\Domain\Entity\Tag;
use App\Crm\Transport\Form\Type\YesNoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package App\Crm\Transport\Form
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class TagEditForm extends AbstractType
{
    use ColorTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
                'attr' => [
                    'autofocus' => 'autofocus',
                ],
                'documentation' => [
                    'type' => 'string',
                    'description' => 'The tag name (forbidden character: comma)',
                ],
            ])
            ->add('visible', YesNoType::class, [
                'label' => 'visible',
                'help' => 'help.visible',
            ])
        ;
        $this->addColor($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'tags_edit',
            'attr' => [
                'data-form-event' => 'kimai.tagUpdate',
            ],
        ]);
    }
}
