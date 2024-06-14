<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Repository\TagRepository;
use App\Crm\Transport\Form\DataTransformer\TagArrayToStringTransformer;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Custom form field type to enter tags or use one of autocompleted field
 */
final class TagsInputType extends AbstractType
{
    public function __construct(
        private readonly TagRepository $tagRepository,
        private readonly UrlGeneratorInterface $router
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new CollectionToArrayTransformer(), true);
        $builder->addModelTransformer(new TagArrayToStringTransformer($this->tagRepository, (bool)$options['allow_create']), true);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'documentation' => [
                'type' => 'string',
                'description' => 'Comma separated list of tags',
            ],
            'allow_create' => false,
            'label' => 'tag',
        ]);
        $resolver->setAllowedTypes('allow_create', 'bool');
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-autocomplete-url' => $this->router->generate('get_tags'),
            'data-minimum-character' => 3,
            'class' => 'form-select',
            'autocomplete' => 'off',
            'data-form-widget' => 'autocomplete',
        ]);

        if ($options['allow_create']) {
            $view->vars['attr'] = array_merge($view->vars['attr'], [
                'data-create' => 'post_tag',
            ]);
        }
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}
