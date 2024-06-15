<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Configuration\LocaleService;
use App\Crm\Domain\Repository\UserRepository;
use App\Crm\Transport\Event\ConfigureMainMenuEvent;
use App\User\Domain\Entity\User;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function count;
use function is_string;

/**
 * Homepage controller is a redirect controller with user specific logic.
 */
#[Route(path: '/homepage')]
#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
final class HomepageController extends AbstractController
{
    public const string DEFAULT_ROUTE = 'timesheet';

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '', name: 'homepage', defaults: [], methods: ['GET'])]
    public function homepage(
        Request $request,
        LocaleService $service,
        EventDispatcherInterface $eventDispatcher,
        UserRepository $userRepository
    ): Response {
        $user = $this->getUser();

        $userLanguage = $user->getLanguage();
        $requestLanguage = $request->getLocale();

        if (empty($requestLanguage)) {
            $requestLanguage = User::DEFAULT_LANGUAGE;
        }

        if (empty($userLanguage)) {
            $userLanguage = $requestLanguage;
        }

        // if a user somehow managed to get a wrong locale into hos account (eg. an imported user from Kimai 1)
        // make sure that he will still see a beautiful page and not a 404
        if (!$service->isKnownLocale($userLanguage->value)) {
            $userLanguage = 'en';
        }

        $routes = [];

        $userRoute = $user->getPreferenceValue('login_initial_view');
        if (is_string($userRoute)) {
            $event = new ConfigureMainMenuEvent();
            $eventDispatcher->dispatch($event);
            $menu = $event->findById($userRoute);
            if ($menu !== null && count($menu->getRouteArgs()) === 0 && $menu->getRoute() !== null) {
                $userRoute = $menu->getRoute();
            }
            $routes[] = [$userRoute, $userLanguage];
            $routes[] = [$userRoute, $requestLanguage];
            $routes[] = [$userRoute, User::DEFAULT_LANGUAGE];
        }

        $routes[] = [self::DEFAULT_ROUTE, $userLanguage];
        $routes[] = [self::DEFAULT_ROUTE, $requestLanguage];

        foreach ($routes as $routeSettings) {
            $route = $routeSettings[0];
            $language = $routeSettings[1];

            try {
                return $this->redirectToRoute($route, [
                    '_locale' => $language->value,
                ]);
            } catch (Exception $ex) {
                if ($route === $userRoute) {
                    // fix invalid routes from old plugins / versions
                    $user->setPreferenceValue('login_initial_view', 'dashboard');
                    $userRepository->saveUser($user);
                } else {
                    $this->logException($ex);
                }
            }
        }

        return $this->redirectToRoute(self::DEFAULT_ROUTE, [
            '_locale' => User::DEFAULT_LANGUAGE,
        ]);
    }
}
