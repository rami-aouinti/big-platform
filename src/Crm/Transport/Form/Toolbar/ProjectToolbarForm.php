<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Toolbar;

use App\Crm\Domain\Repository\Query\ProjectQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used for filtering the projects.
 * @extends AbstractType<ProjectQuery>
 */
final class ProjectToolbarForm extends AbstractType
{
    use ToolbarFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addSearchTermInputField($builder);
        $this->addCustomerMultiChoice($builder, [], false);
        $this->addVisibilityChoice($builder);
        $this->addPageSizeChoice($builder);
        $this->addHiddenPagination($builder);
        $this->addOrder($builder);
        $this->addOrderBy($builder, ProjectQuery::PROJECT_ORDER_ALLOWED);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ProjectQuery::class,
            'csrf_protection' => false,
        ]);
    }
}
