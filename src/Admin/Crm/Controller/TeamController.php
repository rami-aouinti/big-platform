<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Application\Utils\DataTable;
use App\Crm\Application\Utils\PageSetup;
use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Repository\Query\TeamQuery;
use App\Crm\Domain\Repository\TeamRepository;
use App\Crm\Transport\Form\TeamEditForm;
use App\Crm\Transport\Form\Toolbar\TeamToolbarForm;
use App\Crm\Transport\Form\Type\CustomerType;
use App\Crm\Transport\Form\Type\ProjectType;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * @package App\Admin\Crm\Controller
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/admin/teams')]
#[IsGranted('view_team')]
final class TeamController extends AbstractController
{
    public function __construct(
        private readonly TeamRepository $repository
    ) {
    }

    /**
     * @param int            $page
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/', name: 'admin_team', defaults: [
        'page' => 1,
    ], methods: ['GET'])]
    #[Route(path: '/page/{page}', name: 'admin_team_paginated', requirements: [
        'page' => '[1-9]\d*',
    ], methods: ['GET'])]
    public function listTeams(TeamRepository $repository, Request $request, $page): Response
    {
        $query = new TeamQuery();
        $query->setPage($page);
        $query->setCurrentUser($this->getUser());

        $form = $this->getToolbarForm($query);
        if ($this->handleSearch($form, $request)) {
            return $this->redirectToRoute('admin_team');
        }

        $entries = $repository->getPagerfantaForQuery($query);

        $table = new DataTable('admin_teams', $query);
        $table->setPagination($entries);
        $table->setSearchForm($form);
        $table->setPaginationRoute('admin_team_paginated');
        $table->setReloadEvents('kimai.teamUpdate');

        $table->addColumn('name', [
            'class' => 'alwaysVisible',
        ]);
        $table->addColumn('teamlead', [
            'class' => 'd-none badges',
            'orderBy' => false,
        ]);
        $table->addColumn('teamlead_avatar', [
            'title' => 'team.member',
            'translation_domain' => 'teams',
            'class' => 'd-none d-lg-table-cell avatars avatar-list avatar-list-stacked',
            'orderBy' => false,
        ]);
        $table->addColumn('user', [
            'class' => 'd-none badges',
            'orderBy' => false,
            'title' => 'user',
        ]);
        $table->addColumn('actions', [
            'class' => 'actions',
        ]);

        $page = new PageSetup('teams');
        $page->setActionName('teams');
        $page->setHelp('teams.html');
        $page->setDataTable($table);

        return $this->render('team/index.html.twig', [
            'page_setup' => $page,
            'dataTable' => $table,
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/create', name: 'admin_team_create', methods: ['GET', 'POST'])]
    #[IsGranted('create_team')]
    public function createTeam(Request $request): Response
    {
        return $this->renderEditScreen(new Team(''), $request, true);
    }

    #[Route(path: '/{id}/duplicate', name: 'team_duplicate', methods: ['GET', 'POST'])]
    #[IsGranted('create_team')]
    #[IsGranted('edit', 'team')]
    public function duplicateTeam(Team $team, Request $request): Response
    {
        $newTeam = clone $team;

        $i = 1;
        do {
            $newName = sprintf('%s (%s)', $team->getName(), $i++);
        } while (
            $this->repository->count([
                'name' => $newName,
            ]) > 0 && $i < 10
        );
        $newTeam->setName($newName);

        return $this->renderEditScreen($newTeam, $request, true);
    }

    #[Route(path: '/{id}/edit', name: 'admin_team_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'team')]
    public function editAction(Team $team, Request $request): Response
    {
        return $this->renderEditScreen($team, $request);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/edit_member', name: 'admin_team_member', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'team')]
    public function editMemberAction(Team $team, Request $request): Response
    {
        $editForm = $this->createForm(TeamEditForm::class, $team, [
            'action' => $this->generateUrl('admin_team_member', [
                'id' => $team->getId(),
            ]),
            'method' => 'POST',
        ]);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->repository->saveTeam($team);
                $this->flashSuccess('action.update.success');

                return $this->redirectToRoute('admin_team_edit', [
                    'id' => $team->getId(),
                ]);
            } catch (Exception $ex) {
                $this->flashUpdateException($ex);
            }
        }

        $page = new PageSetup('teams');
        $page->setHelp('teams.html');

        return $this->render('team/edit_member.html.twig', [
            'page_setup' => $page,
            'team' => $team,
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    private function renderEditScreen(Team $team, Request $request, bool $create = false): Response
    {
        $customerForm = null;
        $projectForm = null;

        if ($team->getId() === null) {
            $url = $this->generateUrl('admin_team_create');
        } else {
            $url = $this->generateUrl('admin_team_edit', [
                'id' => $team->getId(),
            ]);
        }

        $editForm = $this->createForm(TeamEditForm::class, $team, [
            'action' => $url,
            'method' => 'POST',
        ]);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->repository->saveTeam($team);
                $this->flashSuccess('action.update.success');

                if ($create) {
                    return $this->redirectToRouteAfterCreate('admin_team_edit', [
                        'id' => $team->getId(),
                    ]);
                }

                return $this->redirectToRoute('admin_team_edit', [
                    'id' => $team->getId(),
                ]);
            } catch (Exception $ex) {
                $this->handleFormUpdateException($ex, $editForm);
            }
        }

        if ($team->getId() !== null) {
            $customerForm = $this->createFormWithName('team_customer_form', FormType::class, $team)
                ->add('customers', CustomerType::class, [
                    'label' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder_for_user' => false,
                ]);

            $projectForm = $this->createFormWithName('team_project_form', FormType::class, $team)
                ->add('projects', ProjectType::class, [
                    'label' => false,
                    'multiple' => true,
                    'expanded' => true,
                    'query_builder_for_user' => false,
                ]);
        }

        $page = new PageSetup('teams');
        $page->setHelp('teams.html');

        return $this->render('team/edit.html.twig', [
            'page_setup' => $page,
            'team' => $team,
            'form' => $editForm->createView(),
            'customerForm' => $customerForm?->createView(),
            'projectForm' => $projectForm?->createView(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getToolbarForm(TeamQuery $query): FormInterface
    {
        return $this->createSearchForm(TeamToolbarForm::class, $query, [
            'action' => $this->generateUrl('admin_team', [
                'page' => $query->getPage(),
            ]),
        ]);
    }
}
