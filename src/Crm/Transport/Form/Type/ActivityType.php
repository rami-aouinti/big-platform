<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\Activity;
use App\Crm\Domain\Repository\ActivityRepository;
use App\Crm\Domain\Repository\Query\ActivityFormTypeQuery;
use App\Crm\Transport\Form\Helper\ActivityHelper;
use App\Crm\Transport\Form\Helper\ProjectHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Custom form field type to select an activity.
 */
final class ActivityType extends AbstractType
{
    public function __construct(
        private readonly ActivityHelper $activityHelper,
        private readonly ProjectHelper $projectHelper
    ) {
    }

    public function getChoiceLabel(Activity $activity): string
    {
        return $this->activityHelper->getChoiceLabel($activity);
    }

    public function groupBy(Activity $activity, $key, $index): ?string
    {
        if ($activity->getProject() === null) {
            return null;
        }

        return $this->projectHelper->getChoiceLabel($activity->getProject());
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return array<string, string|int|null>
     */
    public function getChoiceAttributes(Activity $activity, $key, $value): array
    {
        if (null !== ($project = $activity->getProject())) {
            return [
                'data-project' => $project->getId(),
                'data-currency' => $project->getCustomer()?->getCurrency(),
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
                'description' => 'Activity ID',
            ],
            'label' => 'activity',
            'class' => Activity::class,
            'choice_label' => [$this, 'getChoiceLabel'],
            'choice_attr' => [$this, 'getChoiceAttributes'],
            'group_by' => [$this, 'groupBy'],
            'query_builder_for_user' => true,
            // @var Project|Project[]|int|int[]|null
            'projects' => null,
            // @var Activity|Activity[]|int|int[]|null
            'activities' => null,
            // @var Activity|null
            'ignore_activity' => null,
            'allow_create' => false,
        ]);

        $resolver->setDefault('api_data', function (Options $options) {
            if ($options['allow_create'] !== false) {
                return [
                    'create' => 'post_activity',
                ];
            }

            return [];
        });

        $resolver->setDefault('query_builder', function (Options $options) {
            return function (ActivityRepository $repo) use ($options) {
                $query = new ActivityFormTypeQuery($options['activities'], $options['projects']);

                if ($options['query_builder_for_user'] === true) {
                    $query->setUser($options['user']);
                }

                if ($options['ignore_activity'] !== null) {
                    $query->setActivityToIgnore($options['ignore_activity']);
                }

                return $repo->getQueryBuilderForFormType($query);
            };
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-option-pattern' => $this->activityHelper->getChoicePattern(),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
