<?php

declare(strict_types=1);

namespace App\Resume\Transport\Controller\API;

use App\Resume\Infrastructure\Repository\SkillRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
* Class SkillController
 * @package App\Resume\Transport\Controller\API
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class SkillController extends AbstractController
{
    /**
     * @Route("", methods={"GET"}, name="api_skills_collection_get")
     */
    public function collection(
        SkillRepository $skillRepository,
        SerializerInterface $serializer
    ): JsonResponse {
        return new JsonResponse(
            $serializer->serialize($skillRepository->findAll(), 'json', [
                'groups' => 'get',
            ]),
            200,
            [],
            true
        );
    }
}
