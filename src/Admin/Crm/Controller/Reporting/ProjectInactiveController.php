<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Admin\Crm\Controller\AbstractController;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Reporting\ProjectInactive\ProjectInactiveForm;
use App\Reporting\ProjectInactive\ProjectInactiveQuery;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Admin\Crm\Controller\Reporting
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class ProjectInactiveController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/reporting/project_inactive', name: 'report_project_inactive', methods: ['GET', 'POST'])]
    #[IsGranted('report:project')]
    #[IsGranted(new Expression("is_granted('budget_any', 'project')"))]
    public function __invoke(Request $request, ProjectStatisticService $service): Response
    {
        $dateFactory = $this->getDateTimeFactory();
        $user = $this->getUser();
        $now = $dateFactory->createDateTime();

        $query = new ProjectInactiveQuery($dateFactory->createDateTime('-1 year'), $user);
        $form = $this->createFormForGetRequest(ProjectInactiveForm::class, $query, [
            'timezone' => $user->getTimezone(),
        ]);
        $form->submit($request->query->all(), false);

        $projects = $service->findInactiveProjects($query);
        $entries = $service->getProjectView($user, $projects, $now);

        $byCustomer = [];
        foreach ($entries as $entry) {
            $customer = $entry->getProject()->getCustomer();
            if (!isset($byCustomer[$customer->getId()])) {
                $byCustomer[$customer->getId()] = [
                    'customer' => $customer,
                    'projects' => [],
                ];
            }
            $byCustomer[$customer->getId()]['projects'][] = $entry;
        }

        return $this->render('reporting/project_inactive.html.twig', [
            'entries' => $byCustomer,
            'form' => $form->createView(),
            'report_title' => 'report_inactive_project',
            'tableName' => 'inactive_project_reporting',
            'now' => $now,
            'skipColumns' => ['today', 'week', 'month', 'projectStart', 'projectEnd', 'comment'],
        ]);
    }
}
