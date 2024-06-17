<?php

declare(strict_types=1);

namespace App\Resume\Transport\Controller\API;

use App\Resume\Infrastructure\Repository\ReferenceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
* Class ReferenceController
 * @package App\Resume\Transport\Controller\API
 * @author Rami Aouinti <rami.aouinti@tkdeutschland.de>
 */
class ReferenceController
{
    /**
     * @Route("", methods={"GET"}, name="api_references_collection_get")
     */
    public function collection(ReferenceRepository $referenceRepository, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse(
            $serializer->serialize($referenceRepository->findAll(), 'json', [
                'groups' => 'get',
            ]),
            200,
            [],
            true
        );
    }
}
