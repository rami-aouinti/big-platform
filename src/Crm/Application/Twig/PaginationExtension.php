<?php

declare(strict_types=1);

namespace App\Crm\Application\Twig;

use App\Crm\Application\Utils\Pagination;
use App\Crm\Application\Utils\PaginationView;
use Pagerfanta\Pagerfanta;
use Pagerfanta\View\ViewInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPath;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class PaginationExtension extends AbstractExtension
{
    private ?ViewInterface $view = null;

    public function __construct(
        private UrlGeneratorInterface $router
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('pagination', [$this, 'renderPagination'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function renderPagination(Pagerfanta|Pagination $pager, array $options = []): string
    {
        if (!($pager instanceof Pagination)) {
            @trigger_error('Twig function pagination() needs an instanceof Pagination, Pagerfanta given', E_USER_DEPRECATED);
        }

        $routeGenerator = $this->createRouteGenerator($options);

        return $this->getView()->render($pager, $routeGenerator, $options);
    }

    private function getView(): ViewInterface
    {
        if ($this->view === null) {
            $this->view = new PaginationView();
        }

        return $this->view;
    }

    private function createRouteGenerator(array $options = []): \Closure
    {
        $options = array_replace([
            'routeName' => null,
            'routeParams' => [],
            'pageParameter' => '[page]',
        ], $options);

        $router = $this->router;

        if ($options['routeName'] === null) {
            throw new \Exception('Pagination is missing the "routeName" option');
        }

        $routeName = $options['routeName'];
        $routeParams = $options['routeParams'];
        $pagePropertyPath = new PropertyPath($options['pageParameter']);

        return function ($page) use ($router, $routeName, $routeParams, $pagePropertyPath) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
            $propertyAccessor->setValue($routeParams, $pagePropertyPath, $page);

            return $router->generate($routeName, $routeParams); // @phpstan-ignore-line
        };
    }
}
