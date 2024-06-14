<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Admin\Crm\Controller
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/widgets')]
#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
final class WidgetController extends AbstractController
{
    #[Route(path: '/working-time/{year}/{week}', name: 'widgets_working_time_chart', requirements: [
        'year' => '[1-9]\d*',
        'week' => '[0-9]\d*',
    ], methods: ['GET'])]
    #[IsGranted('view_own_timesheet')]
    public function workingtimechartAction($year, $week): Response
    {
        return $this->render('widget/paginatedworkingtimechart.html.twig', [
            'user' => $this->getUser(),
            'year' => $year,
            'week' => $week,
        ]);
    }
}
