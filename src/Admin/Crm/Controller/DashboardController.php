<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Domain\Entity\Bookmark;
use App\Crm\Domain\Repository\BookmarkRepository;
use App\Crm\Transport\Event\DashboardEvent;
use App\User\Domain\Entity\User;
use App\Utils\PageSetup;
use App\Widget\WidgetInterface;
use App\Widget\WidgetService;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function count;

/**
 * Dashboard controller for the admin area.
 */
#[Route(path: '/dashboard')]
#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
final class DashboardController extends AbstractController
{
    public const string BOOKMARK_TYPE = 'dashboard';
    public const string BOOKMARK_NAME = 'default';
    /**
     * @var WidgetInterface[]|null
     */
    private ?array $widgets = null;

    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly WidgetService $service,
        private readonly BookmarkRepository $repository
    ) {
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/', name: 'dashboard', defaults: [], methods: ['GET'])]
    public function index(): Response
    {
        $user = $this->getUser();
        $available = $this->getAllAvailableWidgets($user);
        $widgets = $this->filterWidgets($available, $user);

        $page = new PageSetup('dashboard.title');
        $page->setHelp('dashboard.html');
        $page->setActionName('dashboard');
        $page->setActionPayload([
            'widgets' => $widgets,
            'available' => $available,
        ]);

        return $this->render('dashboard/index.html.twig', [
            'page_setup' => $page,
            'widgets' => $widgets,
            'available' => $available,
        ]);
    }

    #[Route(path: '/reset/', name: 'dashboard_reset', defaults: [], methods: ['GET', 'POST'])]
    public function reset(): RedirectResponse
    {
        $bookmark = $this->getBookmark($this->getUser());
        if ($bookmark !== null) {
            $this->repository->deleteBookmark($bookmark);
        }

        return $this->redirectToRoute('dashboard');
    }

    #[Route(path: '/add-widget/{widget}', name: 'dashboard_add', defaults: [], methods: ['GET'])]
    public function add(string $widget): Response
    {
        $user = $this->getUser();

        $widgets = $this->getUserConfig($user);

        // prevent to add the same widget multiple times
        foreach ($widgets as $id => $setting) {
            if ($setting['id'] === $widget) {
                return $this->redirectToRoute('dashboard_edit');
            }
        }

        $widgets[] = [
            'id' => $widget,
            'options' => [],
        ];

        $this->saveBookmark($user, $widgets);

        return $this->redirectToRoute('dashboard_edit');
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/edit/', name: 'dashboard_edit', defaults: [], methods: ['GET', 'POST'])]
    public function edit(Request $request): Response
    {
        $user = $this->getUser();

        $available = $this->getAllAvailableWidgets($user);
        $widgets = $this->filterWidgets($available, $user);

        $choices = [];

        // the list of widgets for the dropdown is created in the EventListener and not here
        // this form is mainly used for saving
        foreach ($available as $widget) {
            if (empty($widget->getTitle())) {
                continue;
            }
            $choices[$widget->getId()] = $widget->getId();
        }

        $form = $this->createFormBuilder(null, [])
            ->add('widgets', ChoiceType::class, [
                'choices' => $choices,
                'multiple' => true,
            ])
            ->setAction($this->generateUrl('dashboard_edit'))
            ->setMethod('POST')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $userWidgets = $this->getUserConfig($user);
                $saveWidgets = [];
                foreach ($form->getData()['widgets'] as $widgetId) {
                    $options = [];
                    foreach ($userWidgets as $setting) {
                        if ($setting['id'] === $widgetId) {
                            $options = $setting['options'];
                        }
                    }
                    $saveWidgets[] = [
                        'id' => $widgetId,
                        'options' => $options,
                    ];
                }

                $this->saveBookmark($user, $saveWidgets);

                $this->flashSuccess('action.update.success');

                return $this->redirectToRoute('dashboard');
            } catch (Exception $ex) {
                $this->flashDeleteException($ex);
            }
        }

        $page = new PageSetup('dashboard.title');
        $page->setHelp('dashboard.html');
        $page->setActionName('dashboard');
        $page->setActionView('edit');
        $page->setActionPayload([
            'widgets' => $widgets,
            'available' => $available,
        ]);

        return $this->render('dashboard/grid.html.twig', [
            'page_setup' => $page,
            'widgets' => $widgets,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @return array<WidgetInterface>
     * @throws Exception
     */
    private function getAllAvailableWidgets(User $user): array
    {
        if ($this->widgets === null) {
            $all = [];
            foreach ($this->service->getAllWidgets() as $widget) {
                $widget->setUser($user);

                $permissions = $widget->getPermissions();
                if (count($permissions) > 0) {
                    $add = false;
                    foreach ($permissions as $perm) {
                        if ($this->isGranted($perm)) {
                            $add = true;
                            break;
                        }
                    }

                    if (!$add) {
                        continue;
                    }
                }
                $all[] = $widget;
            }
            $this->widgets = $all;
        }

        return $this->widgets;
    }

    private function getBookmark(User $user): ?Bookmark
    {
        return $this->repository->findBookmark($user, self::BOOKMARK_TYPE, self::BOOKMARK_NAME);
    }

    private function getDefaultConfig(): array
    {
        $event = new DashboardEvent($this->getUser());

        // default widgets
        $dashboard = [
            'PaginatedWorkingTimeChart',
            //'UserAmountToday',
            //'UserAmountWeek',
            //'UserAmountMonth',
            //'UserAmountYear',
            //'UserTeams',
            //'UserTeamProjects',
            'DurationToday',
            'DurationWeek',
            'DurationMonth',
            'DurationYear',
            //'ActiveUsersToday',
            //'ActiveUsersWeek',
            //'ActiveUsersMonth',
            //'ActiveUsersYear',
            //'AmountToday',
            //'AmountWeek',
            //'AmountMonth',
            //'AmountYear',
            //'TotalsUser',
            //'TotalsCustomer',
            //'TotalsProject',
            //'TotalsActivity',
        ];

        foreach ($dashboard as $widgetName) {
            $event->addWidget($widgetName);
        }

        $this->eventDispatcher->dispatch($event);

        return $event->getWidgets();
    }

    /**
     * Returns the list of widgets names and options for a user.
     *
     * @return array<int, array<string, mixed>>
     */
    private function getUserConfig(User $user): array
    {
        $bookmark = $this->getBookmark($user);
        if ($bookmark !== null) {
            return $bookmark->getContent();
        }

        $widgets = [];

        foreach ($this->getDefaultConfig() as $name) {
            $widgets[] = [
                'id' => $name,
                'options' => [],
            ];
        }

        return $widgets;
    }

    /**
     * @param array<WidgetInterface> $widgets
     * @return array<WidgetInterface>
     */
    private function filterWidgets(array $widgets, User $user): array
    {
        $filteredWidgets = [];

        foreach ($this->getUserConfig($user) as $setting) {
            $id = $setting['id'];
            $options = $setting['options'];
            foreach ($widgets as $widget) {
                if ($widget->getId() === $id) {
                    $tmpWidget = clone $widget;
                    foreach ($options as $key => $value) {
                        $tmpWidget->setOption($key, $value);
                    }
                    $filteredWidgets[] = $tmpWidget;
                    break;
                }
            }
        }

        return $filteredWidgets;
    }

    private function saveBookmark(User $user, array $widgets): void
    {
        $bookmark = $this->getBookmark($user);
        if ($bookmark === null) {
            $bookmark = new Bookmark();
            $bookmark->setUser($user);
            $bookmark->setType(self::BOOKMARK_TYPE);
            $bookmark->setName(self::BOOKMARK_NAME);
        }
        $bookmark->setContent($widgets);

        $this->repository->saveBookmark($bookmark);
    }
}
