<?php

declare(strict_types=1);

namespace App\Admin\Crm\Controller;

use App\Constants;
use App\Crm\Application\Utils\PageSetup;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * @package App\Admin\Crm\Controller
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route(path: '/about')]
final class AboutController extends AbstractController
{
    public function __construct(
        private readonly string $projectDirectory
    ) {
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route(path: '', name: 'about', methods: ['GET'])]
    public function license(): Response
    {
        $filename = $this->projectDirectory . '/LICENSE';

        try {
            $license = file_get_contents($filename);
        } catch (Exception $ex) {
            $this->logException($ex);
            $license = false;
        }

        if ($license === false) {
            $license = 'Failed reading license file: ' . $filename . '. ' .
                'Check this instead: ' . Constants::GITHUB . 'blob/main/LICENSE';
        }

        return $this->render('about/license.html.twig', [
            'page_setup' => new PageSetup('about.title'),
            'license' => $license,
        ]);
    }
}
