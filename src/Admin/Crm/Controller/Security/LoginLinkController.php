<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Security;

use App\Admin\Crm\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/auth/link')]
/**
 * @CloudRequired
 */
final class LoginLinkController extends AbstractController
{
    #[Route(path: '/check', name: 'link_login_check', methods: ['GET'])]
    public function check(): Response
    {
        return new Response();
    }
}
