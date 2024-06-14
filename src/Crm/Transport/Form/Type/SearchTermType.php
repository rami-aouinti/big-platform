<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Transport\Form\DataTransformer\SearchTermTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

final class SearchTermType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new SearchTermTransformer());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => 'searchTerm',
            'required' => false,
            'attr' => [
                'placeholder' => 'search',
            ],
            'constraints' => [
                new Length([
                    'min' => 2,
                ]),
            ],
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
