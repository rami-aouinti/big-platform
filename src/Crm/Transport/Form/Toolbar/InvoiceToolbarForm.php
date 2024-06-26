<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Toolbar;

use App\Crm\Domain\Repository\Query\InvoiceQuery;
use App\User\Transport\Form\Type\Console\DatePickerType;
use App\User\Transport\Form\Type\Console\InvoiceTemplateType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used for filtering timesheet entries for invoices.
 * @extends AbstractType<InvoiceQuery>
 */
final class InvoiceToolbarForm extends AbstractType
{
    use ToolbarFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addSearchTermInputField($builder);
        $this->addDateRange($builder, [
            'timezone' => $options['timezone'],
        ]);
        $this->addCustomerMultiChoice($builder, [
            'start_date_param' => null,
            'end_date_param' => null,
            'ignore_date' => true,
        ], true);
        $this->addProjectMultiChoice($builder, [
            'ignore_date' => true,
        ], true, true);
        $this->addActivitySelect($builder, [], true, true, false);
        $this->addTagInputField($builder);
        if ($options['include_user']) {
            $this->addUsersChoice($builder);
            $this->addTeamsChoice($builder);
        }
        $this->addExportStateChoice($builder);
        $builder->add('invoiceDate', DatePickerType::class, [
            'required' => true,
        ]);
        $this->addTemplateChoice($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceQuery::class,
            'csrf_protection' => false,
            'include_user' => true,
            'timezone' => date_default_timezone_get(),
        ]);
    }

    protected function addTemplateChoice(FormBuilderInterface $builder): void
    {
        $builder->add('template', InvoiceTemplateType::class, [
            'required' => true,
            'placeholder' => null,
        ]);
    }
}
