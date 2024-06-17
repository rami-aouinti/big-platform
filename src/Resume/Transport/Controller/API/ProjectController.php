<?php

declare(strict_types=1);

namespace App\Resume\Transport\Controller\API;

use App\Resume\Infrastructure\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Class ProjectController
 *
 * @package App\Resume\Transport\Controller\API
 * @author  Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
#[Route('/api/projects')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'api_projects_collection_get', methods: ['GET'])]
    public function collection(
        ProjectRepository $projectRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        return new JsonResponse(
            $serializer->serialize($projectRepository->findAll(), 'json', [
                'groups' => 'get',
            ]),
            200,
            [],
            true
        );
    }
}
