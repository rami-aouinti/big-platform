<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Application\Service\WorkingTime\Model\BoxConfiguration;
use App\Crm\Application\Service\WorkingTime\WorkingTimeService;
use App\Crm\Transport\Event\WorkContractDetailControllerEvent;
use App\Crm\Transport\Form\ContractByUserForm;
use App\Reporting\YearByUser\YearByUser;
use App\User\Domain\Entity\User;
use App\Utils\PageSetup;
use DateTime;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @package App\Admin\Crm\Controller
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ContractController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/contract', name: 'user_contract', methods: ['GET', 'POST'])]
    public function __invoke(
        Request $request,
        WorkingTimeService $workingTimeService,
        EventDispatcherInterface $eventDispatcher
    ): Response {
        $currentUser = $this->getUser();
        $dateTimeFactory = $this->getDateTimeFactory($currentUser);
        $canChangeUser = $this->isGranted('contract_other_profile');
        $defaultDate = $dateTimeFactory->createStartOfYear();
        $now = $dateTimeFactory->createDateTime();

        $values = new YearByUser();
        $values->setUser($currentUser);
        $values->setDate($defaultDate);

        $form = $this->createFormForGetRequest(ContractByUserForm::class, $values, [
            'include_user' => $canChangeUser,
            'timezone' => $dateTimeFactory->getTimezone()->getName(),
            'start_date' => $values->getDate(),
        ]);

        $form->submit($request->query->all(), false);

        if ($values->getUser() === null) {
            $values->setUser($currentUser);
        }

        /** @var User $profile */
        $profile = $values->getUser();
        if ($this->getUser() !== $profile && !$canChangeUser) {
            throw $this->createAccessDeniedException('Cannot access user contract settings');
        }

        if ($values->getDate() === null) {
            $values->setDate(clone $defaultDate);
        }

        /** @var DateTime $yearDate */
        $yearDate = $values->getDate();
        $year = $workingTimeService->getYear($profile, $yearDate, $now);

        $page = new PageSetup('work_times');
        $page->setHelp('contract.html');
        $page->setActionName('contract');
        $page->setActionPayload([
            'profile' => $profile,
            'year' => $yearDate,
        ]);
        $page->setPaginationForm($form);

        // additional boxes by plugins
        $controllerEvent = new WorkContractDetailControllerEvent($year);
        $eventDispatcher->dispatch($controllerEvent);

        $summary = $workingTimeService->getYearSummary($year, $now);

        $boxConfiguration = new BoxConfiguration();
        $boxConfiguration->setDecimal(false);
        $boxConfiguration->setCollapsed($profile->hasWorkHourConfiguration() && $summary->count() > 0);

        return $this->render('contract/status.html.twig', [
            'box_configuration' => $boxConfiguration,
            'page_setup' => $page,
            'decimal' => $boxConfiguration->isDecimal(),
            'summaries' => $summary,
            'now' => $now,
            'boxes' => $controllerEvent->getController(),
            'year' => $year,
            'user' => $profile,
            'form' => $form->createView(),
        ]);
    }
}
