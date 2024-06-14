<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Admin\Crm\Controller\AbstractController;
use App\Crm\Application\Reporting\ProjectView\ProjectViewForm;
use App\Crm\Application\Reporting\ProjectView\ProjectViewQuery;
use App\Crm\Application\Service\Project\ProjectStatisticService;
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
final class ProjectViewController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/reporting/project_view', name: 'report_project_view', methods: ['GET', 'POST'])]
    #[IsGranted('report:project')]
    #[IsGranted(new Expression("is_granted('budget_any', 'project')"))]
    public function __invoke(Request $request, ProjectStatisticService $service): Response
    {
        $dateFactory = $this->getDateTimeFactory();
        $user = $this->getUser();

        $query = new ProjectViewQuery($dateFactory->createDateTime(), $user);
        $form = $this->createFormForGetRequest(ProjectViewForm::class, $query);
        $form->submit($request->query->all(), false);

        $projects = $service->findProjectsForView($query);
        $entries = $service->getProjectView($user, $projects, $query->getToday());

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

        return $this->render('reporting/project_view.html.twig', [
            'entries' => $byCustomer,
            'form' => $form->createView(),
            'report_title' => 'report_project_view',
            'tableName' => 'project_view_reporting',
            'now' => $dateFactory->createDateTime(),
        ]);
    }
}
