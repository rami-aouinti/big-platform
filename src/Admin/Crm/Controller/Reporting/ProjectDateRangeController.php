<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Admin\Crm\Controller\AbstractController;
use App\Crm\Application\Reporting\ProjectDateRange\ProjectDateRangeForm;
use App\Crm\Application\Reporting\ProjectDateRange\ProjectDateRangeQuery;
use App\Crm\Application\Service\Invoice\Customer;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Transport\Form\Model\DateRange;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class ProjectDateRangeController extends AbstractController
{
    #[Route(path: '/reporting/project_daterange', name: 'report_project_daterange', methods: ['GET', 'POST'])]
    #[IsGranted('report:project')]
    #[IsGranted(new Expression("is_granted('budget_any', 'project')"))]
    public function __invoke(Request $request, ProjectStatisticService $service): Response
    {
        $dateFactory = $this->getDateTimeFactory();
        $user = $this->getUser();

        $defaultStart = $dateFactory->getStartOfMonth();
        $query = new ProjectDaterangeQuery($defaultStart, $user);
        $form = $this->createFormForGetRequest(ProjectDateRangeForm::class, $query, [
            'timezone' => $user->getTimezone(),
        ]);
        $form->submit($request->query->all(), false);

        $begin = $query->getMonth() ?? $defaultStart;

        $dateRange = new DateRange(true);
        $dateRange->setBegin($begin);
        $end = $dateFactory->getEndOfMonth($dateRange->getBegin()); // this resets the time

        $dateRange->setEnd($end);

        $projects = $service->findProjectsForDateRange($query, $dateRange);
        $entries = $service->getBudgetStatisticModelForProjectsByDateRange($projects, $begin, $end, $end);

        $byCustomer = [];
        foreach ($entries as $entry) {
            /** @var Customer $customer */
            $customer = $entry->getProject()->getCustomer();
            if (!isset($byCustomer[$customer->getId()])) {
                $byCustomer[$customer->getId()] = [
                    'customer' => $customer,
                    'projects' => [],
                ];
            }
            $byCustomer[$customer->getId()]['projects'][] = $entry;
        }

        return $this->render('reporting/project_daterange.html.twig', [
            'report_title' => 'report_project_daterange',
            'entries' => $byCustomer,
            'form' => $form->createView(),
            'queryEnd' => $dateRange->getEnd(),
        ]);
    }
}
