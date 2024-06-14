<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Extension;

use App\Crm\Application\Reporting\MonthlyUserList\MonthlyUserListForm;
use App\Crm\Application\Reporting\WeeklyUserList\WeeklyUserListForm;
use App\Crm\Application\Reporting\YearlyUserList\YearlyUserListForm;
use App\Crm\Transport\Form\Helper\ToolbarHelper;
use App\Crm\Transport\Form\Toolbar\ExportToolbarForm;
use App\Crm\Transport\Form\Toolbar\InvoiceToolbarForm;
use App\Crm\Transport\Form\Toolbar\TimesheetExportToolbarForm;
use App\Crm\Transport\Form\Toolbar\TimesheetToolbarForm;
use App\Crm\Transport\Form\Toolbar\UserToolbarForm;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class ToolbarFormExtension extends AbstractTypeExtension
{
    public function __construct(
        private ToolbarHelper $toolbarHelper
    ) {
    }

    public static function getExtendedTypes(): iterable
    {
        return [
            InvoiceToolbarForm::class,
            ExportToolbarForm::class,
            TimesheetToolbarForm::class,
            TimesheetExportToolbarForm::class,
            UserToolbarForm::class,
            WeeklyUserListForm::class,
            MonthlyUserListForm::class,
            YearlyUserListForm::class,
        ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->toolbarHelper->cleanupForm($builder);
    }
}
