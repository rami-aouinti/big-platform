<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Configuration\SystemConfiguration;
use App\Crm\Application\Service\Calendar\CalendarService;
use App\Crm\Application\Service\Timesheet\TrackingModeService;
use App\Crm\Application\Utils\PageSetup;
use App\Crm\Transport\Form\CalendarForm;
use App\User\Domain\Entity\User;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use function count;

/**
 * @package App\Admin\Crm\Controller
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/calendar')]
#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
final class CalendarController extends AbstractController
{
    public function __construct(
        private readonly CalendarService $calendarService,
        private readonly SystemConfiguration $configuration,
        private readonly TrackingModeService $service
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/', name: 'calendar', methods: ['GET'])]
    #[Route(path: '/{profile}', name: 'calendar_user', methods: ['GET'])]
    public function userCalendar(Request $request): Response
    {
        $form = null;
        $profile = $this->getUser();

        if ($this->isGranted('view_other_timesheet')) {
            $form = $this->createFormForGetRequest(CalendarForm::class, [
                'user' => $profile,
            ], [
                'action' => $this->generateUrl('calendar'),
            ]);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $values = $form->getData();
                if ($values['user'] instanceof User) {
                    $profile = $values['user'];
                }
            }

            $form = $form->createView();

            // hide if the current user is the only available one
            if (count($form->offsetGet('user')->vars['choices']) < 2) {
                $form = null;
                $profile = $this->getUser();
            }
        }

        $mode = $this->service->getActiveMode();
        $factory = $this->getDateTimeFactory();

        // if now is default time, we do not pass it on, so it can be re-calculated for each new entry
        $defaultStart = null;
        if ($this->configuration->getTimesheetDefaultBeginTime() !== 'now') {
            $defaultStart = $factory->createDateTime($this->configuration->getTimesheetDefaultBeginTime());
            $defaultStart = $defaultStart->format('H:i:s');
        }

        $config = $this->calendarService->getConfiguration();

        $isPunchMode = !$mode->canEditDuration() && !$mode->canEditBegin() && !$mode->canEditEnd();
        $dragAndDrop = [];

        if ($mode->canEditBegin()) {
            try {
                $dragAndDrop = $this->calendarService->getDragAndDropResources($profile);
            } catch (Exception $ex) {
                $this->logException($ex);
            }
        }

        $page = new PageSetup('calendar');
        $page->setHelp('calendar.html');

        return $this->render('calendar/user.html.twig', [
            'page_setup' => $page,
            'form' => $form,
            'user' => $profile,
            'config' => $config,
            'dragAndDrop' => $dragAndDrop,
            'google' => $this->calendarService->getGoogleSources($profile),
            'sources' => $this->calendarService->getSources($profile),
            'now' => $factory->createDateTime(),
            'defaultStartTime' => $defaultStart,
            'is_punch_mode' => $isPunchMode,
            'can_edit_begin' => $mode->canEditBegin(),
            'can_edit_end' => $mode->canEditBegin(),
            'can_edit_duration' => $mode->canEditDuration(),
        ]);
    }
}
