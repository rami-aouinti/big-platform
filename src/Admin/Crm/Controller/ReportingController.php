<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Application\Reporting\ReportingService;
use App\Crm\Application\Utils\PageSetup;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller used to render reports.
 */
#[Route(path: '/reporting')]
#[IsGranted('view_reporting')]
final class ReportingController extends AbstractController
{
    #[Route(path: '/', name: 'reporting', methods: ['GET'])]
    public function defaultReport(ReportingService $reportingService): Response
    {
        $page = new PageSetup('menu.reporting');
        $page->setHelp('reporting.html');

        return $this->render('reporting/index.html.twig', [
            'page_setup' => $page,
            'reports' => $reportingService->getAvailableReports($this->getUser()),
        ]);
    }
}
