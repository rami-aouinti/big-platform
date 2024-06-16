<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Toolbar;

use App\Crm\Domain\Repository\Query\InvoiceArchiveQuery;
use App\User\Transport\Form\Type\Console\InvoiceStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Defines the form used for filtering timesheet entries for invoices.
 * @extends AbstractType<InvoiceArchiveQuery>
 */
final class InvoiceArchiveForm extends AbstractType
{
    use ToolbarFormTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addSearchTermInputField($builder);
        $this->addDateRange($builder, [
            'timezone' => $options['timezone'],
        ]);
        $this->addCustomerMultiChoice($builder, [
            'required' => false,
            'start_date_param' => null,
            'end_date_param' => null,
            'ignore_date' => true,
            'placeholder' => '',
        ], true);
        $builder->add('status', InvoiceStatusType::class, [
            'required' => false,
        ]);
        $this->addPageSizeChoice($builder);
        $this->addHiddenPagination($builder);
        $this->addOrder($builder);
        $this->addOrderBy($builder, InvoiceArchiveQuery::INVOICE_ARCHIVE_ORDER_ALLOWED);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoiceArchiveQuery::class,
            'csrf_protection' => false,
            'timezone' => date_default_timezone_get(),
        ]);
    }
}
