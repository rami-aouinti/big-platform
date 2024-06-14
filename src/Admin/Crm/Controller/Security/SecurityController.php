<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Security;

use App\Admin\Crm\Controller\AbstractController;
use App\Configuration\SamlConfigurationInterface;
use RuntimeException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @package App\Admin\Crm\Controller\Security
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
final class SecurityController extends AbstractController
{
    public function __construct(
        private readonly CsrfTokenManagerInterface $tokenManager,
        private readonly SamlConfigurationInterface $samlConfiguration
    ) {
    }

    #[Route(path: '/login', name: 'login', methods: ['GET', 'POST'])]
    public function loginAction(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        $csrfToken = $this->tokenManager->getToken('authenticate')->getValue();

        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED') && $this->getUser()->isInternalUser()) {
            return $this->render('security/unlock.html.twig', [
                'error' => $error,
                'csrf_token' => $csrfToken,
            ]);
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken,
            'saml_config' => $this->samlConfiguration,
        ]);
    }

    #[Route(path: '/login_check', name: 'security_check', methods: ['POST'])]
    public function checkAction(): Response
    {
        throw new RuntimeException('You must configure the check path to be handled by the firewall using form_login in your security firewall configuration.');
    }

    #[Route(path: '/logout', name: 'logout', methods: ['GET', 'POST'])]
    public function logoutAction(): Response
    {
        throw new RuntimeException('You must activate the logout in your security firewall configuration.');
    }
}
