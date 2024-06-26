<?php

declare(strict_types=1);

namespace App\Crm\Transport\Form\Type;

use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Repository\CustomerRepository;
use App\Crm\Domain\Repository\Query\CustomerFormTypeQuery;
use App\Crm\Domain\Repository\Query\ProjectQuery;
use App\Crm\Transport\Form\Helper\CustomerHelper;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @package App\Form\Type
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class CustomerType extends AbstractType
{
    public function __construct(
        private readonly CustomerHelper $customerHelper
    ) {
    }

    public function getChoiceLabel(Customer $customer): string
    {
        return $this->customerHelper->getChoiceLabel($customer);
    }

    public function getChoiceAttributes(Customer $customer, $key, $value): array
    {
        return [
            'data-currency' => $customer->getCurrency(),
        ];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // documentation is for NelmioApiDocBundle
            'documentation' => [
                'type' => 'integer',
                'description' => 'Customer ID',
            ],
            'label' => 'customer',
            'class' => Customer::class,
            'choice_label' => [$this, 'getChoiceLabel'],
            'choice_attr' => [$this, 'getChoiceAttributes'],
            'query_builder_for_user' => true,
            'project_enabled' => false,
            'project_select' => 'project',
            'start_date_param' => '%begin_date%',
            'end_date_param' => '%end_date%',
            'ignore_date' => false,
            'project_visibility' => ProjectQuery::SHOW_VISIBLE,
            // @var Customer|null
            'ignore_customer' => null,
            // @var Customer|Customer[]|null
            'customers' => null,
            'pre_select_customer' => false,
        ]);

        $resolver->setDefault('query_builder', function (Options $options) {
            return function (CustomerRepository $repo) use ($options) {
                $query = new CustomerFormTypeQuery($options['customers']);

                if ($options['pre_select_customer'] === true) {
                    $query->setAllowCustomerPreselect(true);
                }

                if ($options['query_builder_for_user'] === true) {
                    $query->setUser($options['user']);
                }

                if ($options['ignore_customer'] !== null) {
                    $query->setCustomerToIgnore($options['ignore_customer']);
                }

                return $repo->getQueryBuilderForFormType($query);
            };
        });

        $resolver->setDefault('api_data', function (Options $options) {
            if ($options['project_enabled'] !== false) {
                $name = \is_string($options['project_enabled']) ? $options['project_enabled'] : 'customer';
                $routeParams = [
                    $name => '%' . $name . '%',
                    'visible' => $options['project_visibility'],
                ];
                $emptyRouteParams = [
                    'visible' => $options['project_visibility'],
                ];

                if (!$options['ignore_date']) {
                    if (!empty($options['start_date_param'])) {
                        $routeParams['start'] = $options['start_date_param'];
                        $emptyRouteParams['start'] = $options['start_date_param'];
                    }

                    if (!empty($options['end_date_param'])) {
                        $routeParams['end'] = $options['end_date_param'];
                        $emptyRouteParams['end'] = $options['end_date_param'];
                    }
                } else {
                    $routeParams['ignoreDates'] = 1;
                    $emptyRouteParams['ignoreDates'] = 1;
                }

                return [
                    'reload' => 'get_customers',
                    'select' => $options['project_select'],
                    'route' => 'get_projects',
                    'route_params' => $routeParams,
                    'empty_route_params' => $emptyRouteParams,
                ];
            }

            return [];
        });
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['attr'] = array_merge($view->vars['attr'], [
            'data-option-pattern' => $this->customerHelper->getChoicePattern(),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
