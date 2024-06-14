<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Configuration\SystemConfiguration;
use App\Crm\Domain\Repository\Query\UserQuery;
use App\Crm\Domain\Repository\TimesheetRepository;
use App\Crm\Domain\Repository\UserRepository;
use App\Crm\Transport\API\Export\Spreadsheet\Extractor\ExtractorException;
use App\Crm\Transport\API\Export\Spreadsheet\UserExporter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\BinaryFileResponseWriter;
use App\Crm\Transport\API\Export\Spreadsheet\Writer\XlsxWriter;
use App\Crm\Transport\Event\PrepareUserEvent;
use App\Crm\Transport\Event\UserPreferenceDisplayEvent;
use App\Crm\Transport\Form\Toolbar\UserToolbarForm;
use App\Crm\Transport\Form\UserCreateType;
use App\General\Domain\Enum\Language;
use App\User\Domain\Entity\User;
use App\User\Transport\Form\Type\Console\UserType;
use App\User\UserService;
use App\Utils\DataTable;
use App\Utils\PageSetup;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller used to manage users in the admin part of the site.
 */
#[Route(path: '/admin/user')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[IsGranted('view_user')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly UserRepository $repository,
        private readonly EventDispatcherInterface $dispatcher
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/', name: 'admin_user', defaults: [
        'page' => 1,
    ], methods: ['GET'])]
    #[Route(path: '/page/{page}', name: 'admin_user_paginated', requirements: [
        'page' => '[1-9]\d*',
    ], methods: ['GET'])]
    public function indexAction(int $page, Request $request): Response
    {
        $query = new UserQuery();
        $query->setCurrentUser($this->getUser());
        $query->setPage($page);

        $form = $this->getToolbarForm($query);
        if ($this->handleSearch($form, $request)) {
            return $this->redirectToRoute('admin_user');
        }

        $entries = $this->repository->getPagerfantaForQuery($query);

        $event = new UserPreferenceDisplayEvent(UserPreferenceDisplayEvent::USERS);
        $this->dispatcher->dispatch($event);

        $table = new DataTable('user_admin', $query);
        $table->setPagination($entries);
        $table->setSearchForm($form);
        $table->setPaginationRoute('admin_user_paginated');
        $table->setReloadEvents('kimai.userUpdate');

        $table->addColumn('avatar', [
            'class' => 'alwaysVisible w-avatar',
            'title' => null,
            'orderBy' => false,
        ]);
        //$table->addColumn('user', ['class' => 'alwaysVisible', 'orderBy' => 'user']);
        $table->addColumn('username', [
            'class' => 'alwaysVisible',
        ]);
        $table->addColumn('alias', [
            'class' => 'd-none',
        ]);
        $table->addColumn('account_number', [
            'class' => 'd-none',
        ]);
        $table->addColumn('title', [
            'class' => 'd-none',
        ]);
        $table->addColumn('email', [
            'class' => 'd-none',
            'orderBy' => false,
        ]);
        $table->addColumn('lastLogin', [
            'class' => 'd-none',
            'orderBy' => false,
        ]);
        $table->addColumn('roles', [
            'class' => 'd-none',
            'orderBy' => false,
        ]);
        $table->addColumn('system_account', [
            'class' => 'd-none',
            'orderBy' => 'systemAccount',
        ]);

        foreach ($event->getPreferences() as $userPreference) {
            $table->addColumn('mf_' . $userPreference->getName(), [
                'title' => $userPreference->getLabel(),
                'class' => 'd-none',
                'orderBy' => false,
                'translation_domain' => 'messages',
                'data' => $userPreference,
            ]);
        }

        $table->addColumn('team', [
            'class' => 'text-center w-min',
            'orderBy' => false,
        ]);
        $table->addColumn('active', [
            'class' => 'd-none w-min',
            'orderBy' => false,
        ]);
        $table->addColumn('actions', [
            'class' => 'actions',
        ]);

        $page = new PageSetup('users');
        $page->setHelp('users.html');
        $page->setActionName('users');
        $page->setDataTable($table);

        return $this->render('user/index.html.twig', [
            'page_setup' => $page,
            'dataTable' => $table,
            'preferences' => $event->getPreferences(),
        ]);
    }

    /**
     * @throws OptimisticLockException
     * @throws NotFoundExceptionInterface
     * @throws ORMException
     * @throws ContainerExceptionInterface
     */
    #[Route(path: '/create', name: 'admin_user_create', methods: ['GET', 'POST'])]
    #[IsGranted('create_user')]
    public function createAction(Request $request, SystemConfiguration $config, UserRepository $userRepository, EventDispatcherInterface $dispatcher): Response
    {
        $user = $this->createNewDefaultUser($config);
        $editForm = $this->getCreateUserForm($user);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $password = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
            $user->setPlainPassword($password);

            $userRepository->saveUser($user);
            $this->flashSuccess('action.update.success');

            try {
                $event = new PrepareUserEvent($user, false);
                $dispatcher->dispatch($event);
                $userRepository->saveUser($user);
            } catch (Exception $ex) {
                // it should be no problem, if creating default user preferences fails
            }

            return $this->redirectToRouteAfterCreate('user_profile_edit', [
                'username' => $user->getUserIdentifier(),
            ]);
        }

        $page = new PageSetup('users');
        $page->setHelp('users.html');

        return $this->render('user/create.html.twig', [
            'page_setup' => $page,
            'user' => $user,
            'form' => $editForm->createView(),
        ]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '/{id}/delete', name: 'admin_user_delete', methods: ['GET', 'POST'])]
    #[IsGranted('delete', 'userToDelete')]
    public function deleteAction(User $userToDelete, Request $request, TimesheetRepository $repository, UserService $userService): Response
    {
        // $userToDelete MUST not be called $user, as $user is always the current user!
        $stats = $repository->getUserStatistics($userToDelete);

        $deleteForm = $this->createFormBuilder(null, [
            'attr' => [
                'data-form-event' => 'kimai.userUpdate kimai.userDelete',
                'data-msg-success' => 'action.delete.success',
                'data-msg-error' => 'action.delete.error',
            ],
        ])
            ->add('user', UserType::class, [
                'ignore_users' => [$userToDelete],
                'required' => false,
            ])
            ->setAction($this->generateUrl('admin_user_delete', [
                'id' => $userToDelete->getId(),
            ]))
            ->setMethod('POST')
            ->getForm();

        $deleteForm->handleRequest($request);

        if ($deleteForm->isSubmitted() && $deleteForm->isValid()) {
            try {
                $userService->deleteUser($userToDelete, $deleteForm->get('user')->getData());
                $this->flashSuccess('action.delete.success');
            } catch (Exception $ex) {
                $this->flashDeleteException($ex);
            }

            return $this->redirectToRoute('admin_user');
        }

        $page = new PageSetup('users');
        $page->setHelp('users.html');

        return $this->render('user/delete.html.twig', [
            'page_setup' => $page,
            'user' => $userToDelete,
            'stats' => $stats,
            'form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @throws NotFoundExceptionInterface
     * @throws ExtractorException
     * @throws ContainerExceptionInterface
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    #[Route(path: '/export', name: 'user_export', methods: ['GET'])]
    #[IsGranted('view_user')]
    public function exportAction(Request $request, UserExporter $exporter): Response
    {
        $query = new UserQuery();
        $query->setCurrentUser($this->getUser());

        $form = $this->getToolbarForm($query);
        $form->setData($query);
        $form->submit($request->query->all(), false);

        if (!$form->isValid()) {
            $query->resetByFormError($form->getErrors());
        }

        $entries = $this->repository->getUsersForQuery($query);

        $spreadsheet = $exporter->export(
            $entries,
            new UserPreferenceDisplayEvent(UserPreferenceDisplayEvent::EXPORT)
        );
        $writer = new BinaryFileResponseWriter(new XlsxWriter(), 'kimai-users');

        return $writer->getFileResponse($spreadsheet);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function getToolbarForm(UserQuery $query): FormInterface
    {
        return $this->createSearchForm(UserToolbarForm::class, $query, [
            'action' => $this->generateUrl('admin_user', [
                'page' => $query->getPage(),
            ]),
        ]);
    }

    private function createNewDefaultUser(SystemConfiguration $config): User
    {
        $user = new User();
        $user->setEnabled(true);
        $user->setRoles([User::DEFAULT_ROLE]);
        $user->setTimezone($config->getUserDefaultTimezone());
        $user->setLanguage(Language::EN);

        return $user;
    }

    private function getCreateUserForm(User $user): FormInterface
    {
        return $this->createForm(UserCreateType::class, $user, [
            'action' => $this->generateUrl('admin_user_create'),
            'method' => 'POST',
            'include_active_flag' => true,
            'include_preferences' => true,
            'include_supervisor' => $this->isGranted('supervisor_other_profile'),
            'include_teams' => $this->isGranted('teams_other_profile'),
            'include_roles' => $this->isGranted('roles_other_profile'),
        ]);
    }
}
