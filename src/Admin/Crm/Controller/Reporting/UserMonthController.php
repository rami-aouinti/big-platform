<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Crm\Application\Model\DailyStatistic;
use App\Crm\Application\Reporting\MonthByUser\MonthByUser;
use App\Crm\Application\Reporting\MonthByUser\MonthByUserForm;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\BinaryFileResponseWriter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\XlsxWriter;
use App\User\Domain\Entity\User;
use DateTime;
use Exception;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Admin\Crm\Controller\Reporting
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/reporting/user')]
#[IsGranted('report:user')]
final class UserMonthController extends AbstractUserReportController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/month', name: 'report_user_month', methods: ['GET', 'POST'])]
    public function monthByUser(Request $request): Response
    {
        return $this->render('reporting/report_by_user.html.twig', $this->getData($request));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    #[Route(path: '/month_export', name: 'report_user_month_export', methods: ['GET', 'POST'])]
    public function export(Request $request): Response
    {
        $data = $this->getData($request);

        $content = $this->renderView('reporting/report_by_user_data.html.twig', $data);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);

        $writer = new BinaryFileResponseWriter(new XlsxWriter(), 'kimai-export-user-monthly');

        return $writer->getFileResponse($spreadsheet);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function getData(Request $request): array
    {
        $currentUser = $this->getUser();
        $dateTimeFactory = $this->getDateTimeFactory($currentUser);
        $canChangeUser = $this->canSelectUser();

        $values = new MonthByUser();
        $values->setUser($currentUser);
        $values->setDate($dateTimeFactory->getStartOfMonth());

        $form = $this->createFormForGetRequest(MonthByUserForm::class, $values, [
            'include_user' => $canChangeUser,
            'timezone' => $dateTimeFactory->getTimezone()->getName(),
            'start_date' => $values->getDate(),
        ]);

        $form->submit($request->query->all(), false);

        if ($values->getUser() === null) {
            $values->setUser($currentUser);
        }

        if ($currentUser !== $values->getUser() && !$canChangeUser) {
            throw new AccessDeniedException('User is not allowed to see other users timesheet');
        }

        if ($values->getDate() === null) {
            $values->setDate($dateTimeFactory->getStartOfMonth());
        }

        /** @var DateTime $start */
        $start = $values->getDate();
        $start->modify('first day of 00:00:00');

        $end = clone $start;
        $end->modify('last day of 23:59:59');

        /** @var User $selectedUser */
        $selectedUser = $values->getUser();

        $previousMonth = clone $start;
        $previousMonth->modify('-1 month');

        $nextMonth = clone $start;
        $nextMonth->modify('+1 month');

        $data = $this->prepareReport($start, $end, $selectedUser);

        return [
            'decimal' => $values->isDecimal(),
            'dataType' => $values->getSumType(),
            'report_title' => 'report_user_month',
            'box_id' => 'user-month-reporting-box',
            'form' => $form->createView(),
            'rows' => $data,
            'period' => new DailyStatistic($start, $end, $selectedUser),
            'user' => $selectedUser,
            'current' => $start,
            'next' => $nextMonth,
            'previous' => $previousMonth,
            'begin' => $start,
            'end' => $end,
            'export_route' => 'report_user_month_export',
        ];
    }
}
