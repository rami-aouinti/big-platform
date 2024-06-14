<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Toolbar;

use App\Crm\Domain\Repository\Query\TagQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<TagQuery>
 */
final class TagToolbarForm extends AbstractType
{
    use ToolbarFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addVisibilityChoice($builder);
        $this->addSearchTermInputField($builder);
        $this->addPageSizeChoice($builder);
        $this->addHiddenPagination($builder);
        $this->addOrder($builder);
        $this->addOrderBy($builder, TagQuery::TAG_ORDER_ALLOWED);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TagQuery::class,
            'csrf_protection' => false,
        ]);
    }
}
