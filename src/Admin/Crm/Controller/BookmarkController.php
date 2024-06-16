<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Crm\Domain\Repository\BookmarkRepository;
use App\Crm\Domain\Entity\Bookmark;
use App\Crm\Application\Utils\ProfileManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\RuntimeException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

use function count;

/**
* Class BookmarkController
 * @package App\Admin\Crm\Controller
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/bookmark')]
final class BookmarkController extends AbstractController
{
    public const string DATATABLE_TOKEN = 'datatable_update';
    public const string PARAM_TOKEN_NAME = 'datatable_token';
    public const string PARAM_DATATABLE = 'datatable_name';
    public const string PARAM_PROFILE = 'datatable_profile';

    public function __construct(
        private readonly BookmarkRepository $bookmarkRepository,
        private readonly ProfileManager $profileManager
    ) {
    }

    #[Route(path: '/datatable/profile', name: 'bookmark_profile', methods: ['POST'])]
    public function datatableProfile(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        if (!$request->request->has(self::PARAM_TOKEN_NAME) || !$request->request->has(self::PARAM_PROFILE)) {
            throw $this->createNotFoundException('Missing CSRF Token');
        }

        if (!$csrfTokenManager->isTokenValid(new CsrfToken(self::DATATABLE_TOKEN, $request->request->get(self::PARAM_TOKEN_NAME)))) {
            throw $this->createAccessDeniedException('Invalid CSRF Token');
        }

        $profile = $request->request->get(self::PARAM_PROFILE);
        if (!$this->profileManager->isValidProfile($profile)) {
            throw $this->createNotFoundException('Invalid profile given');
        }

        $this->profileManager->setProfile($request->getSession(), $profile);
        $csrfTokenManager->refreshToken(self::DATATABLE_TOKEN);

        return new Response();
    }

    #[Route(path: '/datatable/save', name: 'bookmark_save_datatable', methods: ['POST'])]
    public function datatableSave(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        [$profile, $datatableName] = $this->extracted($request, $csrfTokenManager);

        if (empty($datatableName) || mb_strlen($datatableName) > 50) {
            throw new RuntimeException('Invalid datatable name');
        }

        $enabled = [];
        foreach ($request->request->all() as $name => $value) {
            if ($value !== 'on' || mb_strlen($name) > 30) {
                continue;
            }
            $enabled[$name] = true;
        }

        if (count($enabled) > 50) {
            throw new RuntimeException(sprintf('Too many columns provided, expected maximum 50, received %s.', count($enabled)));
        }

        $user = $this->getUser();

        $bookmark = $this->bookmarkRepository->findBookmark($user, Bookmark::COLUMN_VISIBILITY, $datatableName);
        if ($bookmark === null) {
            $bookmark = new Bookmark();
            $bookmark->setUser($user);
            $bookmark->setType(Bookmark::COLUMN_VISIBILITY);
            $bookmark->setName($datatableName);
        }
        $bookmark->setContent($enabled);

        $this->bookmarkRepository->saveBookmark($bookmark);
        $this->profileManager->setProfile($request->getSession(), $profile);
        $csrfTokenManager->refreshToken(self::DATATABLE_TOKEN);

        return new Response();
    }

    #[Route(path: '/datatable/delete', name: 'bookmark_delete', methods: ['POST'])]
    public function datatableDelete(Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        [$profile, $datatableName] = $this->extracted($request, $csrfTokenManager);

        $bookmark = $this->bookmarkRepository->findBookmark($this->getUser(), Bookmark::COLUMN_VISIBILITY, $datatableName);
        if ($bookmark !== null) {
            $this->bookmarkRepository->deleteBookmark($bookmark);
        }

        $csrfTokenManager->refreshToken(self::DATATABLE_TOKEN);

        return new Response();
    }/**
* @param Request $request
 * @param CsrfTokenManagerInterface $csrfTokenManager
 *
 * @return array
 */
    public function extracted(Request $request, CsrfTokenManagerInterface $csrfTokenManager): array
    {
        if (
            !$request->request->has(self::PARAM_TOKEN_NAME) || !$request->request->has(
                self::PARAM_DATATABLE
            ) || !$request->request->has(self::PARAM_PROFILE)
        ) {
            throw $this->createNotFoundException('Missing data: csrf token, datatable name or profile');
        }

        if (
            !$csrfTokenManager->isTokenValid(
                new CsrfToken(self::DATATABLE_TOKEN, $request->request->get(self::PARAM_TOKEN_NAME))
            )
        ) {
            throw $this->createAccessDeniedException('Invalid CSRF Token');
        }

        $profile = $request->request->get(self::PARAM_PROFILE);
        if (!$this->profileManager->isValidProfile($profile)) {
            throw $this->createNotFoundException('Invalid profile given');
        }

        $datatableName = $request->request->get(self::PARAM_DATATABLE);
        $datatableName = $this->profileManager->getDatatableName($datatableName, $profile);

        return [$profile, $datatableName];
    }
}
