<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Configuration\SystemConfiguration;
use App\Crm\Application\Service\Project\ProjectDuplicationService;
use App\Crm\Application\Service\Project\ProjectService;
use App\Crm\Application\Service\Project\ProjectStatisticService;
use App\Crm\Application\Utils\Context;
use App\Crm\Application\Utils\DataTable;
use App\Crm\Application\Utils\PageSetup;
use App\Crm\Domain\Entity\Customer;
use App\Crm\Domain\Entity\MetaTableTypeInterface;
use App\Crm\Domain\Entity\Project;
use App\Crm\Domain\Entity\ProjectComment;
use App\Crm\Domain\Entity\ProjectRate;
use App\Crm\Domain\Entity\Team;
use App\Crm\Domain\Repository\ActivityRepository;
use App\Crm\Domain\Repository\ProjectRateRepository;
use App\Crm\Domain\Repository\ProjectRepository;
use App\Crm\Domain\Repository\Query\ActivityQuery;
use App\Crm\Domain\Repository\Query\ProjectQuery;
use App\Crm\Domain\Repository\Query\TeamQuery;
use App\Crm\Domain\Repository\Query\TimesheetQuery;
use App\Crm\Domain\Repository\TeamRepository;
use App\Crm\Transport\API\Export\Spreadsheet\EntityWithMetaFieldsExporter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\BinaryFileResponseWriter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\XlsxWriter;
use App\Crm\Transport\Event\ProjectDetailControllerEvent;
use App\Crm\Transport\Event\ProjectMetaDefinitionEvent;
use App\Crm\Transport\Event\ProjectMetaDisplayEvent;
use App\Crm\Transport\Form\ProjectCommentForm;
use App\Crm\Transport\Form\ProjectEditForm;
use App\Crm\Transport\Form\ProjectRateForm;
use App\Crm\Transport\Form\ProjectTeamPermissionForm;
use App\Crm\Transport\Form\Toolbar\ProjectToolbarForm;
use App\Crm\Transport\Form\Type\ProjectType;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller used to manage projects.
 */
#[Route(path: '/admin/project')]
final class ProjectController extends AbstractController
{
    public function __construct(
        private readonly ProjectRepository $repository,
        private readonly SystemConfiguration $configuration,
        private readonly EventDispatcherInterface $dispatcher,
        private readonly ProjectService $projectService
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    #[Route(path: '/', name: 'admin_project', defaults: [
        'page' => 1,
    ], methods: ['GET'])]
    #[Route(path: '/page/{page}', name: 'admin_project_paginated', requirements: [
        'page' => '[1-9]\d*',
    ], methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('listing', 'project')"))]
    public function indexAction(int $page, Request $request): Response
    {
        $query = new ProjectQuery();
        $query->setCurrentUser($this->getUser());
        $query->setPage($page);

        $form = $this->getToolbarForm($query);
        if ($this->handleSearch($form, $request)) {
            return $this->redirectToRoute('admin_project');
        }

        $entries = $this->repository->getPagerfantaForQuery($query);
        $metaColumns = $this->findMetaColumns($query);

        $table = new DataTable('project_admin', $query);
        $table->setPagination($entries);
        $table->setSearchForm($form);
        $table->setPaginationRoute('admin_project_paginated');
        $table->setReloadEvents('kimai.projectUpdate kimai.projectDelete kimai.projectTeamUpdate');

        $table->addColumn('name', [
            'class' => 'alwaysVisible',
        ]);
        $table->addColumn('customer', [
            'class' => 'd-none',
        ]);
        $table->addColumn('comment', [
            'class' => 'd-none',
            'title' => 'description',
        ]);
        $table->addColumn('number', [
            'class' => 'd-none w-min',
            'title' => 'project_number',
        ]);
        $table->addColumn('orderNumber', [
            'class' => 'd-none',
        ]);
        $table->addColumn('orderDate', [
            'class' => 'd-none',
        ]);
        $table->addColumn('project_start', [
            'class' => 'd-none',
        ]);
        $table->addColumn('project_end', [
            'class' => 'd-none',
        ]);

        foreach ($metaColumns as $metaColumn) {
            $table->addColumn('mf_' . $metaColumn->getName(), [
                'title' => $metaColumn->getLabel(),
                'class' => 'd-none',
                'orderBy' => false,
                'data' => $metaColumn,
            ]);
        }

        if ($this->isGranted('budget_money', 'project')) {
            $table->addColumn('budget', [
                'class' => 'd-none text-end w-min',
                'title' => 'budget',
            ]);
        }

        if ($this->isGranted('budget_time', 'project')) {
            $table->addColumn('timeBudget', [
                'class' => 'd-none text-end w-min',
                'title' => 'timeBudget',
            ]);
        }

        $table->addColumn('billable', [
            'class' => 'd-none text-center w-min',
            'orderBy' => false,
        ]);
        $table->addColumn('team', [
            'class' => 'text-center w-min',
            'orderBy' => false,
        ]);
        $table->addColumn('visible', [
            'class' => 'd-none text-center w-min',
        ]);
        $table->addColumn('actions', [
            'class' => 'actions',
        ]);

        $page = $this->createPageSetup();
        $page->setDataTable($table);
        $page->setActionName('projects');

        return $this->render('project/index.html.twig', [
            'page_setup' => $page,
            'dataTable' => $table,
            'metaColumns' => $metaColumns,
            'now' => $this->getDateTimeFactory()->createDateTime(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/permissions', name: 'admin_project_permissions', methods: ['GET', 'POST'])]
    #[IsGranted('permissions', 'project')]
    public function teamPermissions(Project $project, Request $request): Response
    {
        $form = $this->createForm(ProjectTeamPermissionForm::class, $project, [
            'action' => $this->generateUrl('admin_project_permissions', [
                'id' => $project->getId(),
            ]),
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->projectService->updateProject($project);
                $this->flashSuccess('action.update.success');

                if ($this->isGranted('view', $project)) {
                    return $this->redirectToRoute('project_details', [
                        'id' => $project->getId(),
                    ]);
                }

                return $this->redirectToRoute('admin_project');
            } catch (Exception $ex) {
                $this->flashUpdateException($ex);
            }
        }

        return $this->render('project/permissions.html.twig', [
            'page_setup' => $this->createPageSetup(),
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/create/{customer}', name: 'admin_project_create_with_customer', methods: ['GET', 'POST'])]
    #[IsGranted('create_project')]
    public function createWithCustomerAction(Request $request, Customer $customer): Response
    {
        return $this->createProject($request, $customer);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/create', name: 'admin_project_create', methods: ['GET', 'POST'])]
    #[IsGranted('create_project')]
    public function createAction(Request $request): Response
    {
        return $this->createProject($request, null);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/comment_delete/{token}', name: 'project_comment_delete', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('edit', subject.getProject()) and is_granted('comments', subject.getProject())"), 'comment')]
    public function deleteCommentAction(ProjectComment $comment, string $token, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $projectId = $comment->getProject()->getId();

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('project.delete_comment', $token))) {
            $this->flashError('action.csrf.error');

            return $this->redirectToRoute('project_details', [
                'id' => $projectId,
            ]);
        }

        $csrfTokenManager->refreshToken('project.delete_comment');

        try {
            $this->repository->deleteComment($comment);
        } catch (Exception $ex) {
            $this->flashDeleteException($ex);
        }

        return $this->redirectToRoute('project_details', [
            'id' => $projectId,
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/comment_add', name: 'project_comment_add', methods: ['POST'])]
    #[IsGranted('comments', 'project')]
    public function addCommentAction(Project $project, Request $request): Response
    {
        $comment = new ProjectComment($project);
        $form = $this->getCommentForm($comment);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->repository->saveComment($comment);
            } catch (Exception $ex) {
                $this->flashUpdateException($ex);
            }
        }

        return $this->redirectToRoute('project_details', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/comment_pin/{token}', name: 'project_comment_pin', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('edit', subject.getProject()) and is_granted('comments', subject.getProject())"), 'comment')]
    public function pinCommentAction(ProjectComment $comment, string $token, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $projectId = $comment->getProject()->getId();

        if (!$csrfTokenManager->isTokenValid(new CsrfToken('project.pin_comment', $token))) {
            $this->flashError('action.csrf.error');

            return $this->redirectToRoute('project_details', [
                'id' => $projectId,
            ]);
        }

        $csrfTokenManager->refreshToken('project.pin_comment');

        $comment->setPinned(!$comment->isPinned());

        try {
            $this->repository->saveComment($comment);
        } catch (Exception $ex) {
            $this->flashUpdateException($ex);
        }

        return $this->redirectToRoute('project_details', [
            'id' => $projectId,
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/create_team', name: 'project_team_create', methods: ['GET'])]
    #[IsGranted('create_team')]
    #[IsGranted('permissions', 'project')]
    public function createDefaultTeamAction(Project $project, TeamRepository $teamRepository): Response
    {
        $defaultTeam = $teamRepository->findOneBy([
            'name' => $project->getName(),
        ]);

        if ($defaultTeam === null) {
            $defaultTeam = new Team($project->getName());
        }

        $defaultTeam->addTeamlead($this->getUser());
        $defaultTeam->addProject($project);

        try {
            $teamRepository->saveTeam($defaultTeam);
        } catch (Exception $ex) {
            $this->flashUpdateException($ex);
        }

        return $this->redirectToRoute('project_details', [
            'id' => $project->getId(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/{id}/activities/{page}', name: 'project_activities', defaults: [
        'page' => 1,
    ], methods: ['GET', 'POST'])]
    #[IsGranted('view', 'project')]
    public function activitiesAction(Project $project, int $page, ActivityRepository $activityRepository): Response
    {
        $query = new ActivityQuery();
        $query->setCurrentUser($this->getUser());
        $query->setPage($page);
        $query->setPageSize(5);
        $query->addProject($project);
        $query->setExcludeGlobals(true);
        $query->setShowBoth();
        $query->addOrderGroup('visible', ActivityQuery::ORDER_DESC);
        $query->addOrderGroup('name', ActivityQuery::ORDER_ASC);

        $entries = $activityRepository->getPagerfantaForQuery($query);

        return $this->render('project/embed_activities.html.twig', [
            'project' => $project,
            'activities' => $entries,
            'page' => $page,
            'now' => $this->getDateTimeFactory()->createDateTime(),
        ]);
    }

    /**
     * @throws Exception
     */
    #[Route(path: '/{id}/details', name: 'project_details', methods: ['GET', 'POST'])]
    #[IsGranted('view', 'project')]
    public function detailsAction(
        Project $project,
        TeamRepository $teamRepository,
        ProjectRateRepository $rateRepository,
        ProjectStatisticService $statisticService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        $event = new ProjectMetaDefinitionEvent($project);
        $this->dispatcher->dispatch($event);

        $stats = null;
        $defaultTeam = null;
        $commentForm = null;
        $attachments = [];
        $comments = null;
        $teams = null;
        $rates = [];
        $now = $this->getDateTimeFactory()->createDateTime();

        $exportUrl = null;
        $invoiceUrl = null;
        if ($this->isGranted('create_export') && $project->getCustomer() !== null) {
            $exportUrl = $this->generateUrl('export', [
                'customers[]' => $project->getCustomer()->getId(),
                'projects[]' => $project->getId(),
                'daterange' => '',
                'exported' => TimesheetQuery::STATE_NOT_EXPORTED,
                'preview' => true,
                'billable' => true,
            ]);
        }
        if ($this->isGranted('view_invoice') && $project->getCustomer() !== null) {
            $invoiceUrl = $this->generateUrl('invoice', [
                'customers[]' => $project->getCustomer()->getId(),
                'projects[]' => $project->getId(),
                'daterange' => '',
                'exported' => TimesheetQuery::STATE_NOT_EXPORTED,
                'billable' => true,
            ]);
        }

        if ($this->isGranted('edit', $project)) {
            if ($this->isGranted('create_team')) {
                $defaultTeam = $teamRepository->findOneBy([
                    'name' => $project->getName(),
                ]);
            }
            $rates = $rateRepository->getRatesForProject($project);
        }

        if ($this->isGranted('budget', $project) || $this->isGranted('time', $project)) {
            $stats = $statisticService->getBudgetStatisticModel($project, $now);
        }

        if ($this->isGranted('comments', $project)) {
            $comments = $this->repository->getComments($project);
            $commentForm = $this->getCommentForm(new ProjectComment($project))->createView();
        }

        if ($this->isGranted('permissions', $project) || $this->isGranted('details', $project) || $this->isGranted('view_team')) {
            $query = new TeamQuery();
            $query->addProject($project);
            $teams = $teamRepository->getTeamsForQuery($query);
        }

        // additional boxes by plugins
        $event = new ProjectDetailControllerEvent($project);
        $this->dispatcher->dispatch($event);
        $boxes = $event->getController();

        $page = $this->createPageSetup();
        $page->setActionName('project');
        $page->setActionView('project_details');
        $page->setActionPayload([
            'project' => $project,
            'token' => $csrfTokenManager->getToken('project.duplicate'),
        ]);

        return $this->render('project/details.html.twig', [
            'page_setup' => $page,
            'project' => $project,
            'comments' => $comments,
            'commentForm' => $commentForm,
            'attachments' => $attachments,
            'stats' => $stats,
            'team' => $defaultTeam,
            'teams' => $teams,
            'rates' => $rates,
            'now' => $now,
            'boxes' => $boxes,
            'export_url' => $exportUrl,
            'invoice_url' => $invoiceUrl,
        ]);
    }

    #[Route(path: '/{id}/rate/{rate}', name: 'admin_project_rate_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'project')]
    public function editRateAction(Project $project, ProjectRate $rate, Request $request, ProjectRateRepository $repository): Response
    {
        return $this->rateFormAction($project, $rate, $request, $repository, $this->generateUrl('admin_project_rate_edit', [
            'id' => $project->getId(),
            'rate' => $rate->getId(),
        ]));
    }

    #[Route(path: '/{id}/rate', name: 'admin_project_rate_add', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'project')]
    public function addRateAction(Project $project, Request $request, ProjectRateRepository $repository): Response
    {
        $rate = new ProjectRate();
        $rate->setProject($project);

        return $this->rateFormAction($project, $rate, $request, $repository, $this->generateUrl('admin_project_rate_add', [
            'id' => $project->getId(),
        ]));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/edit', name: 'admin_project_edit', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'project')]
    public function editAction(Project $project, Request $request): Response
    {
        $editForm = $this->createEditForm($project);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->projectService->updateProject($project);
                $this->flashSuccess('action.update.success');

                if ($this->isGranted('view', $project)) {
                    return $this->redirectToRoute('project_details', [
                        'id' => $project->getId(),
                    ]);
                }

                return new Response();
            } catch (Exception $ex) {
                $this->flashUpdateException($ex);
            }
        }

        return $this->render('project/edit.html.twig', [
            'page_setup' => $this->createPageSetup(),
            'project' => $project,
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route(path: '/{id}/duplicate/{token}', name: 'admin_project_duplicate', methods: ['GET', 'POST'])]
    #[IsGranted('edit', 'project')]
    public function duplicateAction(Project $project, string $token, ProjectDuplicationService $projectDuplicationService, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if (!$csrfTokenManager->isTokenValid(new CsrfToken('project.duplicate', $token))) {
            $this->flashError('action.csrf.error');

            return $this->redirectToRoute('project_details', [
                'id' => $project->getId(),
            ]);
        }

        $csrfTokenManager->refreshToken('project.duplicate');

        $newProject = $projectDuplicationService->duplicate($project, $project->getName() . ' [COPY]');

        $this->flashSuccess('action.update.success');

        return $this->redirectToRoute('project_details', [
            'id' => $newProject->getId(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/delete', name: 'admin_project_delete', methods: ['GET', 'POST'])]
    #[IsGranted('delete', 'project')]
    public function deleteAction(Project $project, Request $request, ProjectStatisticService $statisticService): Response
    {
        $stats = $statisticService->getProjectStatistics($project);

        $deleteForm = $this->createFormBuilder(null, [
            'attr' => [
                'data-form-event' => 'kimai.projectDelete',
                'data-msg-success' => 'action.delete.success',
                'data-msg-error' => 'action.delete.error',
            ],
        ])
            ->add('project', ProjectType::class, [
                'ignore_project' => $project,
                'customers' => $project->getCustomer(),
                'query_builder_for_user' => true,
                'required' => false,
            ])
            ->setAction($this->generateUrl('admin_project_delete', [
                'id' => $project->getId(),
            ]))
            ->setMethod('POST')
            ->getForm();

        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $this->repository->deleteProject($project, $deleteForm->get('project')->getData());
                $this->flashSuccess('action.delete.success');
            } catch (Exception $ex) {
                $this->flashDeleteException($ex);
            }

            return $this->redirectToRoute('admin_project');
        }

        return $this->render('project/delete.html.twig', [
            'page_setup' => $this->createPageSetup(),
            'project' => $project,
            'stats' => $stats,
            'form' => $deleteForm->createView(),
        ]);
    }

    #[Route(path: '/export', name: 'project_export', methods: ['GET'])]
    #[IsGranted(new Expression("is_granted('listing', 'project')"))]
    public function exportAction(Request $request, EntityWithMetaFieldsExporter $exporter): Response
    {
        $query = new ProjectQuery();
        $query->setCurrentUser($this->getUser());

        $form = $this->getToolbarForm($query);
        $form->setData($query);
        $form->submit($request->query->all(), false);

        if (!$form->isValid()) {
            $query->resetByFormError($form->getErrors());
        }

        $entries = $this->repository->getProjectsForQuery($query);

        $spreadsheet = $exporter->export(
            Project::class,
            $entries,
            new ProjectMetaDisplayEvent($query, ProjectMetaDisplayEvent::EXPORT)
        );
        $writer = new BinaryFileResponseWriter(new XlsxWriter(), 'kimai-projects');

        return $writer->getFileResponse($spreadsheet);
    }

    /**
     * @return MetaTableTypeInterface[]
     */
    private function findMetaColumns(ProjectQuery $query): array
    {
        $event = new ProjectMetaDisplayEvent($query, ProjectMetaDisplayEvent::PROJECT);
        $this->dispatcher->dispatch($event);

        return $event->getFields();
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function createProject(Request $request, ?Customer $customer = null): Response
    {
        $project = $this->projectService->createNewProject($customer);

        $editForm = $this->createEditForm($project);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            try {
                $this->projectService->saveNewProject($project, new Context($this->getUser()));
                $this->flashSuccess('action.update.success');

                return $this->redirectToRouteAfterCreate('project_details', [
                    'id' => $project->getId(),
                ]);
            } catch (Exception $ex) {
                $this->handleFormUpdateException($ex, $editForm);
            }
        }

        return $this->render('project/edit.html.twig', [
            'page_setup' => $this->createPageSetup(),
            'project' => $project,
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function rateFormAction(Project $project, ProjectRate $rate, Request $request, ProjectRateRepository $repository, string $formUrl): Response
    {
        $form = $this->createForm(ProjectRateForm::class, $rate, [
            'action' => $formUrl,
            'method' => 'POST',
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $repository->saveRate($rate);
                $this->flashSuccess('action.update.success');

                return $this->redirectToRoute('project_details', [
                    'id' => $project->getId(),
                ]);
            } catch (Exception $ex) {
                $this->flashUpdateException($ex);
            }
        }

        return $this->render('project/rates.html.twig', [
            'page_setup' => $this->createPageSetup(),
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function getToolbarForm(ProjectQuery $query): FormInterface
    {
        return $this->createSearchForm(ProjectToolbarForm::class, $query, [
            'action' => $this->generateUrl('admin_project', [
                'page' => $query->getPage(),
            ]),
        ]);
    }

    private function getCommentForm(ProjectComment $comment): FormInterface
    {
        if ($comment->getId() === null) {
            $comment->setCreatedBy($this->getUser());
        }

        return $this->createForm(ProjectCommentForm::class, $comment, [
            'action' => $this->generateUrl('project_comment_add', [
                'id' => $comment->getProject()->getId(),
            ]),
            'method' => 'POST',
        ]);
    }

    /**
     * @throws Exception
     */
    private function createEditForm(Project $project): FormInterface
    {
        $event = new ProjectMetaDefinitionEvent($project);
        $this->dispatcher->dispatch($event);

        $currency = $this->configuration->getCustomerDefaultCurrency();
        $url = $this->generateUrl('admin_project_create');

        if ($project->getId() !== null) {
            $url = $this->generateUrl('admin_project_edit', [
                'id' => $project->getId(),
            ]);
            $currency = $project->getCustomer()->getCurrency();
        }

        return $this->createForm(ProjectEditForm::class, $project, [
            'action' => $url,
            'method' => 'POST',
            'currency' => $currency,
            'timezone' => $this->getDateTimeFactory()->getTimezone()->getName(),
            'include_budget' => $this->isGranted('budget', $project),
            'include_time' => $this->isGranted('time', $project),
        ]);
    }

    private function createPageSetup(): PageSetup
    {
        $page = new PageSetup('projects');
        $page->setHelp('project.html');

        return $page;
    }
}
