<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Admin\Crm\Controller\AbstractController;
use App\Configuration\SystemConfiguration;
use App\Crm\Application\Model\MonthlyStatistic;
use App\Crm\Application\Reporting\YearlyUserList\YearlyUserList;
use App\Crm\Application\Reporting\YearlyUserList\YearlyUserListForm;
use App\Crm\Application\Service\Timesheet\TimesheetStatisticService;
use App\Crm\Domain\Repository\Query\TimesheetStatisticQuery;
use App\Crm\Domain\Repository\Query\UserQuery;
use App\Crm\Domain\Repository\Query\VisibilityInterface;
use App\Crm\Domain\Repository\UserRepository;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\BinaryFileResponseWriter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\XlsxWriter;
use DateTime;
use PhpOffice\PhpSpreadsheet\Reader\Exception;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Admin\Crm\Controller\Reporting
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/reporting/users')]
#[IsGranted('report:other')]
final class ReportUsersYearController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/year', name: 'report_yearly_users', methods: ['GET', 'POST'])]
    public function report(Request $request, SystemConfiguration $systemConfiguration, TimesheetStatisticService $statisticService, UserRepository $userRepository): Response
    {
        return $this->render(
            'reporting/report_user_list_monthly.html.twig',
            $this->getData($request, $systemConfiguration, $statisticService, $userRepository)
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/year_export', name: 'report_yearly_users_export', methods: ['GET', 'POST'])]
    public function export(Request $request, SystemConfiguration $systemConfiguration, TimesheetStatisticService $statisticService, UserRepository $userRepository): Response
    {
        $data = $this->getData($request, $systemConfiguration, $statisticService, $userRepository);

        $content = $this->renderView('reporting/report_user_list_monthly_export.html.twig', $data);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);

        $writer = new BinaryFileResponseWriter(new XlsxWriter(), 'kimai-export-users-yearly');

        return $writer->getFileResponse($spreadsheet);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    private function getData(Request $request, SystemConfiguration $systemConfiguration, TimesheetStatisticService $statisticService, UserRepository $userRepository): array
    {
        $currentUser = $this->getUser();
        $dateTimeFactory = $this->getDateTimeFactory();

        $defaultDate = $dateTimeFactory->createStartOfYear();

        $financialYear = $systemConfiguration->getFinancialYearStart();
        if (null !== ($financialYear)) {
            $defaultDate = $this->getDateTimeFactory()->createStartOfFinancialYear($financialYear);
        }

        $values = new YearlyUserList();
        $values->setDate(clone $defaultDate);

        $form = $this->createFormForGetRequest(YearlyUserListForm::class, $values, [
            'timezone' => $dateTimeFactory->getTimezone()->getName(),
            'start_date' => $values->getDate(),
        ]);

        $form->submit($request->query->all(), false);

        $query = new UserQuery();
        $query->setVisibility(VisibilityInterface::SHOW_BOTH);
        $query->setSystemAccount(false);
        $query->setCurrentUser($currentUser);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                $values->setDate(clone $defaultDate);
            } else {
                if ($values->getTeam() !== null) {
                    $query->setSearchTeams([$values->getTeam()]);
                }
            }
        }

        $allUsers = $userRepository->getUsersForQuery($query);

        if ($values->getDate() === null) {
            $values->setDate(clone $defaultDate);
        }

        /** @var DateTime $start */
        $start = $values->getDate();

        // there is a potential edge case bug for financial years:
        // the last month will be skipped, if the financial year started on a different day than the first
        $end = $dateTimeFactory->createEndOfFinancialYear($start);

        $monthStats = [];
        $hasData = true;

        if (!empty($allUsers)) {
            $statsQuery = new TimesheetStatisticQuery($start, $end, $allUsers);
            $statsQuery->setProject($values->getProject());
            $monthStats = $statisticService->getMonthlyStats($statsQuery);
        }

        if (empty($monthStats)) {
            $monthStats = [new MonthlyStatistic($start, $end, $currentUser)];
            $hasData = false;
        }

        return [
            'subReportDate' => $values->getDate(),
            'period_attribute' => 'months',
            'dataType' => $values->getSumType(),
            'report_title' => 'report_yearly_users',
            'box_id' => 'yearly-user-list-reporting-box',
            'export_route' => 'report_yearly_users_export',
            'decimal' => $values->isDecimal(),
            'form' => $form->createView(),
            'stats' => $monthStats,
            'hasData' => $hasData,
        ];
    }
}
