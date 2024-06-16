<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller\Auth;

use App\Admin\Auth\Saml\SamlAuthFactory;
use App\Configuration\SamlConfigurationInterface;
use OneLogin\Saml2\Error;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\SecurityRequestAttributes;

use function is_object;

/**
 * @package App\Admin\Crm\Controller\Auth
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/saml')]
final class SamlController extends AbstractController
{
    public function __construct(
        private readonly SamlAuthFactory $authFactory,
        private readonly SamlConfigurationInterface $samlConfiguration
    ) {
    }

    /**
     * @throws Error
     */
    #[Route(path: '/login', name: 'saml_login')]
    public function loginAction(Request $request): Response
    {
        if (!$this->samlConfiguration->isActivated()) {
            throw $this->createNotFoundException('SAML deactivated');
        }

        $session = $request->getSession();
        $authErrorKey = SecurityRequestAttributes::AUTHENTICATION_ERROR;

        $error = null;

        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif ($session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        }

        if ($error) {
            if (is_object($error) && method_exists($error, 'getMessage')) {
                $error = $error->getMessage();
            }

            throw new RuntimeException($error);
        }

        // this does set headers and exit as $stay is not set to true
        $redirectTarget = $session->get('_security.main.target_path');
        if ($redirectTarget === null || $redirectTarget === '') {
            $redirectTarget = $this->generateUrl('homepage', [], UrlGeneratorInterface::ABSOLUTE_URL);
        }

        $url = $this->authFactory->create()->login($redirectTarget);

        if ($url === null) {
            throw new RuntimeException('SAML login failed');
        }

        // this line is not (yet) reached, as the previous call will exit
        return $this->redirect($url);
    }

    /**
     * @throws Error
     */
    #[Route(path: '/metadata', name: 'saml_metadata')]
    public function metadataAction(): Response
    {
        if (!$this->samlConfiguration->isActivated()) {
            throw $this->createNotFoundException('SAML deactivated');
        }

        $metadata = $this->authFactory->create()->getSettings()->getSPMetadata();

        $response = new Response($metadata);
        $response->headers->set('Content-Type', 'xml');

        return $response;
    }

    #[Route(path: '/acs', name: 'saml_acs')]
    public function assertionConsumerServiceAction(): Response
    {
        if (!$this->samlConfiguration->isActivated()) {
            throw $this->createNotFoundException('SAML deactivated');
        }

        throw new RuntimeException('You must configure the check path in your firewall.');
    }

    #[Route(path: '/logout', name: 'saml_logout')]
    public function logoutAction(): Response
    {
        if (!$this->samlConfiguration->isActivated()) {
            throw $this->createNotFoundException('SAML deactivated');
        }

        throw new RuntimeException('You must configure the logout path in your firewall.');
    }
}
