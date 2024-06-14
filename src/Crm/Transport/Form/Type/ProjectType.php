<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Repository\ProjectRepository;
use App\Crm\Domain\Repository\Query\ActivityQuery;
use App\Crm\Domain\Repository\Query\ProjectFormTypeQuery;
use App\Crm\Transport\Form\Helper\CustomerHelper;
use App\Crm\Transport\Form\Helper\ProjectHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select a project.
 */
final class ProjectType extends AbstractType
{
    public function __construct(
        private ProjectHelper $projectHelper,
        private CustomerHelper $customerHelper
    ) {
    }

    public function getChoiceLabel(Project $project): string
    {
        return $this->projectHelper->getChoiceLabel($project);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return array<string, string|int|null>
     */
    public function getChoiceAttributes(Project $project, $key, $value): array
    {
        $customer = $project->getCustomer();
        if (null !== ($customer)) {
            return [
                'data-customer' => $customer->getId(),
                'data-currency' => $customer->getCurrency(),
            ];
        }

        return [];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // documentation is for NelmioApiDocBundle
            'documentation' => [
                'type' => 'integer',
                'description' => 'Project ID',
            ],
            'label' => 'project',
            'class' => Project::class,
            'choice_label' => [$this, 'getChoiceLabel'],
            'choice_attr' => [$this, 'getChoiceAttributes'],
            'group_by' => function (Project $project, $key, $index) {
                if ($project->getCustomer() === null) {
                    return null;
                }

                return $this->customerHelper->getChoiceLabel($project->getCustomer());
            },
            'query_builder_for_user' => true,
            'activity_enabled' => false,
            'activity_select' => 'activity',
            'activity_visibility' => ActivityQuery::SHOW_VISIBLE,
            'ignore_date' => false,
            'join_customer' => false,
            // @var Project|null
            'ignore_project' => null,
            // @var Customer|Customer[]|int|int[]|null
            'customers' => null,
            // @var Project|Project[]|int|int[]|null
            'projects' => null,
            // @var DateTime|null
            'project_date_start' => null,
            // @var DateTime|null
            'project_date_end' => null,
        ]);

        $resolver->setDefault('query_builder', function (Options $options) {
            return function (ProjectRepository $repo) use ($options) {
                $query = new ProjectFormTypeQuery($options['projects'], $options['customers']);
                if ($options['query_builder_for_user'] === true) {
                    $query->setUser($options['user']);
                }

                if ($options['ignore_date'] === true) {
                    $query->setIgnoreDate(true);
                } else {
                    if ($options['project_date_start'] !== null) {
                        $query->setProjectStart($options['project_date_start']);
                    }
                    if ($options['project_date_end'] !== null) {
                        $query->setProjectEnd($options['project_date_end']);
                    }
                }

                if ($options['join_customer'] === true) {
                    $query->setWithCustomer(true);
                }

                if ($options['ignore_project'] !== null) {
                    $query->setProjectToIgnore($options['ignore_project']);
                }

                return $repo->getQueryBuilderForFormType($query);
            };
        });

        $resolver->setDefault('api_data', function (Options $options) {
            if ($options['activity_enabled'] !== false) {
                $name = \is_string($options['activity_enabled']) ? $options['activity_enabled'] : 'project';

                return [
                    'select' => $options['activity_select'],
                    'route' => 'get_activities',
                    'route_params' => [
                        $name => '%' . $name . '%',
                        'visible' => $options['activity_visibility'],
                    ],
                    'empty_route_params' => [
                        'globals' => 'true',
                        'visible' => $options['activity_visibility'],
                    ],
                ];
            }

            return [];
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-option-pattern' => $this->projectHelper->getChoicePattern(),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
