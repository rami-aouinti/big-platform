<?php

declare(strict_types=1);

namespace App\Resume\Transport\Controller\API;

use App\Resume\Infrastructure\Repository\ExperienceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ExperienceController
 *
 * @package App\Resume\Transport\Controller\API
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route('/api/experiences')]
class ExperienceController extends AbstractController
{
    #[Route('/', name: 'api_experiences_collection_get', methods: ['GET'])]
    public function collection(ExperienceRepository $experienceRepository): JsonResponse
    {
        return $this->json($experienceRepository->findAll());
    }
}
