<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Reporting;

use App\Admin\Crm\Controller\AbstractController;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Reporting\ProjectDetails\ProjectDetailsForm;
use App\Reporting\ProjectDetails\ProjectDetailsQuery;
use App\Utils\PageSetup;
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
final class ProjectDetailsController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/reporting/project_details', name: 'report_project_details', methods: ['GET'])]
    #[IsGranted('report:project')]
    #[IsGranted(new Expression("is_granted('details', 'project')"))]
    public function __invoke(Request $request, ProjectStatisticService $service): Response
    {
        $dateFactory = $this->getDateTimeFactory();
        $user = $this->getUser();

        $query = new ProjectDetailsQuery($dateFactory->createDateTime(), $user);
        $form = $this->createFormForGetRequest(ProjectDetailsForm::class, $query);
        $form->submit($request->query->all(), false);

        $projectView = null;
        $projectDetails = null;
        $project = $query->getProject();

        if ($project !== null && $this->isGranted('details', $project)) {
            $projectViews = $service->getProjectView($user, [$project], $query->getToday());
            $projectView = $projectViews[0];
            $projectDetails = $service->getProjectsDetails($query);
        }

        $page = new PageSetup('projects');
        $page->setHelp('project.html');

        if ($project !== null) {
            $page->setActionName('project');
            $page->setActionView('project_details_report');
            $page->setActionPayload([
                'project' => $project,
            ]);
        }

        return $this->render('reporting/project_details.html.twig', [
            'page_setup' => $page,
            'report_title' => 'report_project_details',
            'project' => $project,
            'project_view' => $projectView,
            'project_details' => $projectDetails,
            'form' => $form->createView(),
            'now' => $this->getDateTimeFactory()->createDateTime(),
        ]);
    }
}
