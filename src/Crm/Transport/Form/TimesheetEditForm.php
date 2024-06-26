<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Configuration\SystemConfiguration;
use App\Crm\Application\Service\Timesheet\Calculator\BillableCalculator;
use App\Crm\Domain\Entity\Timesheet;
use App\Crm\Domain\Repository\CustomerRepository;
use App\Crm\Transport\Form\Type\DatePickerType;
use App\Crm\Transport\Form\Type\DurationType;
use App\Crm\Transport\Form\Type\FixedRateType;
use App\Crm\Transport\Form\Type\HourlyRateType;
use App\Crm\Transport\Form\Type\TimePickerType;
use App\Crm\Transport\Form\Type\TimesheetBillableType;
use App\Crm\Transport\Form\Type\YesNoType;
use App\User\Transport\Form\Type\Console\UserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Defines the form used to manipulate Timesheet entries.
 */
class TimesheetEditForm extends AbstractType
{
    use FormTrait;

    public function __construct(
        private CustomerRepository $customers,
        private SystemConfiguration $systemConfiguration
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $activity = null;
        $project = null;
        $customer = null;
        $currency = false;
        $timezone = $options['timezone'];
        $isNew = true;

        if (isset($options['data'])) {
            /** @var Timesheet $entry */
            $entry = $options['data'];

            $activity = $entry->getActivity();
            $project = $entry->getProject();
            $customer = $project?->getCustomer();

            if ($entry->getId() !== null) {
                $isNew = false;
            }

            if ($project === null && $activity !== null) {
                $project = $activity->getProject();
            }

            if ($customer !== null) {
                $currency = $customer->getCurrency();
            }

            if (null !== ($begin = $entry->getBegin())) {
                $timezone = $begin->getTimezone()->getName();
            }
        }

        $dateTimeOptions = [
            'model_timezone' => $timezone,
            'view_timezone' => $timezone,
        ];

        // primarily for API usage, where we cannot use a user/locale specific format
        if ($options['date_format'] !== null) {
            $dateTimeOptions['format'] = $options['date_format'];
        }

        if ($options['allow_begin_datetime']) {
            $this->addBegin($builder, $dateTimeOptions, $options);
        }

        if ($options['allow_end_datetime']) {
            $this->addEnd($builder, $dateTimeOptions, $options);
        }

        if ($options['allow_duration']) {
            $this->addDuration($builder, $options, (!$options['allow_begin_datetime'] || !$options['allow_end_datetime']), $isNew);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $maxMinutes = $this->systemConfiguration->getTimesheetLongRunningDuration();
        $maxHours = 10;
        if ($maxMinutes > 0) {
            $maxHours = (int)($maxMinutes / 60);
        }

        $resolver->setDefaults([
            'data_class' => Timesheet::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'timesheet_edit',
            'include_user' => false,
            'include_exported' => false,
            'include_billable' => true,
            'include_rate' => true,
            'create_activity' => false,
            'docu_chapter' => 'timesheet.html',
            'method' => 'POST',
            'date_format' => null,
            'timezone' => date_default_timezone_get(),
            'customer' => false, // for API usage
            'allow_begin_datetime' => true,
            'allow_end_datetime' => true,
            'allow_duration' => false,
            'duration_minutes' => null,
            'duration_hours' => $maxHours,
            'attr' => [
                'data-form-event' => 'kimai.timesheetUpdate',
                'data-msg-success' => 'action.update.success',
                'data-msg-error' => 'action.update.error',
            ],
        ]);
    }

    protected function showCustomer(array $options, bool $isNew, int $customerCount): bool
    {
        if (!$isNew && $options['customer']) {
            return true;
        }

        if ($customerCount < 2) {
            return false;
        }

        if (!$options['customer']) {
            return false;
        }

        return true;
    }

    protected function addBegin(FormBuilderInterface $builder, array $dateTimeOptions, array $options = []): void
    {
        $dateOptions = $dateTimeOptions;
        $builder->add('begin_date', DatePickerType::class, array_merge($dateOptions, [
            'label' => 'date',
            'mapped' => false,
            'constraints' => [
                new NotBlank(),
            ],
        ]));

        $timeOptions = $dateTimeOptions;

        $builder->add('begin_time', TimePickerType::class, array_merge($timeOptions, [
            'label' => 'starttime',
            'mapped' => false,
            'constraints' => [
                new NotBlank(),
            ],
        ]));

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                /** @var Timesheet $timesheet */
                $timesheet = $event->getData();
                $begin = $timesheet->getBegin();

                if ($begin !== null) {
                    $event->getForm()->get('begin_date')->setData($begin);
                    $event->getForm()->get('begin_time')->setData($begin);
                }
            }
        );

        // map single fields to original datetime object
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                /** @var Timesheet $data */
                $data = $event->getData();

                /** @var \DateTime|null $date */
                $date = $event->getForm()->get('begin_date')->getData();
                $time = $event->getForm()->get('begin_time')->getData();

                if ($date === null || $time === null) {
                    return;
                }

                // mutable datetime are a problem for doctrine
                $newDate = clone $date;
                $newDate->setTime($time->format('H'), $time->format('i'));

                if ($data->getBegin() === null || $data->getBegin()->getTimestamp() !== $newDate->getTimestamp()) {
                    $data->setBegin($newDate);
                }
            }
        );
    }

    protected function addEnd(FormBuilderInterface $builder, array $dateTimeOptions, array $options = []): void
    {
        $builder->add('end_time', TimePickerType::class, array_merge($dateTimeOptions, [
            'required' => false,
            'label' => 'endtime',
            'mapped' => false,
        ]));

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                /** @var Timesheet|null $data */
                $data = $event->getData();
                if ($data->getEnd() !== null) {
                    $event->getForm()->get('end_time')->setData($data->getEnd());
                }
            }
        );

        // make sure that date & time fields are mapped back to begin & end fields
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                /** @var Timesheet $timesheet */
                $timesheet = $event->getData();
                $oldEnd = $timesheet->getEnd();

                $end = $event->getForm()->get('end_time')->getData();
                if ($end === null || $end === false) {
                    $timesheet->setEnd(null);

                    return;
                }

                // mutable datetime are a problem for doctrine
                $end = clone $end;

                // end is assumed to be the same day then start, if not we raise the day by one
                //$time = $event->getForm()->get('begin_time')->getData();
                $time = $timesheet->getBegin();
                if ($time === null) {
                    throw new \Exception('Cannot work with timesheets without start time');
                }
                $newEnd = clone $time;
                $newEnd->setTime($end->format('H'), $end->format('i'));

                if ($newEnd < $time) {
                    $newEnd->modify('+ 1 day');
                }

                if ($oldEnd === null || $oldEnd->getTimestamp() !== $newEnd->getTimestamp()) {
                    $timesheet->setEnd($newEnd);
                }
            }
        );
    }

    protected function addDuration(FormBuilderInterface $builder, array $options, bool $forceApply = false, bool $autofocus = false): void
    {
        $durationOptions = [
            'required' => false,
            //'toggle' => true,
            'attr' => [
                'placeholder' => '0:00',
            ],
        ];

        if ($autofocus) {
            $durationOptions['attr']['autofocus'] = 'autofocus';
        }

        $duration = $options['duration_minutes'];
        if ($duration !== null && (int)$duration > 0) {
            $durationOptions = array_merge($durationOptions, [
                'preset_minutes' => $duration,
            ]);
        }

        $duration = $options['duration_hours'];
        if ($duration !== null && (int)$duration > 0) {
            $durationOptions = array_merge($durationOptions, [
                'preset_hours' => $duration,
            ]);
        }

        $builder->add('duration', DurationType::class, $durationOptions);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                /** @var Timesheet|null $timesheet */
                $timesheet = $event->getData();
                if ($timesheet === null || $timesheet->isRunning()) {
                    $event->getForm()->get('duration')->setData(null);
                }
            }
        );

        // make sure that duration is mapped back to end field
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) use ($forceApply) {
                /** @var Timesheet $timesheet */
                $timesheet = $event->getData();

                $newDuration = $event->getForm()->get('duration')->getData();
                if ($newDuration !== null && $newDuration > 0 && $newDuration !== $timesheet->getDuration()) {
                    // TODO allow to use a duration that differs from end-start by adding a system configuration check here
                    if ($timesheet->getEnd() === null) {
                        $timesheet->setDuration($newDuration);
                    }
                }

                $duration = $timesheet->getDuration() ?? 0;

                // only apply the duration, if the end is not yet set
                // without that check, the end would be overwritten and the real end time would be lost
                if (($forceApply && $duration > 0) || ($duration > 0 && $timesheet->isRunning())) {
                    $end = clone $timesheet->getBegin();
                    $end->modify('+ ' . $duration . 'seconds');
                    $timesheet->setEnd($end);
                }
            }
        );
    }

    protected function addRates(FormBuilderInterface $builder, $currency, array $options): void
    {
        if (!$options['include_rate']) {
            return;
        }

        $builder
            ->add('fixedRate', FixedRateType::class, [
                'currency' => $currency,
            ])
            ->add('hourlyRate', HourlyRateType::class, [
                'currency' => $currency,
            ]);
    }

    protected function addUser(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['include_user']) {
            return;
        }

        $builder->add('user', UserType::class);
    }

    protected function addExported(FormBuilderInterface $builder, array $options): void
    {
        if (!$options['include_exported']) {
            return;
        }

        $builder->add('exported', YesNoType::class, [
            'label' => 'exported',
        ]);
    }

    protected function addBillable(FormBuilderInterface $builder, array $options): void
    {
        if ($options['include_billable']) {
            $builder->add('billableMode', TimesheetBillableType::class, []);
        }

        $builder->addModelTransformer(new CallbackTransformer(
            function (Timesheet $record) {
                if ($record->getBillableMode() === Timesheet::BILLABLE_DEFAULT) {
                    if ($record->isBillable()) {
                        $record->setBillableMode(Timesheet::BILLABLE_YES);
                    } else {
                        $record->setBillableMode(Timesheet::BILLABLE_NO);
                    }
                }

                return $record;
            },
            function (Timesheet $record) {
                $billable = new BillableCalculator();
                $billable->calculate($record, []);

                return $record;
            }
        ));
    }
}
