<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form;

use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Repository\ProjectRepository;
use App\Crm\Domain\Repository\Query\ProjectFormTypeQuery;
use App\Crm\Transport\Form\Type\ActivityType;
use App\Crm\Transport\Form\Type\CustomerType;
use App\Crm\Transport\Form\Type\ProjectType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Helper functions to manage dependent customer-project-activity fields.
 *
 * If you always want to show the list of all available projects/activities, use the form types directly.
 */
trait FormTrait
{
    protected function addCustomer(FormBuilderInterface $builder, ?Customer $customer = null): void
    {
        $builder->add('customer', CustomerType::class, [
            'query_builder_for_user' => true,
            'customers' => $customer,
            'data' => $customer,
            'required' => false,
            'placeholder' => '',
            'mapped' => false,
            'project_enabled' => true,
        ]);
    }

    protected function addProject(FormBuilderInterface $builder, bool $isNew, ?Project $project = null, ?Customer $customer = null, array $options = []): void
    {
        $options = array_merge([
            'placeholder' => '',
            'activity_enabled' => true,
            'query_builder_for_user' => true,
            'join_customer' => true,
        ], $options);

        $builder->add('project', ProjectType::class, array_merge($options, [
            'projects' => $project,
            'customers' => $customer,
        ]));

        // replaces the project select after submission, to make sure only projects for the selected customer are displayed
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($builder, $project, $customer, $isNew, $options) {
                /** @var array<string, mixed> $data */
                $data = $event->getData();
                $customer = \array_key_exists('customer', $data) && $data['customer'] !== '' ? $data['customer'] : null;
                $project = \array_key_exists('project', $data) && $data['project'] !== '' ? $data['project'] : $project;

                $event->getForm()->add('project', ProjectType::class, array_merge($options, [
                    'group_by' => null,
                    'query_builder' => function (ProjectRepository $repo) use ($builder, $project, $customer, $isNew) {
                        // is there a better way to prevent starting a record with a hidden project ?
                        $project = \is_string($project) ? (int)$project : $project;
                        $customer = \is_string($customer) ? (int)$customer : $customer;
                        if ($isNew && \is_int($project)) {
                            /** @var Project $project */
                            $project = $repo->find($project);
                            if ($project !== null) {
                                if (!$project->getCustomer()->isVisible()) {
                                    $customer = null;
                                    $project = null;
                                } elseif (!$project->isVisible()) {
                                    $project = null;
                                }
                            }
                        }

                        if ($project !== null && !\is_int($project) && !($project instanceof Project)) {
                            throw new \InvalidArgumentException('Project type needs a project object or an ID');
                        }

                        if ($customer !== null && !\is_int($customer) && !($customer instanceof Customer)) {
                            throw new \InvalidArgumentException('Project type needs a customer object or an ID');
                        }

                        $query = new ProjectFormTypeQuery($project, $customer);
                        $query->setUser($builder->getOption('user'));
                        $query->setWithCustomer(true);

                        return $repo->getQueryBuilderForFormType($query);
                    },
                ]));
            }
        );
    }

    protected function addActivity(FormBuilderInterface $builder, ?Activity $activity = null, ?Project $project = null, array $options = []): void
    {
        $options = array_merge([
            'placeholder' => '',
            'query_builder_for_user' => true,
        ], $options);

        $options['projects'] = $project;
        $options['activities'] = $activity;

        $builder->add('activity', ActivityType::class, $options);

        // replaces the activity select after submission, to make sure only activities for the selected project are displayed
        $builder->addEventListener(
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) use ($options) {
                /** @var array<string, mixed> $data */
                $data = $event->getData();

                if (!\array_key_exists('project', $data) || $data['project'] === '' || $data['project'] === null) {
                    return;
                }

                $options['projects'] = $data['project'];

                $event->getForm()->add('activity', ActivityType::class, $options);
            }
        );
    }
}
