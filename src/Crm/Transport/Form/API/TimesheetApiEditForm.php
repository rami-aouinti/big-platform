<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\API;

use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Transport\Form\TimesheetEditForm;
use App\Crm\Transport\Form\Type\BillableType;
use App\Crm\Transport\Form\Type\TagsInputType;
use App\Crm\Transport\Form\Type\TimesheetBillableType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TimesheetApiEditForm extends TimesheetEditForm
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if ($builder->has('metaFields')) {
            $builder->remove('metaFields');
        }

        if ($builder->has('duration')) {
            $builder->remove('duration');
        }

        if ($builder->has('user')) {
            $builder->get('user')->setRequired(false);
        }

        // TODO this is only a quick fix, see bugs reports
        if ($builder->has('duration')) {
            $builder->remove('duration');
        }

        if ($builder->has('tags')) {
            $builder->remove('tags');
            // @deprecated for BC reasons here, arrays will be supported in 2.0
            $builder->add('tags', TagsInputType::class, [
                'required' => false,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'csrf_protection' => false,
            'allow_duration' => false,
            // overwritten and changed to default "true",
            // because the docs are cached without these fields otherwise
            'include_user' => true,
            'include_exported' => true,
            'include_billable' => true,
            'include_rate' => true,
        ]);
    }
    protected function addBillable(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['include_billable']) {
            return;
        }

        $builder->add('billable', BillableType::class);

        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                if (\array_key_exists('billable', $data)) {
                    $data['billableMode'] = Timesheet::BILLABLE_AUTOMATIC;
                    $event->getForm()->add('billableMode', TimesheetBillableType::class, []);
                    $billable = $data['billable'] === null ? false : (bool)$data['billable'];
                    if ($billable === true) {
                        $data['billableMode'] = Timesheet::BILLABLE_YES;
                    } elseif ($billable === false) {
                        $data['billableMode'] = Timesheet::BILLABLE_NO;
                    }
                }
                $event->setData($data);
            }
        );
    }

    protected function addBegin(FormBuilderInterface $builder, array $dateTimeOptions, array $options = []): void
    {
        $builder->add('begin', DateTimeApiType::class, array_merge($dateTimeOptions, [
            'label' => 'begin',
        ]));
    }

    protected function addEnd(FormBuilderInterface $builder, array $dateTimeOptions, array $options = []): void
    {
        $builder->add('end', DateTimeApiType::class, array_merge($dateTimeOptions, [
            'label' => 'end',
            'required' => false,
        ]));
    }
}
