<?php

declare(strict_types=1);

namespace App\Crm\Transport\API;

use App\Crm\Application\Service\Timesheet\DateTimeFactory;
use App\Crm\Domain\Repository\Query\BaseQuery;
use App\User\Domain\Entity\User;
use App\Utils\Pagination;
use FOS\RestBundle\View\View;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;

/**
 * @method null|User getUser()
 */
abstract class BaseApiController extends AbstractController
{
    public const DATE_ONLY_FORMAT = 'yyyy-MM-dd';
    public const DATE_FORMAT = DateTimeType::HTML5_FORMAT;
    public const DATE_FORMAT_PHP = 'Y-m-d\TH:i:s';

    /**
     * @template TFormType of FormTypeInterface<TData>
     * @template TData of BaseQuery
     * @param class-string<TFormType> $type
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @return FormInterface<BaseQuery>
     */
    protected function createSearchForm(string $type, BaseQuery $data, array $options = []): FormInterface
    {
        return $this->container
            ->get('form.factory')
            ->createNamed('', $type, $data, array_merge([
                'method' => 'GET',
            ], $options));
    }

    protected function getDateTimeFactory(?User $user = null): DateTimeFactory
    {
        if ($user === null) {
            $user = $this->getUser();
        }

        return DateTimeFactory::createByUser($user);
    }

    protected function addPagination(View $view, Pagination $pagination): void
    {
        $view->setHeader('X-Page', (string)$pagination->getCurrentPage());
        $view->setHeader('X-Total-Count', (string)$pagination->getNbResults());
        $view->setHeader('X-Total-Pages', (string)$pagination->getNbPages());
        $view->setHeader('X-Per-Page', (string)$pagination->getMaxPerPage());
    }
}
