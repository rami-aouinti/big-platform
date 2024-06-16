<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Root;

use App\General\Application\DTO\Interfaces\RestDtoInterface;
use App\General\Transport\Rest\Traits\Methods\CreateMethod;
use App\Role\Domain\Enum\Role;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

/**
 * Trait CreateAction
 *
 * Trait to add 'createAction' for REST controllers for 'ROLE_ROOT' users.
 *
 * @see \App\General\Transport\Rest\Traits\Methods\CreateMethod for detailed documents.
 *
 * @package App\General
 */
trait CreateAction
{
    use CreateMethod;

    /**
     * Create entity, accessible only for 'ROLE_ROOT' users.
     *
     * @throws Throwable
     */
    #[Route(
        path: '',
        methods: [Request::METHOD_POST],
    )]
    #[IsGranted(Role::ROOT->value)]
    #[OA\RequestBody(
        request: 'body',
        description: 'object',
        content: new JsonContent(
            type: 'object',
            example: [
                'param' => 'value',
            ],
        ),
    )]
    #[OA\Response(
        response: 201,
        description: 'created',
        content: new JsonContent(
            type: 'object',
            example: [],
        ),
    )]
    #[OA\Response(
        response: 403,
        description: 'Access denied',
        content: new JsonContent(
            properties: [
                new Property(property: 'code', description: 'Error code', type: 'integer'),
                new Property(property: 'message', description: 'Error description', type: 'string'),
            ],
            type: 'object',
            example: [
                'code' => 403,
                'message' => 'Access denied',
            ],
        ),
    )]
    public function createAction(Request $request, RestDtoInterface $restDto): Response
    {
        return $this->createMethod($request, $restDto);
    }
}
