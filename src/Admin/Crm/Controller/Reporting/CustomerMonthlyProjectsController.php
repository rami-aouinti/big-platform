<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Admin\Crm\Controller\AbstractController;
use App\Crm\Domain\Repository\Query\UserQuery;
use App\Crm\Domain\Repository\Query\VisibilityInterface;
use App\Crm\Domain\Repository\UserRepository;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\BinaryFileResponseWriter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\XlsxWriter;
use App\Reporting\CustomerMonthlyProjects\CustomerMonthlyProjects;
use App\Reporting\CustomerMonthlyProjects\CustomerMonthlyProjectsForm;
use App\Reporting\CustomerMonthlyProjects\CustomerMonthlyProjectsRepository;
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
#[Route(path: '/reporting/customer/monthly_projects')]
#[IsGranted('report:customer')]
#[IsGranted('report:other')]
final class CustomerMonthlyProjectsController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/view', name: 'report_customer_monthly_projects', methods: ['GET', 'POST'])]
    public function report(Request $request, CustomerMonthlyProjectsRepository $repository, UserRepository $userRepository): Response
    {
        return $this->render(
            'reporting/customer/monthly_projects.html.twig',
            $this->getData($request, $repository, $userRepository)
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws Exception
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/export', name: 'report_customer_monthly_projects_export', methods: ['GET', 'POST'])]
    public function export(Request $request, CustomerMonthlyProjectsRepository $repository, UserRepository $userRepository): Response
    {
        $data = $this->getData($request, $repository, $userRepository);

        $content = $this->render('reporting/customer/monthly_projects_export.html.twig', $data)->getContent();

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($content);

        $writer = new BinaryFileResponseWriter(new XlsxWriter(), 'kimai-export-users-monthly');

        return $writer->getFileResponse($spreadsheet);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws \Exception
     */
    private function getData(Request $request, CustomerMonthlyProjectsRepository $repository, UserRepository $userRepository): array
    {
        $currentUser = $this->getUser();
        $dateTimeFactory = $this->getDateTimeFactory();

        $query = new UserQuery();
        $query->setVisibility(VisibilityInterface::SHOW_BOTH);
        $query->setSystemAccount(false);
        $query->setCurrentUser($currentUser);
        $allUsers = $userRepository->getUsersForQuery($query);

        $values = new CustomerMonthlyProjects();
        $values->setDate($dateTimeFactory->getStartOfMonth());

        $form = $this->createFormForGetRequest(CustomerMonthlyProjectsForm::class, $values, [
            'timezone' => $dateTimeFactory->getTimezone()->getName(),
            'start_date' => $values->getDate(),
        ]);

        $form->submit($request->query->all(), false);

        if ($form->isSubmitted() && !$form->isValid()) {
            $values->setDate($dateTimeFactory->getStartOfMonth());
        }

        if ($values->getDate() === null) {
            $values->setDate($dateTimeFactory->getStartOfMonth());
        }

        $start = $values->getDate();
        $start = $dateTimeFactory->getStartOfMonth($start);
        $end = $dateTimeFactory->getEndOfMonth($start);

        $previous = clone $start;
        $previous->modify('-1 month');

        $next = clone $start;
        $next->modify('+1 month');

        $stats = $repository->getGroupedByCustomerProjectActivityUser($start, $end, $allUsers, $values->getCustomer());

        return [
            'dataType' => $values->getSumType(),
            'report_title' => 'report_customer_monthly_projects',
            'export_route' => 'report_customer_monthly_projects_export',
            'form' => $form->createView(),
            'current' => $start,
            'next' => $next,
            'previous' => $previous,
            'decimal' => $values->isDecimal(),
            'stats' => $stats,
        ];
    }
}
