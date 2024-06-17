<?php

declare(strict_types=1);

namespace App\Resume\Transport\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 * @package App\Controller\FrontOffice
 * @Route("/", name="home")
 */
class HomeController extends AbstractController
{
    public function __invoke(): Response
    {
        return $this->render('front_office/home.html.twig');
    }
}
