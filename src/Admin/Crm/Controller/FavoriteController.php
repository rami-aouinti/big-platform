<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Application\Service\Timesheet\FavoriteRecordService;
use App\Crm\Domain\Entity\Timesheet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Admin\Crm\Controller
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/favorite')]
#[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
final class FavoriteController extends AbstractController
{
    #[Route(path: '/timesheet/', name: 'favorites_timesheets', methods: ['GET'])]
    #[IsGranted('start_own_timesheet')]
    public function favoriteAction(): Response
    {
        return $this->render('favorite/index.html.twig');
    }

    #[Route(path: '/timesheet/add/{id}', name: 'favorites_timesheets_add', methods: ['GET'])]
    #[IsGranted('start_own_timesheet')]
    public function add(Timesheet $timesheet, FavoriteRecordService $favoriteRecordService): Response
    {
        $favoriteRecordService->addFavorite($timesheet);

        return $this->render('favorite/index.html.twig');
    }

    #[Route(path: '/timesheet/remove/{id}', name: 'favorites_timesheets_remove', methods: ['GET'])]
    #[IsGranted('start_own_timesheet')]
    public function remove(Timesheet $timesheet, FavoriteRecordService $favoriteRecordService): Response
    {
        $favoriteRecordService->removeFavorite($timesheet);

        return $this->render('favorite/index.html.twig');
    }
}
